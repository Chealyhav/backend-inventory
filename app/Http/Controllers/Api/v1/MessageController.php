<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        return Message::with('user')->latest()->take(50)->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'sender_id' => 'required|integer', // Make sure this is required!
        ]);

        $message = Message::create([
            'message' => $request->message,
            'sender_id' => $request->sender_id,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['message' => $message]);
    }

    public function seen($id)
    {
        $message = Message::findOrFail($id);
        $message->seen_at = now();
        $message->save();

        broadcast(new Message($message->id, $message->seen_at))->toOthers();

        return response()->json(['success' => true]);
    }
}
