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

        $existe = DB::table('validados')
            ->select('email', 'validado', 'origem')
            ->where('email', $email)
            ->where('origem', $origin)
            ->first();

        if($existe && $existe->validado == 1){
            return response()->json([
                'message' => 'esse email ja foi verificado para o aplicativo ' . $origin
            ], 400);
        }
        
        if($existe){
            DB::table('validados')
                ->where('email', $email)
                ->where('origem', $origin)
                ->update([
                    'token' => $token,
                    'expires_at' => $expiresAt,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('validados')->insert([
                'email' => $email,
                'token' => $token,
                'origem' => $origin,
                'expires_at' => $expiresAt,
                'created_at' => now(),
            ]);
        }

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
