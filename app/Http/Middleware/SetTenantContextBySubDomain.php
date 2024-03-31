<?php

namespace App\Http\Middleware;

use App\Facades\Context;
use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContextBySubDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Context::hasTenant()) {
            /**
             * lighthouse-graphql-passport-authが提供するGraphQL Resolverは内部でREST APIを呼び出すため、再度このミドルウェアが呼び出される。
             * そのときRequestのhostはlocalhost固定になるため、エラーにならないようにContextが既に設定されている場合は以降の処理をスキップする。
             */
            return $next($request);
        }

        $host = $request->getHost();
        $subdomain = $this->getSubdomain($host);
        abort_unless($subdomain, 500, "{$host} is unexpected domain.");

        $tenant = Tenant::whereSubdomain($subdomain)->first();
        abort_unless($tenant, 404, "テナント（{$subdomain}）が見つかりません。");

        return Context::callWithTenant($tenant, fn () => $next($request));
    }

    protected function getSubdomain(string $host): string
    {
        $hostParts = explode('.', $host);

        array_pop($hostParts);

        return implode('.', $hostParts);
    }
}
