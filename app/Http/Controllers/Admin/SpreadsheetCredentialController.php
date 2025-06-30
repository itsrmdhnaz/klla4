<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spreadsheet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SpreadsheetCredentialController extends Controller
{
    /**
     * Serve credential file securely
     */
    public function serveCredential(Request $request, $spreadsheetId)
    {
        $user = auth()->user();
        
        // Find spreadsheet
        $spreadsheet = Spreadsheet::findOrFail($spreadsheetId);
        
        // Check if user has access to this spreadsheet
        if (!$spreadsheet->userHasAccess($user->id, 'read')) {
            abort(403, 'Access denied to this spreadsheet');
        }
        
        // Check if credentials file exists
        if (!$spreadsheet->credentials_path || !Storage::disk('credentials')->exists($spreadsheet->credentials_path)) {
            abort(404, 'Credentials file not found');
        }
        
        // Get file path
        $filePath = Storage::disk('credentials')->path($spreadsheet->credentials_path);
        
        // Verify file is actually a JSON credential file
        $content = Storage::disk('credentials')->get($spreadsheet->credentials_path);
        $credentials = json_decode($content, true);
        
        if (!$credentials || !isset($credentials['type']) || $credentials['type'] !== 'service_account') {
            abort(403, 'Invalid credentials file');
        }
        
        // Log access
        $spreadsheet->accessLogs()->create([
            'user_id' => $user->id,
            'action' => 'credential_access',
            'request_data' => [
                'file' => $spreadsheet->credentials_path,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return response()->json($credentials);
    }
}
