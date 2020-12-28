<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\NewChatMessage;
class ChatController extends Controller
{
    public function rooms(Request $request)
    {
        return ChatRoom::all();
    }

    public function messages(Request $request, $roomId)
    {
        // return ChatMessage::where('chat_room_id','$roomId')
        //                 ->with('user')
        //                 ->orderBy('created_at', 'DESC')
        //                 ->get();

        $messages = DB::table('chat_messages')
                   ->join('users', 'users.id','=','chat_messages.user_id')
                   ->select('users.name','chat_messages.*')
                   ->where('chat_messages.chat_room_id',$roomId)
                   ->get();

        return response()->json($messages);
    }

    public function newMessage(Request $request, $roomId)
    {
        $newMessage = new ChatMessage();
        $newMessage->user_id = Auth::id();
        $newMessage->chat_room_id = $roomId;
        $newMessage->message = $request->message;
        $newMessage->save();

        broadcast(new NewChatMessage( $newMessage ))->toOthers();
        return $newMessage;
    }
}
