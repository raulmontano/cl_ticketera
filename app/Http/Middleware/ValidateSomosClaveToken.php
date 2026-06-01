<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateSomosClaveToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->somosclave_token;

        if (!$token || !str_contains($token, '.')) {
            return redirect()->route('requester.access-denied',['not_logged'=>true]);
        }

        [$payload64, $signature] = explode('.', $token, 2);
        $payloadJson = base64_decode($payload64);
        $payload = json_decode($payloadJson, true);

        $secret = ENV('SOMOSCLAVE_SECRET');

        $expected = hash_hmac('sha256', $payloadJson, $secret);

        if (!hash_equals($expected, $signature)) {
            return redirect()->route('requester.access-denied',['not_logged'=>true]);
        }

        //4 = claro admin
        //11 = vtr admin
        if(!isset($payload['user_type']) || !in_array($payload['user_type'],[4, 11])){
            return redirect()->route('requester.access-denied',['bad_privileges'=>true]);
        }

        // 📦 Adjuntar usuario al request
        $request->attributes->set('somosclave_user', $payload);
        $request->attributes->set('somosclave_token', $token);

        return $next($request);
    }
}
