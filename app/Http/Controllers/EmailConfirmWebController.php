<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class EmailConfirmWebController extends Controller
{
    public function confirmar($token)
    {
        $registro = DB::table('validados')
            ->where('token', $token)
            ->first();

        if (!$registro) {
            return view('error');
        }

        if ($registro->expires_at < now()) {
            return view('expired');
        }

        if (!$registro->validado) {
            DB::table('validados')
                ->where('id', $registro->id)
                ->update([
                    'validado' => 1,
                    'updated_at' => now(),
                ]);
        }

        return view('success', [
            'origin' => $registro->origem
        ]);
    }
}