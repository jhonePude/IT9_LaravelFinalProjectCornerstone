<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Event; 
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Monthly Giving (current month) – keep for reference
        $monthlyGiving = Transaction::where('Type', 'Income')
            ->where('Status', 'Completed')
            ->whereMonth('Transaction_Date', Carbon::now()->month)
            ->sum('Amount');

        // 2. Total Income, Expense, and Balance (for the card)
        $totalIncome = Transaction::where('Type', 'Income')
            ->where('Status', 'Completed')
            ->sum('Amount');

        $totalExpense = Transaction::where('Type', 'Expense')
            ->where('Status', 'Completed')
            ->sum('Amount');

        $totalBalance = $totalIncome - $totalExpense;

        // 3. Financial Chart Logic (6 months)
        $months = [];
        $incomeData = [];
        $expenseData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M');
            $incomeData[] = Transaction::where('Type', 'Income')
                ->where('Status', 'Completed')
                ->whereMonth('Transaction_Date', $month->month)
                ->sum('Amount');
            $expenseData[] = Transaction::where('Type', 'Expense')
                ->where('Status', 'Completed')
                ->whereMonth('Transaction_Date', $month->month)
                ->sum('Amount');
        }

        // 4. Attendance Chart Logic (last 8 events)
        $attendanceData = Event::withCount('registrations')
            ->orderBy('Event_Date', 'desc')
            ->take(8)
            ->get()
            ->reverse();
        $eventLabels = $attendanceData->pluck('Title')->toArray();
        $eventCounts = $attendanceData->pluck('registrations_count')->toArray();

        // 5. Stats array
        $stats = [
            'monthly_giving'  => number_format($monthlyGiving, 2),
            'total_balance'   => number_format($totalBalance, 2),
            'total_members'   => User::count(),
            'upcoming_events' => Event::count()
        ];

        return view('dashboard', compact('stats', 'months', 'incomeData', 'expenseData', 'eventLabels', 'eventCounts'));
    }
}