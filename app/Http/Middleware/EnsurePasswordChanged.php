<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            return $next($request);
        }

        if (! $user->password_change_required) {
            return $next($request);
        }

        $hasConfirmedOtp = DB::table('breezy_sessions')
            ->where('authenticatable_type', $user::class)
            ->where('authenticatable_id', $user->id)
            ->where('panel_id', 'admin')
            ->whereNotNull('two_factor_secret')
            ->whereNotNull('two_factor_confirmed_at')
            ->exists();

        if (! $hasConfirmedOtp) {
            return $next($request);
        }

        if (
            ! $request->is('admin/change-password')
            && ! $request->is('livewire/*')
            && ! $request->is('admin/logout')
        ) {
            return redirect('/admin/change-password');
        }

        return $next($request);
    }
}