<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

class ProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    
    // Form properties
    public $isEditMode = false;
    public $productId = null;
    public $name = '';
    public $harga_modal = 0.00;
    public $harga_base = 0.00;
    public $type = 'LM'; // LM or BR

    protected $rules = [
        'name' => 'required|string|max:255',
        'harga_modal' => 'required|numeric|min:0',
        'harga_base' => 'required|numeric|min:0',
        'type' => 'required|in:LM,BR',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetErrorBag();
        $this->isEditMode = false;
        $this->productId = null;
        $this->name = '';
        $this->harga_modal = 0.00;
        $this->harga_base = 0.00;
        $this->type = 'LM';

        $this->dispatch('show-product-modal');
    }

    public function openEditModal(Product $product)
    {
        $this->resetErrorBag();
        $this->isEditMode = true;
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->harga_modal = (float) $product->harga_modal;
        $this->harga_base = (float) $product->harga_base;
        $this->type = $product->type;

        $this->dispatch('show-product-modal');
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditMode) {
            $product = Product::findOrFail($this->productId);
            $product->update([
                'name' => $this->name,
                'harga_modal' => $this->harga_modal,
                'harga_base' => $this->harga_base,
                'type' => $this->type,
            ]);
            $this->dispatch('hide-product-modal');
            session()->flash('message', 'Data produk berhasil diperbarui.');
        } else {
            Product::create([
                'name' => $this->name,
                'harga_modal' => $this->harga_modal,
                'harga_base' => $this->harga_base,
                'type' => $this->type,
            ]);
            $this->dispatch('hide-product-modal');
            session()->flash('message', 'Produk baru berhasil ditambahkan.');
        }
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete(); // Soft delete as configured
        session()->flash('message', 'Produk berhasil dinonaktifkan.');
    }

    public function render()
    {
        $products = Product::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('livewire.product-list', [
            'products' => $products
        ]);
    }
}
