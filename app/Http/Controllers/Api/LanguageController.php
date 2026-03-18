<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Switch the application locale.
     */
    public function switch(Request $request)
    {
        $request->validate([
            'lang' => 'required|string|in:en,ar',
        ]);

        $lang = $request->lang;
        
        // Update session for web-based views if any
        session(['locale' => $lang]);
        
        // Set application locale for current request
        app()->setLocale($lang);

        return response()->json([
            'success' => true,
            'message' => 'Language switched to ' . $lang,
            'locale' => $lang
        ]);
    }
}
