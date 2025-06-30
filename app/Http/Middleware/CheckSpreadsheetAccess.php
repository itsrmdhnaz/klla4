<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Spreadsheet;

class CheckSpreadsheetAccess
{
    public function handle(Request $request, Closure $next, string $level = 'read')
    {
        $user = auth()->user();
        $spreadsheetId = $request->route('spreadsheet') ?? $request->route('spreadsheetId');
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $spreadsheet = Spreadsheet::find($spreadsheetId);
        
        if (!$spreadsheet) {
            return response()->json(['error' => 'Spreadsheet not found'], 404);
        }
        
        if (!$spreadsheet->userHasAccess($user->id, $level)) {
            return response()->json(['error' => 'Access denied'], 403);
        }
        
        // Add spreadsheet to request for use in controller
        $request->merge(['spreadsheet' => $spreadsheet]);
        
        return $next($request);
    }
}
