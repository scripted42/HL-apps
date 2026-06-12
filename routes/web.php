<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;

Route::get('/', function () {
    return view('welcome');
});

// Onboarding Walkthrough Tour Route
Route::post('/admin/complete-tour', function () {
    $user = auth()->user() ?? \Filament\Facades\Filament::auth()->user();
    if ($user) {
        $user->update(['has_completed_tour' => true]);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
})->middleware(['web']);

// PDF Generation Routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/transactions/{transaction}/pdf', [PDFController::class, 'downloadReceipt'])->name('admin.transactions.pdf');
    Route::get('/admin/customers/{customer}/pdf-recap', [PDFController::class, 'downloadCustomerRecap'])->name('admin.customers.pdf-recap');
});
