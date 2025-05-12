<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SendNotification extends Controller
{
    public function sendWhatsappMessage(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'target' => 'required|string',
            'message' => 'required|string',
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'ThQcjFqb6bCiEq1vKEf2',
        ])->asForm()
            ->post('https://api.fonnte.com/send', [
                'target' => $request->input('target'),
                'message' => $request->input('message'),
            ]);

        // Check for errors
        if ($response->failed()) {
            return response()->json([
                'error' => 'Error from Fonnte API',
                'message' => $response->body(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $response->json(),
        ]);
    }
}
