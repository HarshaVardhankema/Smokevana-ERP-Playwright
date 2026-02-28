<?php

namespace App\Http\Middleware;

use App\GuestSession;
use App\Models\ElevenLabsSessionModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Server\Exception\OAuthServerException;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

class EcomCustomerValidate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse | \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->bearerToken()) {
            // Cart routes: allow guest_session (GET, POST, DELETE, etc.)
            $path = $request->path();
            $isCartRoute = str_starts_with($path, 'api/cart');
            if ($isCartRoute) {
                $guestToken = $request->query('guest_session') ?: $request->header('guest_session');
                if ($guestToken) {
                    $locationId = config('services.b2b.location_id', 1);
                    $guestSession = GuestSession::where('uuid', $guestToken)
                        ->where('location_id', $locationId)
                        ->where('expires_at', '>', now())
                        ->first();
                    if ($guestSession) {
                        $request->attributes->set('current_guest_session', $guestSession);
                        $request->merge(['current_guest_session' => $guestSession]);
                        return $next($request);
                    }
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid or expired guest session.',
                    ], 401);
                }
            }

            if($request->query('elevenlabs_conversation_id')){
                $token_exists = ElevenLabsSessionModel::where('conversation_id', $request->query('elevenlabs_conversation_id'))->first();
                if($token_exists){
                    $request->headers->set('Authorization', 'Bearer ' . $token_exists->token);
                    // validate based on conversation id 
                    try {
                        $user = Auth::guard('api')->user();
                        if (!$user || !$user->isApproved==1) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Unauthenticated.',
                            ], 401);
                        }
                        return $next($request);
                    } catch (OAuthServerException | RequiredConstraintsViolated $e) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Invalid or expired token.',
                        ], 401);
                    }
                }
            }
            return response()->json([
                'status' => false,
                'message' => 'No API token provided.',
            ], 401);
        }
        
        try {
            $user = Auth::guard('api')->user();
            if (!$user || !$user->isApproved==1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }
            return $next($request);
        } catch (OAuthServerException | RequiredConstraintsViolated $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired token.',
            ], 401);
        }
    }
}
