<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmailValidationController extends Controller
{

    public function gerar(Request $request)
    {

        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;
        $token = Str::uuid()->toString();
        $expiresAt = now()->addMinutes(30);
        $origin = $request->input('origin', 'web');

        DB::table('validados')->insert([
            'email' => $email,
            'token' => $token,
            'origem' => $origin,
            'expires_at' => $expiresAt,
            'created_at' => now(),
        ]);

        $link = url("/confirmar-email/{$token}");

        return response()->json([
            'link' => $link,
            'expires_at' => $expiresAt->toIso8601String()
        ]);
    }


    public function status($email)
    {
        $validado = DB::table('validados')
            ->where('email', $email)
            ->where('validado', 1)
            ->exists();

        return response()->json([
            'email' => $email,
            'validado' => $validado
        ]);
    }
}
