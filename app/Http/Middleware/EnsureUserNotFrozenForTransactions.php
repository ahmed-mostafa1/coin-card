<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserNotFrozenForTransactions
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->is_frozen) {
            return redirect()->back()
                ->withInput()
                ->with('status', 'حسابك مجمّد مؤقتًا ولا يمكنك إجراء عمليات حالياً.');
        }

        return $next($request);
    }
}
