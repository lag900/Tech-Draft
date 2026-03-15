<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'client')
            ->with(['client'])
            ->withCount([
                'createdOrders as total_orders',
                'createdOrders as production_orders' => function ($q) {
                    $q->whereIn('status', ['sampling', 'production']);
                },
                'createdOrders as completed_orders' => function ($q) {
                    $q->where('status', 'completed');
                }
            ]);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhereHas('client', function($cq) use ($request) {
                      $cq->where('company_name', 'like', "%{$request->search}%")
                        ->orWhere('brand_name', 'like', "%{$request->search}%");
                  });
            });
        }

        return $query->latest()->get();
    }

    public function show(User $user)
    {
        if ($user->role !== 'client') {
            return response()->json(['message' => 'Not a client'], 404);
        }

        $clientData = $user->load(['client', 'createdOrders' => function($q) {
            $q->latest()->limit(10);
        }])->loadCount([
            'createdOrders as total_orders',
            'createdOrders as production_orders' => function ($q) {
                $q->whereIn('status', ['sampling', 'production']);
            },
            'createdOrders as completed_orders' => function ($q) {
                $q->where('status', 'completed');
            }
        ]);

        // Calculate total units manually since it's in a JSON field
        $totalUnits = 0;
        foreach ($user->createdOrders as $order) {
            $totalUnits += (int)($order->production_details['quantity'] ?? 0);
        }
        $clientData->total_units = $totalUnits;

        return $clientData;
    }

    public function stats()
    {
        $totalClients = User::where('role', 'client')->count();
        $totalOrders = DB::table('orders')->count();
        $ordersInProduction = DB::table('orders')->whereIn('status', ['sampling', 'production'])->count();
        
        return response()->json([
            'total_clients' => $totalClients,
            'total_orders' => $totalOrders,
            'production_orders' => $ordersInProduction,
        ]);
    }
}
