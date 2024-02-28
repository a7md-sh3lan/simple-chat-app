<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MessageController extends Controller
{
    protected $encrypter;

    public function __construct()
    {
        $key = 'bVYUOaaKDFk/B5iDgbKSf267+JOjgLQuz1NSGQOEnGc=';
        $this->encrypter = new Encrypter(base64_decode($key), 'AES-256-CBC');
    }

    public function index()
    {
        $user = Auth::id();

        $sessions = User::where('id', '!=', $user)->get();

        $sessions->map(function ($session) use($user) {
            $session->last_message = 'Start Chat';

            $user1_id = $session->id;
            $user2_id = $user;

            $last_message = Message::where(function ($query) use ($user1_id, $user2_id) {
                                        $query->where('sender_id', $user1_id)
                                            ->where('receiver_id', $user2_id);
                                    })
                                    ->orWhere(function ($query) use ($user1_id, $user2_id) {
                                        $query->where('sender_id', $user2_id)
                                            ->where('receiver_id', $user1_id);
            })->latest()->first();

            if($last_message) {
                $session->last_message = $this->encrypter->decrypt($last_message->content);
            }
        });

        $messages = Message::where('sender_id', $user)
            ->orWhere('receiver_id', $user)
            ->with('sender', 'receiver')
            ->get();

        $messages->map(function ($message) {
            // Decrypt the message attribute
            $message->message = $this->encrypter->decrypt($message->content);

            // You can perform other operations on the message here

            return $message;
        });

        // Group messages based on the other user (sender or receiver)
        $groupedMessages = $messages->groupBy(function ($message) use ($user) {
            return $message->sender_id == $user ? $message->receiver_id : $message->sender_id;
        });

        return response()->json(['user' => Auth::user(), 'chats' => $groupedMessages, 'users' => $sessions]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validatedData['receiver_id'],
            'content' => $this->encrypter->encrypt($validatedData['content']),
        ]);

        $message->created_at = $message->created_at->format('Y-m-d h:i A');
        $message->name = Auth::user()->name;

        // event(new MessageEvent($validatedData['content'], Auth::id(), $validatedData['receiver_id']));
        Http::post('http://localhost:3001/message',['message' => $validatedData['content'],'sender_id' => Auth::id(), 'receiver_id' => $validatedData['receiver_id']]);

        return response()->json($message, 201);
    }

    public function chatHistory(Request $request, $id)
    {
        $user1_id = Auth::id();

        $user2_id = $id;

        $chat = Message::where(function ($query) use ($user1_id, $user2_id) {
                            $query->where('sender_id', $user1_id)
                                ->where('receiver_id', $user2_id);
                        })
                        ->orWhere(function ($query) use ($user1_id, $user2_id) {
                            $query->where('sender_id', $user2_id)
                                ->where('receiver_id', $user1_id);
                })->get();

        $chat->map(function ($message) {
                    // Decrypt the message attribute
                    $message->message = $this->encrypter->decrypt($message->content);
                    $message->created_at = $message->created_at->format('Y-m-d h:i A');
                    $message->name = User::find($message->sender_id)->name;
        
                    // You can perform other operations on the message here
        
                    return $message;
                });

        
        return response()->json(['chat_history' => $chat]);
    }
}

