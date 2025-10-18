<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $query = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->leftJoin('subscription_plans', 'payments.subscription_plan_id', '=', 'subscription_plans.id')
            ->select(
                'payments.*',
                'users.name as user_name',
                'users.email as user_email',
                'subscription_plans.name as plan_name'
            );

        // Filter by status
        if ($request->filled('status')) {
            $query->where('payments.status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payments.payment_method', $request->payment_method);
        }

        // Search by transaction ID or user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payments.transaction_id', 'like', "%{$search}%")
                    ->orWhere('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('payments.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payments.created_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('payments.created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total_payments' => DB::table('payments')->count(),
            'total_revenue' => DB::table('payments')->where('status', 'completed')->sum('amount'),
            'completed_payments' => DB::table('payments')->where('status', 'completed')->count(),
            'pending_payments' => DB::table('payments')->where('status', 'pending')->count(),
            'failed_payments' => DB::table('payments')->where('status', 'failed')->count(),
            'refunded_payments' => DB::table('payments')->where('status', 'refunded')->count(),
            'revenue_this_month' => DB::table('payments')
                ->where('status', 'completed')
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Display payment details.
     */
    public function show($id)
    {
        $payment = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->leftJoin('subscription_plans', 'payments.subscription_plan_id', '=', 'subscription_plans.id')
            ->select(
                'payments.*',
                'users.name as user_name',
                'users.email as user_email',
                'subscription_plans.name as plan_name',
                'subscription_plans.price as plan_price'
            )
            ->where('payments.id', $id)
            ->first();

        if (!$payment) {
            return redirect()->route('admin.payments.index')
                ->with('error', __('Payment not found'));
        }

        // Decode metadata if exists
        if ($payment->metadata) {
            $payment->metadata = json_decode($payment->metadata, true);
        }

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Update payment status.
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
        ]);

        $payment = DB::table('payments')->where('id', $id)->first();

        if (!$payment) {
            return redirect()->route('admin.payments.index')
                ->with('error', __('Payment not found'));
        }

        $updateData = ['status' => $validated['status']];

        // If marking as completed, set paid_at timestamp
        if ($validated['status'] === 'completed' && !$payment->paid_at) {
            $updateData['paid_at'] = now();
        }

        DB::table('payments')->where('id', $id)->update($updateData);

        return redirect()->route('admin.payments.show', $id)
            ->with('success', __('Payment status updated successfully'));
    }

    /**
     * Refund a payment.
     */
    public function refund($id)
    {
        $payment = DB::table('payments')->where('id', $id)->first();

        if (!$payment) {
            return redirect()->route('admin.payments.index')
                ->with('error', __('Payment not found'));
        }

        if ($payment->status !== 'completed') {
            return redirect()->route('admin.payments.show', $id)
                ->with('error', __('Only completed payments can be refunded'));
        }

        DB::table('payments')->where('id', $id)->update([
            'status' => 'refunded',
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.payments.show', $id)
            ->with('success', __('Payment refunded successfully'));
    }

    /**
     * Export payments data to CSV.
     */
    public function export(Request $request)
    {
        $query = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->leftJoin('subscription_plans', 'payments.subscription_plan_id', '=', 'subscription_plans.id')
            ->select(
                'payments.*',
                'users.name as user_name',
                'users.email as user_email',
                'subscription_plans.name as plan_name'
            );

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('payments.status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payments.payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payments.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payments.created_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('payments.created_at', 'desc')->get();

        $csvData = "Transaction ID,User Name,User Email,Plan,Amount,Currency,Status,Payment Method,Payment Gateway,Paid At,Created At\n";

        foreach ($payments as $payment) {
            $csvData .= sprintf(
                '"%s","%s","%s","%s",%.2f,"%s","%s","%s","%s","%s","%s"' . "\n",
                $payment->transaction_id,
                $payment->user_name,
                $payment->user_email,
                $payment->plan_name ?? 'N/A',
                $payment->amount,
                $payment->currency,
                $payment->status,
                $payment->payment_method,
                $payment->payment_gateway ?? 'N/A',
                $payment->paid_at ?? 'N/A',
                $payment->created_at
            );
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Get revenue statistics for charts.
     */
    public function getRevenueStats()
    {
        // Revenue by month (last 12 months)
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = DB::table('payments')
                ->where('status', 'completed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $monthlyRevenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => (float) $revenue,
            ];
        }

        // Revenue by payment method
        $revenueByMethod = DB::table('payments')
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->where('status', 'completed')
            ->groupBy('payment_method')
            ->get()
            ->map(function ($item) {
                return [
                    'method' => $item->payment_method,
                    'total' => (float) $item->total,
                ];
            });

        // Revenue by plan
        $revenueByPlan = DB::table('payments')
            ->join('subscription_plans', 'payments.subscription_plan_id', '=', 'subscription_plans.id')
            ->select('subscription_plans.name', DB::raw('SUM(payments.amount) as total'))
            ->where('payments.status', 'completed')
            ->whereNotNull('payments.subscription_plan_id')
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->get()
            ->map(function ($item) {
                return [
                    'plan' => $item->name,
                    'total' => (float) $item->total,
                ];
            });

        return response()->json([
            'monthly_revenue' => $monthlyRevenue,
            'revenue_by_method' => $revenueByMethod,
            'revenue_by_plan' => $revenueByPlan,
        ]);
    }

    /**
     * Create a manual payment entry.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        $plans = DB::table('subscription_plans')->orderBy('price')->get();

        return view('admin.payments.create', compact('users', 'plans'));
    }

    /**
     * Store a manual payment entry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'status' => 'required|in:pending,completed,failed,refunded',
            'payment_method' => 'required|in:credit_card,paypal,stripe,bank_transfer,other',
            'payment_gateway' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $transactionId = 'MAN-' . strtoupper(uniqid());

        DB::table('payments')->insert([
            'user_id' => $validated['user_id'],
            'subscription_plan_id' => $validated['subscription_plan_id'] ?? null,
            'transaction_id' => $transactionId,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'status' => $validated['status'],
            'payment_method' => $validated['payment_method'],
            'payment_gateway' => $validated['payment_gateway'] ?? 'Manual Entry',
            'description' => $validated['description'] ?? null,
            'paid_at' => $validated['status'] === 'completed' ? now() : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.payments.index')
            ->with('success', __('Payment created successfully'));
    }
}
