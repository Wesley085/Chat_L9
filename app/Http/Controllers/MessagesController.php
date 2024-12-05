<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function LoadThePreviousMessages(Request $request)
    {
        if ($request->has('chat_id')) {
            // Se o request contém chat_id, carregue as mensagens do chat
            return Chat::where("id", $request->chat_id)->with(["messages" => function($q) use ($request) {
                $q->where("messages.chat_id", $request->chat_id)->orderBy("id", "asc");
            }])->get();
        }

        return Message::where(function ($query) use ($request) {
            $query->where('from_email', auth("sanctum")->user()->email)
                  ->where('to_email', $request->other);
        })->orWhere(function ($query) use ($request) {
            $query->where('from_email', $request->other)
                  ->where('to_email', auth("sanctum")->user()->email);
        })->orderBy('created_at', 'ASC')->limit(10)->get();
    }

    public function deleteMessage($id)
    {
        // Busca a mensagem pelo ID
        $message = Message::findOrFail($id);

        if ($message->from_email !== auth("sanctum")->user()->email) {
            return response()->json(['message' => 'Você não tem permissão para excluir esta mensagem.'], 403);
        }

        $message->delete();

        return response()->json(['message' => 'Mensagem excluída com sucesso.'], 200);
    }
}
