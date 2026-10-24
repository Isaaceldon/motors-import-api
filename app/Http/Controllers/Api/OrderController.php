<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SparePart;
use App\Notifications\OrderConfirmationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.spare_part_id' => ['required', 'exists:spare_parts,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $order = DB::transaction(function () use ($data, $request) {
            $totalUsd = 0;
            $totalFcfa = 0;
            $items = [];

            foreach ($data['items'] as $item) {
                $sparePart = SparePart::findOrFail($item['spare_part_id']);

                if (! $sparePart->online_sale_enabled) {
                    throw ValidationException::withMessages([
                        'items' => ["\"{$sparePart->name}\" n'est pas disponible à la vente en ligne."],
                    ]);
                }

                if ($sparePart->stock_quantity < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => ["Stock insuffisant pour \"{$sparePart->name}\"."],
                    ]);
                }

                $totalUsd += $sparePart->price_usd * $item['quantity'];
                $totalFcfa += $sparePart->price_fcfa * $item['quantity'];

                $items[] = [
                    'spare_part_id' => $sparePart->id,
                    'quantity' => $item['quantity'],
                    'unit_price_usd' => $sparePart->price_usd,
                    'unit_price_fcfa' => $sparePart->price_fcfa,
                ];

                $sparePart->decrement('stock_quantity', $item['quantity']);
            }

            $order = Order::create([
                'user_id' => $request->user('sanctum')?->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'status' => 'en_attente',
                'total_usd' => $totalUsd,
                'total_fcfa' => $totalFcfa,
            ]);

            $order->items()->createMany($items);

            return $order;
        });

        $order->load('items.sparePart');

        $user = $request->user('sanctum');

        if ($user) {
            $user->notify(new OrderConfirmationNotification($order));
        } else {
            Notification::route('mail', $order->customer_email)
                ->notify(new OrderConfirmationNotification($order));
        }

        return response()->json($order, 201);
    }

    public function index(Request $request)
    {
        $orders = Order::query()
            ->with('items.sparePart.photos')
            ->when($request->user('sanctum'), fn ($q) => $q->where('user_id', $request->user('sanctum')->id))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $orders;
    }
}
