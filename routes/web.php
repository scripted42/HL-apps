<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PDFController;
use App\Livewire\Dashboard;
use App\Livewire\CustomerList;
use App\Livewire\CustomerDetail;
use App\Livewire\ProductList;
use App\Livewire\TransactionList;
use App\Livewire\TransactionCreator;

// Guest Routes
Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/', function () {
        return redirect('/login');
    });
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    // Customers CRUD
    Route::get('/customers', CustomerList::class)->name('customers.index');
    Route::get('/customers/{customer}', CustomerDetail::class)->name('customers.show');
    Route::get('/customers/{customer}/pdf-recap', [PDFController::class, 'downloadCustomerRecap'])->name('customers.pdf-recap');
    
    // Products CRUD
    Route::get('/products', ProductList::class)->name('products.index');
    
    // Transactions CRUD
    Route::get('/transactions', TransactionList::class)->name('transactions.index');
    Route::get('/transactions/create', TransactionCreator::class)->name('transactions.create');
    Route::get('/transactions/{transaction}/edit', TransactionCreator::class)->name('transactions.edit');
    Route::get('/transactions/{transaction}/pdf', [PDFController::class, 'downloadReceipt'])->name('transactions.pdf');
});
