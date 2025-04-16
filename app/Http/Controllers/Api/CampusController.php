<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CampusController extends Controller
{
    /**
     * Display a listing of campuses.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $campuses = Campus::select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json($campuses, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching campuses', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to retrieve campuses'
            ], 500);
        }
    }
}
