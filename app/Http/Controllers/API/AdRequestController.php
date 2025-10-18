<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdRequestController extends Controller
{
    /**
     * Display a listing of the user's ad requests.
     */
    public function index()
    {
        try {
            $adRequests = AdRequest::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $adRequests,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ad requests',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created ad request.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'campaign_name' => 'required|string|max:255',
            'campaign_description' => 'nullable|string',
            'platform' => 'required|in:facebook,instagram,twitter,linkedin,tiktok,snapchat,youtube',
            'ad_type' => 'required|in:image,video,carousel,story,collection',
            'objective' => 'required|in:awareness,traffic,engagement,leads,sales,app_promotion',
            'budget' => 'required|numeric|min:1',
            'currency' => 'nullable|string|size:3',
            'duration_days' => 'required|integer|min:1|max:365',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'targeting' => 'nullable|array',
            'targeting.age_min' => 'nullable|integer|min:13|max:100',
            'targeting.age_max' => 'nullable|integer|min:13|max:100',
            'targeting.gender' => 'nullable|in:all,male,female',
            'targeting.locations' => 'nullable|array',
            'targeting.interests' => 'nullable|array',
            'creative_assets' => 'nullable|array',
            'ad_headline' => 'nullable|string|max:255',
            'ad_copy' => 'nullable|string',
            'call_to_action' => 'nullable|string|max:255',
            'destination_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $adRequest = AdRequest::create([
                'user_id' => Auth::id(),
                'campaign_name' => $request->campaign_name,
                'campaign_description' => $request->campaign_description,
                'platform' => $request->platform,
                'ad_type' => $request->ad_type,
                'objective' => $request->objective,
                'budget' => $request->budget,
                'currency' => $request->currency ?? 'USD',
                'duration_days' => $request->duration_days,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'targeting' => $request->targeting,
                'creative_assets' => $request->creative_assets,
                'ad_headline' => $request->ad_headline,
                'ad_copy' => $request->ad_copy,
                'call_to_action' => $request->call_to_action,
                'destination_url' => $request->destination_url,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ad request submitted successfully. Our team will review it shortly.',
                'data' => $adRequest,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ad request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified ad request.
     */
    public function show($id)
    {
        try {
            $adRequest = AdRequest::where('user_id', Auth::id())
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $adRequest,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ad request not found',
            ], 404);
        }
    }

    /**
     * Update the specified ad request (only if pending).
     */
    public function update(Request $request, $id)
    {
        try {
            $adRequest = AdRequest::where('user_id', Auth::id())
                ->findOrFail($id);

            if ($adRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update ad request. It is already being reviewed or running.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'campaign_name' => 'sometimes|string|max:255',
                'campaign_description' => 'nullable|string',
                'platform' => 'sometimes|in:facebook,instagram,twitter,linkedin,tiktok,snapchat,youtube',
                'ad_type' => 'sometimes|in:image,video,carousel,story,collection',
                'objective' => 'sometimes|in:awareness,traffic,engagement,leads,sales,app_promotion',
                'budget' => 'sometimes|numeric|min:1',
                'duration_days' => 'sometimes|integer|min:1|max:365',
                'start_date' => 'sometimes|date|after_or_equal:today',
                'end_date' => 'sometimes|date|after:start_date',
                'targeting' => 'nullable|array',
                'creative_assets' => 'nullable|array',
                'ad_headline' => 'nullable|string|max:255',
                'ad_copy' => 'nullable|string',
                'call_to_action' => 'nullable|string|max:255',
                'destination_url' => 'nullable|url',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $adRequest->update($request->only([
                'campaign_name',
                'campaign_description',
                'platform',
                'ad_type',
                'objective',
                'budget',
                'duration_days',
                'start_date',
                'end_date',
                'targeting',
                'creative_assets',
                'ad_headline',
                'ad_copy',
                'call_to_action',
                'destination_url',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Ad request updated successfully',
                'data' => $adRequest,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ad request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified ad request (only if pending).
     */
    public function destroy($id)
    {
        try {
            $adRequest = AdRequest::where('user_id', Auth::id())
                ->findOrFail($id);

            if ($adRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete ad request. It is already being reviewed or running.',
                ], 403);
            }

            $adRequest->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ad request deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ad request',
            ], 500);
        }
    }

    /**
     * Get ad request statistics for the user.
     */
    public function statistics()
    {
        try {
            $userId = Auth::id();

            $stats = [
                'total' => AdRequest::where('user_id', $userId)->count(),
                'pending' => AdRequest::where('user_id', $userId)->where('status', 'pending')->count(),
                'in_review' => AdRequest::where('user_id', $userId)->where('status', 'in_review')->count(),
                'approved' => AdRequest::where('user_id', $userId)->where('status', 'approved')->count(),
                'running' => AdRequest::where('user_id', $userId)->where('status', 'running')->count(),
                'completed' => AdRequest::where('user_id', $userId)->where('status', 'completed')->count(),
                'rejected' => AdRequest::where('user_id', $userId)->where('status', 'rejected')->count(),
                'total_budget' => AdRequest::where('user_id', $userId)->sum('budget'),
                'running_budget' => AdRequest::where('user_id', $userId)
                    ->where('status', 'running')
                    ->sum('budget'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
            ], 500);
        }
    }
}
