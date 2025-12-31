<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\GarminAccount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GarminController extends Controller
{
    /**
     * Redirigir al alumno a Garmin para autorizar la conexión.
     */
    public function redirectToGarmin()
    {
        // Nota: Si se usa un driver personalizado de Socialite, se haría así:
        // return Socialite::driver('garmin')->redirect();

        // Como no tenemos el driver instalado y requiere configuración de terceros,
        // simularemos o prepararemos el flujo.
        return response()->json(['message' => 'Redirección a Garmin no implementada totalmente (falta driver o credenciales reales)'], 501);
    }

    /**
     * Manejar el callback de Garmin tras la autorización.
     */
    public function handleGarminCallback()
    {
        try {
            // $garminUser = Socialite::driver('garmin')->user();

            // Simulación de datos recibidos de Garmin
            $garminUser = (object)[
                'id' => 'garmin_user_' . Str::random(10),
                'token' => Str::random(40),
                'refreshToken' => Str::random(40),
                'expiresIn' => 3600,
            ];

            $user = Auth::user();
            if (!$user || $user->rol !== 'alumno') {
                return redirect('/dashboard')->with('error', 'Solo los alumnos pueden conectar su cuenta de Garmin.');
            }

            $alumno = Alumno::where('userId', $user->id)->first();
            if (!$alumno) {
                return redirect('/dashboard')->with('error', 'No se encontró el perfil de alumno.');
            }

            GarminAccount::updateOrCreate(
                ['alumno_id' => $alumno->id],
                [
                    'id' => 'ga' . bin2hex(random_bytes(10)),
                    'garmin_user_id' => $garminUser->id,
                    'access_token' => $garminUser->token,
                    'refresh_token' => $garminUser->refreshToken ?? null,
                    'expires_at' => now()->addSeconds($garminUser->expiresIn ?? 3600),
                ]
            );

            return redirect('/dashboard/alumno/configuracion')->with('success', 'Cuenta de Garmin conectada correctamente.');

        } catch (Exception $e) {
            return redirect('/dashboard/alumno/configuracion')->with('error', 'Error al conectar con Garmin: ' . $e->getMessage());
        }
    }

    /**
     * Desconectar la cuenta de Garmin.
     */
    public function disconnect()
    {
        $user = Auth::user();
        $alumno = Alumno::where('userId', $user->id)->first();

        if ($alumno && $alumno->garminAccount) {
            $alumno->garminAccount->delete();
            return back()->with('success', 'Cuenta de Garmin desconectada.');
        }

        return back()->with('error', 'No hay ninguna cuenta de Garmin conectada.');
    }
}
