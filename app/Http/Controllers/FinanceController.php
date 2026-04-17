<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class FinanceController extends Controller
{
    public function index()
    {
        // Fetch categories for the Modal dropdown
        $categories = DB::table('transaction_categories')->get();
        return view('finance.index', compact('categories'));
    }

    public function getData()
    {
        // 1. Get all transactions (including Pending ones) to show in the table
        $transactions = Transaction::select('transactions.*', 'transaction_categories.Category_Name')
            ->join('transaction_categories', 'transactions.Category_Id', '=', 'transaction_categories.Category_Id')
            ->orderBy('Transaction_Date', 'desc')
            ->get();

        // 2. Calculate Totals (ONLY count COMPLETED records for the cards)
        $totalIncome = Transaction::where('Type', 'Income')
            ->where('Status', 'Completed')
            ->sum('Amount');

        $totalExpense = Transaction::where('Type', 'Expense')
            ->where('Status', 'Completed')
            ->sum('Amount');

        $totalBalance = $totalIncome - $totalExpense;

        // 3. Return JSON for the frontend
        return response()->json([
            'transactions' => $transactions,
            'stats' => [
                'income' => number_format($totalIncome, 2),
                'expense' => number_format($totalExpense, 2),
                'balance' => number_format($totalBalance, 2)
            ]
        ]);
    }

    public function store(Request $request)
    {
        try {
            // If ID is empty string, make it null so we create a new record
            $id = $request->Transaction_Id ?: null;

            Transaction::updateOrCreate(
                ['Transaction_Id' => $id], 
                [
                    'Description'      => $request->description,
                    'Type'             => $request->type,
                    'Amount'           => $request->amount,
                    'Category_Id'      => $request->category_id,
                    'Transaction_Date' => $request->date,
                    'Status'           => $request->status,
                    'Recorded_By'      => Auth::id()
                ]
            );

            return response("success");
        } catch (\Exception $e) {
            return response("Error: " . $e->getMessage(), 500);
        }
    }

    // THIS IS THE NEW "MARK AS COMPLETED" METHOD
    public function markAsPaid($id)
    {
        try {
            $transaction = Transaction::find($id);
            if ($transaction) {
                $transaction->Status = 'Completed';
                $transaction->save();
                return response("success");
            }
            return response("Transaction not found", 404);
        } catch (\Exception $e) {
            return response("Error: " . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        if ($transaction) {
            $transaction->delete();
            return response("success");
        }
        return response("error", 404);
    }


    public function exportPDF() 
{
        $transactions = Transaction::select('transactions.*', 'transaction_categories.Category_Name')
            ->join('transaction_categories', 'transactions.Category_Id', '=', 'transaction_categories.Category_Id')
            ->orderBy('Transaction_Date', 'desc')
            ->get();

        $data = [
            'transactions' => $transactions,
            'income' => Transaction::where('Type', 'Income')->where('Status', 'Completed')->sum('Amount'),
            'expense' => Transaction::where('Type', 'Expense')->where('Status', 'Completed')->sum('Amount'),
            'balance' => 0, // Will calculate below
            'reportDate' => now()->format('M d, Y')
        ];
        $data['balance'] = $data['income'] - $data['expense'];

        // This method bypasses the Facade entirely and asks Laravel for the PDF tool directly
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('finance.pdf_report', $data);
        
        return $pdf->download('Church_Financial_Report.pdf');
    }









       public function initiatePayment($id)
    {
        $transaction = Transaction::findOrFail($id);

        // PayMongo amounts are in centavos (Pesos * 100)
        $amountInCentavos = (int) ($transaction->Amount * 100);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode(config('services.paymongo.secret_key') . ':')
            ])->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'send_email_receipt' => true,
                        'show_description' => true,
                        'show_line_items' => true,
                        'description' => "Payment for " . $transaction->Description,
                        'line_items' => [
                            [
                                'currency' => 'PHP',
                                'amount' => $amountInCentavos,
                                'description' => $transaction->Description,
                                'name' => $transaction->Description,
                                'quantity' => 1,
                            ]
                        ],
                        'payment_method_types' => ['gcash', 'paymaya', 'card', 'dob', 'dob_ubp'],
                        'success_url' => route('finance.success', $transaction->Transaction_Id),
                        'cancel_url' => route('finance.index'),
                    ]
                ]
            ]);

            if ($response->successful()) {
                $checkoutUrl = $response->json()['data']['attributes']['checkout_url'];
                return response()->json(['checkout_url' => $checkoutUrl]);
            }

            return response()->json(['error' => 'Failed to create payment session'], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function paymentSuccess($id)
    {
        $transaction = Transaction::findOrFail($id);
        
        // Update status to completed
        $transaction->Status = 'Completed';
        $transaction->save();

        return redirect()->route('finance.index')->with('success', 'Payment Successful!');
    }
}