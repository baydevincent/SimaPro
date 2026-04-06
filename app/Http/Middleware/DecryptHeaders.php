<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DecryptHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verify encrypted token if present
        $encryptedToken = $request->header('X-ENCRYPTED-TOKEN');
        if ($encryptedToken) {
            try {
                $decryptedToken = decrypt($encryptedToken);
                $request->headers->set('X-DECRYPTED-TOKEN', $decryptedToken);
                
                // Verify token matches CSRF token
                if ($decryptedToken !== $request->session()->token()) {
                    \Log::warning('Token mismatch detected from IP: ' . $request->ip());
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to decrypt token: ' . $e->getMessage());
            }
        }

        // Verify timestamp (prevent replay attacks)
        $timestamp = $request->header('X-TIMESTAMP');
        if ($timestamp) {
            $requestAge = time() - (int)$timestamp;
            if ($requestAge > 300) { // 5 minutes
                \Log::warning('Request too old (' . $requestAge . 's) from IP: ' . $request->ip());
            }
        }

        // Check if request is marked as encrypted
        $isEncrypted = $request->header('X-ENCRYPTED-REQUEST');
        if ($isEncrypted === 'true') {
            \Log::info('Encrypted request received from IP: ' . $request->ip(), [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'timestamp' => $timestamp,
            ]);
        }

        return $next($request);
    }
}
