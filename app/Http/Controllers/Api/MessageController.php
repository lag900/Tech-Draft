<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Message;

class MessageController extends Controller
{
    public function index(Request $request, $orderId)
    {
        return Message::query()->with('sender')->where('order_id', (string) $orderId)->oldest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        return Message::create([
            'order_id' => $request->order_id,
            'sender_id' => $request->user()->id,
            'receiver_id' => $request->receiver_id,
            'content' => $request->input('content'),
        ]);
    }
}
