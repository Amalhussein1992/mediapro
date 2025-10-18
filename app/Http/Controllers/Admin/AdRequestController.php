<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdRequest;
use Illuminate\Http\Request;

class AdRequestController extends Controller
{
    /**
     * Display a listing of ad requests.
     */
    public function index(Request $request)
    {
        $query = AdRequest::with('user');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by platform
        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        // Search by campaign name or user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('campaign_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $adRequests = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total' => AdRequest::count(),
            'pending' => AdRequest::where('status', 'pending')->count(),
            'in_review' => AdRequest::where('status', 'in_review')->count(),
            'approved' => AdRequest::where('status', 'approved')->count(),
            'running' => AdRequest::where('status', 'running')->count(),
            'completed' => AdRequest::where('status', 'completed')->count(),
            'rejected' => AdRequest::where('status', 'rejected')->count(),
            'total_budget' => AdRequest::whereIn('status', ['approved', 'running', 'completed'])->sum('budget'),
        ];

        return view('admin.ad-requests.index', compact('adRequests', 'stats'));
    }

    /**
     * Display the specified ad request.
     */
    public function show($id)
    {
        $adRequest = AdRequest::with('user')->findOrFail($id);
        return view('admin.ad-requests.show', compact('adRequest'));
    }

    /**
     * Update ad request status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_review,approved,running,paused,completed,rejected',
            'admin_notes' => 'nullable|string',
            'rejection_reason' => 'required_if:status,rejected|string',
        ]);

        $adRequest = AdRequest::findOrFail($id);

        $adRequest->status = $request->status;
        $adRequest->admin_notes = $request->admin_notes;

        if ($request->status === 'rejected') {
            $adRequest->rejection_reason = $request->rejection_reason;
        }

        if ($request->status === 'approved' || $request->status === 'in_review') {
            $adRequest->reviewed_at = now();
        }

        if ($request->status === 'running') {
            $adRequest->started_at = now();
        }

        if ($request->status === 'completed') {
            $adRequest->completed_at = now();
        }

        $adRequest->save();

        return redirect()->route('admin.ad-requests.show', $id)
            ->with('success', 'Ad request status updated successfully');
    }

    /**
     * Update performance metrics.
     */
    public function updateMetrics(Request $request, $id)
    {
        $request->validate([
            'impressions' => 'nullable|integer|min:0',
            'clicks' => 'nullable|integer|min:0',
            'conversions' => 'nullable|integer|min:0',
            'spend' => 'nullable|numeric|min:0',
        ]);

        $adRequest = AdRequest::findOrFail($id);

        $metrics = $adRequest->performance_metrics ?? [];
        $metrics = array_merge($metrics, [
            'impressions' => $request->impressions ?? 0,
            'clicks' => $request->clicks ?? 0,
            'conversions' => $request->conversions ?? 0,
            'spend' => $request->spend ?? 0,
            'ctr' => $request->impressions > 0 ? round(($request->clicks / $request->impressions) * 100, 2) : 0,
            'cpc' => $request->clicks > 0 ? round($request->spend / $request->clicks, 2) : 0,
            'updated_at' => now()->toDateTimeString(),
        ]);

        $adRequest->performance_metrics = $metrics;
        $adRequest->save();

        return redirect()->route('admin.ad-requests.show', $id)
            ->with('success', 'Performance metrics updated successfully');
    }

    /**
     * Delete an ad request.
     */
    public function destroy($id)
    {
        $adRequest = AdRequest::findOrFail($id);

        if (in_array($adRequest->status, ['running'])) {
            return redirect()->route('admin.ad-requests.index')
                ->with('error', 'Cannot delete a running ad campaign');
        }

        $adRequest->delete();

        return redirect()->route('admin.ad-requests.index')
            ->with('success', 'Ad request deleted successfully');
    }

    /**
     * Bulk approve ad requests.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ad_requests,id',
        ]);

        AdRequest::whereIn('id', $request->ids)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'reviewed_at' => now(),
            ]);

        return redirect()->route('admin.ad-requests.index')
            ->with('success', count($request->ids) . ' ad requests approved successfully');
    }

    /**
     * Export ad requests to CSV.
     */
    public function export(Request $request)
    {
        $query = AdRequest::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        $adRequests = $query->orderBy('created_at', 'desc')->get();

        $csvData = "ID,Campaign Name,User,Platform,Ad Type,Objective,Budget,Status,Start Date,End Date,Created At\n";

        foreach ($adRequests as $request) {
            $csvData .= sprintf(
                '%d,"%s","%s","%s","%s","%s",%s,"%s","%s","%s","%s"' . "\n",
                $request->id,
                $request->campaign_name,
                $request->user->name ?? 'N/A',
                ucfirst($request->platform),
                ucfirst($request->ad_type),
                ucfirst(str_replace('_', ' ', $request->objective)),
                $request->budget,
                ucfirst($request->status),
                $request->start_date->format('Y-m-d'),
                $request->end_date->format('Y-m-d'),
                $request->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ad_requests_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
