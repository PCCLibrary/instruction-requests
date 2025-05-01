<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class LibrarianController extends Controller
{
    /**
     * Display a listing of librarians.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $librarians = User::orderedLibrariansScope()
                ->select('id', 'display_name')
                ->orderBy('display_name')
                ->get();

            return response()->json($librarians, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching librarians', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to retrieve librarians'
            ], 500);
        }
    }
}
