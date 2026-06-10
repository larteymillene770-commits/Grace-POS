<?php
namespace App\Http\Middleware;

use Closure;

class CheckPermission
{
    public function handle($request, Closure $next, $perm)
    {
        if (env('RBAC_ENFORCE', false) === false) {
            return $next($request);
        }

        $user = $request->user();
        if (!$user) return response()->json(['message'=>'Unauthorized'], 401);

        $perms = cache()->remember("u:{$user->id}:perms", 300, fn() => $user->permissions());

        $aliases = [
            'cars.delete'=>'items.delete',
            'pos.new_sale'=>'sales.pos.create',
            'sale.list'=>'sales.list.view',
            'sale.check'=>'sales.check',
            'sale.item'=>'sales.item.view',
            'customer.check_ledger'=>'customers.ledger.view',
            'supplier.check_ledger'=>'suppliers.ledger.view',
            'product.inventory_ledger'=>'inventory.ledger.view',
            'purchase.ledger'=>'purchases.ledger.view',
            'due.remitter'=>'cash.due_remit',
            'reports.pnl'=>'reports.pnl.view',
            'reports.gst'=>'reports.gst.view',
            'filters.manage'=>'settings.filters.manage'
        ];
        $key = $aliases[$perm] ?? $perm;

        if (!in_array($key, $perms)) {
            return response()->json(['message'=>'Forbidden','required'=>$key], 403);
        }
        return $next($request);
    }
}
