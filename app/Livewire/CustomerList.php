<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;

class CustomerList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    
    // Form properties
    public $isEditMode = false;
    public $customerId = null;
    public $name = '';
    public $discount_lm_input = ''; // e.g. "20, 20, 10"
    public $discount_br_input = ''; // e.g. "20, 10"
    public $bonus_threshold = 10000000;

    protected $rules = [
        'name' => 'required|string|max:255',
        'discount_lm_input' => 'nullable|string',
        'discount_br_input' => 'nullable|string',
        'bonus_threshold' => 'required|numeric|min:0',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetErrorBag();
        $this->isEditMode = false;
        $this->customerId = null;
        $this->name = '';
        $this->discount_lm_input = '20, 20, 10';
        $this->discount_br_input = '20, 10';
        $this->bonus_threshold = 10000000;

        $this->dispatch('show-customer-modal');
    }

    public function openEditModal(Customer $customer)
    {
        $this->resetErrorBag();
        $this->isEditMode = true;
        $this->customerId = $customer->id;
        $this->name = $customer->name;
        
        $this->discount_lm_input = is_array($customer->discount_lm) ? implode(', ', $customer->discount_lm) : '';
        $this->discount_br_input = is_array($customer->discount_br) ? implode(', ', $customer->discount_br) : '';
        $this->bonus_threshold = (float) $customer->bonus_threshold;

        $this->dispatch('show-customer-modal');
    }

    public function save()
    {
        $this->validate();

        // Parse discount strings
        $discountLm = $this->parseDiscounts($this->discount_lm_input);
        $discountBr = $this->parseDiscounts($this->discount_br_input);

        if ($this->isEditMode) {
            $customer = Customer::findOrFail($this->customerId);
            $customer->update([
                'name' => $this->name,
                'discount_lm' => $discountLm,
                'discount_br' => $discountBr,
                'bonus_threshold' => $this->bonus_threshold,
            ]);
            $this->dispatch('hide-customer-modal');
            session()->flash('message', 'Data pelanggan berhasil diperbarui.');
        } else {
            Customer::create([
                'name' => $this->name,
                'discount_lm' => $discountLm,
                'discount_br' => $discountBr,
                'bonus_threshold' => $this->bonus_threshold,
            ]);
            $this->dispatch('hide-customer-modal');
            session()->flash('message', 'Pelanggan baru berhasil ditambahkan.');
        }
    }

    public function delete($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete(); // Soft delete as configured
        session()->flash('message', 'Pelanggan berhasil dinonaktifkan.');
    }

    private function parseDiscounts($input): array
    {
        if (empty(trim($input))) {
            return [];
        }

        $items = explode(',', $input);
        $percentages = [];
        
        foreach ($items as $item) {
            $num = filter_var(trim($item), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            if ($num !== false && $num !== '') {
                $percentages[] = (float) $num;
            }
        }
        
        return $percentages;
    }

    public function render()
    {
        $customers = Customer::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('livewire.customer-list', [
            'customers' => $customers
        ]);
    }
}
