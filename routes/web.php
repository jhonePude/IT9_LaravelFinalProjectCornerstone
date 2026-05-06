<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MemberPortalController;
use App\Http\Controllers\EventController;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

/* --- Public Landing Page --- */
Route::get('/', function () { 
    if (Auth::check()) {
        return Auth::user()->Role_Id == 1 
            ? redirect()->route('dashboard') 
            : redirect()->route('member.portal');
    }

    $events = Event::with('category')->orderBy('Event_Date', 'asc')->take(4)->get();

    // Finance data for Loading Bars
    $totalExpenses = Transaction::where('Type', 'Expense')->where('Status', 'Completed')->sum('Amount');
    $totalIncome = Transaction::where('Type', 'Income')->where('Status', 'Completed')->sum('Amount');
    
    $totalFinance = $totalIncome + $totalExpenses;
    $incomePercentage = $totalFinance > 0 ? round(($totalIncome / $totalFinance) * 100) : 0;
    $expensePercentage = $totalFinance > 0 ? round(($totalExpenses / $totalFinance) * 100) : 0;

    $impactData = Transaction::select('transaction_categories.Category_Name', DB::raw('SUM(Amount) as total'))
        ->join('transaction_categories', 'transactions.Category_Id', '=', 'transaction_categories.Category_Id')
        ->where('Type', 'Expense')
        ->where('Status', 'Completed')
        ->groupBy('transaction_categories.Category_Name')
        ->get()
        ->map(function($item) use ($totalExpenses) {
            $item->percentage = $totalExpenses > 0 ? round(($item->total / $totalExpenses) * 100) : 0;
            return $item;
        });

    return view('landingpage.landingpage', compact('events', 'impactData', 'totalIncome', 'totalExpenses', 'incomePercentage', 'expensePercentage')); 
})->name('landing');

// ... (Rest of your routes remain exactly the same)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password/process', [AuthController::class, 'handleRecovery']);
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/member/portal', [MemberPortalController::class, 'index'])->name('member.portal');
    Route::get('/fetch-portal-events', [MemberPortalController::class, 'getEvents'])->name('portal.events.data');
    Route::post('/member/portal/toggle-join', [MemberPortalController::class, 'toggleJoin']);
    Route::post('/member/portal/update-photo', [MemberPortalController::class, 'updatePhoto']);
    Route::post('/notifications/mark-read', [MemberPortalController::class, 'markNotificationsRead'])->name('notifications.markRead');

    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/data', [UserController::class, 'getUsers']);
        Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/update', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/reset-password', [UserController::class, 'resetPassword']);
        Route::post('/users/toggle-status', [UserController::class, 'toggleStatus']);
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::get('/events/data', [EventController::class, 'getData']);
        Route::post('/events/store', [EventController::class, 'store']);
        Route::delete('/events/delete/{id}', [EventController::class, 'destroy']);
        Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::get('/finance/data', [FinanceController::class, 'getData']);
        Route::post('/finance/store', [FinanceController::class, 'store']);
        Route::delete('/finance/delete/{id}', [FinanceController::class, 'destroy']);
        Route::post('/finance/pay/{id}', [FinanceController::class, 'markAsPaid']);
        Route::get('/finance/export', [FinanceController::class, 'exportPDF'])->name('finance.export');    
        Route::post('/finance/pay/{id}', [FinanceController::class, 'initiatePayment'])->name('finance.pay');
        Route::get('/finance/success/{id}', [FinanceController::class, 'paymentSuccess'])->name('finance.success');
    });
});