<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    public function index()
    {
        return response()->json(Platform::all());
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'platform_id' => 'required|exists:platforms,id',
        ]);

        $user = $request->user();

        if ($user->platforms()->where('platform_id', $request->platform_id)->exists()) {
            $user->platforms()->detach($request->platform_id);
            return response()->json(['message' => 'Platform detached']);
        } else {
            $user->platforms()->attach($request->platform_id);
            return response()->json(['message' => 'Platform attached']);
        }
    }
}
