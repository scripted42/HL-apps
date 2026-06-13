<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction;
use App\Models\Customer;
use Livewire\Attributes\Url;

class TransactionList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    
    #[Url]
    public $statusFilter = ''; // Piutang or Lunas
    
    public $customerFilter = ''; // customer_id

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCustomerFilter()
    {
        $this->resetPage();
    }

    public function settle($id)
    {
        $t = Transaction::findOrFail($id);
        $t->update([
            'status' => 'Lunas',
            'tanggal_pelunasan' => now(),
        ]);
        session()->flash('message', "Bon #{$t->nomor_bon} berhasil dilunasi.");
    }

    public function delete($id)
    {
        $t = Transaction::findOrFail($id);
        $t->delete(); // Cascades delete items because of cascade constraint
        session()->flash('message', "Transaksi berhasil dihapus.");
    }

    public function render()
    {
        $query = Transaction::with(['customer', 'items']);

        if (!empty($this->search)) {
            $query->where('nomor_bon', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->customerFilter)) {
            $query->where('customer_id', $this->customerFilter);
        }

        $transactions = $query->orderBy('tanggal', 'desc')
            ->latest()
            ->paginate(10);

        $customers = Customer::orderBy('name', 'asc')->get();

        return view('livewire.transaction-list', [
            'transactions' => $transactions,
            'customers' => $customers,
        ]);
    }
}
