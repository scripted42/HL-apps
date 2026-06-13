<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Customer;
use App\Models\Product;
use App\Helpers\DiscountCalculator;
use App\Helpers\BonusCalculator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionCreator extends Component
{
    public $isEditMode = false;
    public $transactionId = null;

    // Header properties
    public $nomor_bon = '';
    public $customer_id = '';
    public $tanggal = '';
    public $tanggal_pelunasan = '';
    public $status = 'Piutang';
    public $is_bonus = false;
    public $bonuses_claimed = 0;
    public $ongkir = 0;
    public $deskripsi = '';

    // Items list
    public $items = [];

    // Helper caches/stats
    public $customerStats = null;
    public $customerDiscountLm = [];
    public $customerDiscountBr = [];

    // List of available customers and products for selects
    public $customersList = [];
    public $productsList = [];

    protected $rules = [
        'nomor_bon' => 'required|string|max:255',
        'customer_id' => 'required|exists:customers,id',
        'tanggal' => 'required|date',
        'tanggal_pelunasan' => 'nullable|date|required_if:status,Lunas',
        'status' => 'required|in:Piutang,Lunas',
        'is_bonus' => 'boolean',
        'bonuses_claimed' => 'required|integer|min:0',
        'ongkir' => 'required|numeric|min:0',
        'deskripsi' => 'nullable|string',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|numeric|min:1',
        'items.*.harga_base' => 'required|numeric|min:0',
    ];

    public function mount(Transaction $transaction = null)
    {
        $this->customersList = Customer::orderBy('name', 'asc')->get();
        $this->productsList = Product::orderBy('name', 'asc')->get();

        if ($transaction && $transaction->exists) {
            $this->isEditMode = true;
            $this->transactionId = $transaction->id;
            $this->nomor_bon = $transaction->nomor_bon;
            $this->customer_id = $transaction->customer_id;
            $this->tanggal = $transaction->tanggal ? $transaction->tanggal->format('Y-m-d') : '';
            $this->tanggal_pelunasan = $transaction->tanggal_pelunasan ? $transaction->tanggal_pelunasan->format('Y-m-d') : '';
            $this->status = $transaction->status;
            $this->is_bonus = (bool) $transaction->is_bonus;
            $this->bonuses_claimed = $transaction->bonuses_claimed;
            $this->ongkir = (float) $transaction->ongkir;
            $this->deskripsi = $transaction->deskripsi;

            // Load items
            foreach ($transaction->items as $item) {
                $this->items[] = [
                    'key' => uniqid(),
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'product_type' => $item->product_type,
                    'harga_modal' => (float) $item->harga_modal,
                    'harga_base' => (float) $item->harga_base,
                    'discount_steps' => $item->discount_steps ?? [],
                    'discount_steps_input' => is_array($item->discount_steps) ? implode(', ', $item->discount_steps) : '',
                    'quantity' => (float) $item->quantity,
                    'discounted_unit_price' => (float) $item->discounted_unit_price,
                    'line_omzet' => (float) $item->line_omzet,
                    'line_laba' => (float) $item->line_laba,
                ];
            }

            $this->loadCustomerData();
        } else {
            $this->isEditMode = false;
            $this->tanggal = now()->format('Y-m-d');
            $this->status = 'Piutang';
            $this->is_bonus = false;
            $this->bonuses_claimed = 0;
            $this->ongkir = 0;
            $this->generateNomorBon();
            
            // Add one empty row
            $this->addItem();
        }
    }

    public function generateNomorBon()
    {
        $dateStr = now()->format('Ymd');
        $prefix = 'BON-' . $dateStr . '-';
        
        $lastTransaction = Transaction::where('nomor_bon', 'like', $prefix . '%')
            ->orderBy('nomor_bon', 'desc')
            ->first();
            
        if ($lastTransaction) {
            $lastNum = substr($lastTransaction->nomor_bon, -4);
            $nextNum = str_pad((int)$lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }
        
        $this->nomor_bon = $prefix . $nextNum;
    }

    public function updatedCustomerId()
    {
        $this->loadCustomerData();
        $this->reapplyCustomerDiscounts();
    }

    public function updatedIsBonus()
    {
        if ($this->is_bonus) {
            if ($this->bonuses_claimed == 0) {
                $this->bonuses_claimed = 1;
            }
        } else {
            $this->bonuses_claimed = 0;
        }
        $this->recalculateAll();
    }

    public function loadCustomerData()
    {
        if (empty($this->customer_id)) {
            $this->customerStats = null;
            $this->customerDiscountLm = [];
            $this->customerDiscountBr = [];
            return;
        }

        $customer = Customer::withTrashed()->find($this->customer_id);
        if ($customer) {
            $this->customerStats = BonusCalculator::getStats($customer);
            $this->customerDiscountLm = $customer->discount_lm ?? [];
            $this->customerDiscountBr = $customer->discount_br ?? [];
        }
    }

    public function reapplyCustomerDiscounts()
    {
        foreach ($this->items as $index => $item) {
            if (!empty($item['product_id'])) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $discountSteps = ($product->type === 'LM') ? $this->customerDiscountLm : $this->customerDiscountBr;
                    $this->items[$index]['discount_steps'] = $discountSteps;
                    $this->items[$index]['discount_steps_input'] = implode(', ', $discountSteps);
                }
            }
        }
        $this->recalculateAll();
    }

    public function addItem()
    {
        $this->items[] = [
            'key' => uniqid(),
            'id' => null,
            'product_id' => '',
            'product_name' => '',
            'product_type' => '',
            'harga_modal' => 0.00,
            'harga_base' => 0.00,
            'discount_steps' => [],
            'discount_steps_input' => '',
            'quantity' => 1,
            'discounted_unit_price' => 0.00,
            'line_omzet' => 0.00,
            'line_laba' => 0.00,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->recalculateAll();
    }

    public function updated($name, $value)
    {
        if (str_starts_with($name, 'items.')) {
            $parts = explode('.', $name);
            if (count($parts) >= 3) {
                $index = (int) $parts[1];
                $property = $parts[2];

                if ($property === 'product_id') {
                    $productId = $value;
                    if (!empty($productId)) {
                        $product = Product::find($productId);
                        if ($product) {
                            $this->items[$index]['product_name'] = $product->name;
                            $this->items[$index]['product_type'] = $product->type;
                            $this->items[$index]['harga_modal'] = (float) $product->harga_modal;
                            $this->items[$index]['harga_base'] = (float) $product->harga_base;
                            
                            $discountSteps = ($product->type === 'LM') ? $this->customerDiscountLm : $this->customerDiscountBr;
                            $this->items[$index]['discount_steps'] = $discountSteps;
                            $this->items[$index]['discount_steps_input'] = implode(', ', $discountSteps);
                        }
                    } else {
                        $this->items[$index]['product_name'] = '';
                        $this->items[$index]['product_type'] = '';
                        $this->items[$index]['harga_modal'] = 0.00;
                        $this->items[$index]['harga_base'] = 0.00;
                        $this->items[$index]['discount_steps'] = [];
                        $this->items[$index]['discount_steps_input'] = '';
                    }
                } elseif ($property === 'discount_steps_input') {
                    $this->items[$index]['discount_steps'] = $this->parseDiscounts($value);
                }

                $this->recalculateAll();
            }
        }
    }

    private function parseDiscounts($input): array
    {
        if (empty(trim($input))) {
            return [];
        }

        $items = explode(',', $input);
        $percentages = [];
        
        foreach ($items as $item) {
            $trimmed = trim($item);
            if ($trimmed !== '') {
                $percentages[] = (float) $trimmed;
            }
        }
        
        return $percentages;
    }

    public function recalculateAll()
    {
        foreach ($this->items as $index => $item) {
            if (empty($item['product_id'])) {
                continue;
            }

            $quantity = (float) $item['quantity'];
            $harga_base = (float) $item['harga_base'];
            $harga_modal = (float) $item['harga_modal'];

            if ($this->is_bonus) {
                $discounted_price = 0.00;
                $line_omzet = 0.00;
                $line_laba = 0.00;
            } else {
                $discounted_price = DiscountCalculator::calculate($harga_base, $item['discount_steps'] ?? []);
                $line_omzet = $discounted_price * $quantity;
                $line_laba = ($discounted_price - $harga_modal) * $quantity;
            }

            $this->items[$index]['discounted_unit_price'] = round($discounted_price, 2);
            $this->items[$index]['line_omzet'] = round($line_omzet, 2);
            $this->items[$index]['line_laba'] = round($line_laba, 2);
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->status === 'Lunas') {
            if (empty($this->tanggal_pelunasan)) {
                $this->tanggal_pelunasan = now()->format('Y-m-d');
            }
        } else {
            $this->tanggal_pelunasan = null;
        }

        DB::beginTransaction();
        try {
            if ($this->isEditMode) {
                $transaction = Transaction::findOrFail($this->transactionId);
                $transaction->update([
                    'nomor_bon' => $this->nomor_bon,
                    'customer_id' => $this->customer_id,
                    'tanggal' => $this->tanggal,
                    'tanggal_pelunasan' => $this->tanggal_pelunasan,
                    'status' => $this->status,
                    'is_bonus' => $this->is_bonus,
                    'bonuses_claimed' => $this->bonuses_claimed,
                    'ongkir' => $this->ongkir,
                    'deskripsi' => $this->deskripsi,
                ]);

                $transaction->items()->delete();
            } else {
                $transaction = Transaction::create([
                    'nomor_bon' => $this->nomor_bon,
                    'customer_id' => $this->customer_id,
                    'tanggal' => $this->tanggal,
                    'tanggal_pelunasan' => $this->tanggal_pelunasan,
                    'status' => $this->status,
                    'is_bonus' => $this->is_bonus,
                    'bonuses_claimed' => $this->bonuses_claimed,
                    'ongkir' => $this->ongkir,
                    'deskripsi' => $this->deskripsi,
                ]);
            }

            foreach ($this->items as $item) {
                if (empty($item['product_id'])) {
                    continue;
                }

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_type' => $item['product_type'],
                    'harga_modal' => $item['harga_modal'],
                    'harga_base' => $item['harga_base'],
                    'discount_steps' => $item['discount_steps'],
                    'quantity' => $item['quantity'],
                    'discounted_unit_price' => $item['discounted_unit_price'],
                    'line_omzet' => $item['line_omzet'],
                    'line_laba' => $item['line_laba'],
                ]);
            }

            DB::commit();

            session()->flash('message', $this->isEditMode ? 'Transaksi berhasil diperbarui.' : 'Transaksi baru berhasil dibuat.');
            return redirect()->route('transactions.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getSubtotalProperty()
    {
        return collect($this->items)->sum('line_omzet');
    }

    public function getTotalOwedProperty()
    {
        return $this->subtotal + (float) $this->ongkir;
    }

    public function render()
    {
        return view('livewire.transaction-creator');
    }
}
