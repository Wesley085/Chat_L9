<?php

namespace App\Http\Controllers;

use App\Events\SendMessageEvent;
use App\Http\Requests\SendMessageRequest;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;

class ChatController extends Controller
{
    private Chat $chat;

    public function SendMessage(SendMessageRequest $request)
    {
        // Verifica se o usuário está tentando enviar mensagem para si mesmo
        if ($request->to == auth("sanctum")->user()->email) {
            return response()->json(['message' => "Você não pode enviar mensagem para si mesmo."], 400);
        }

        $OtherUserEmail = $request->to;

        $collection = $this->IsTherePreviousChat($OtherUserEmail, auth("sanctum")->user()->email);

        if ($collection == false) {
            $chat = Chat::create([
                'user_id' => auth("sanctum")->user()->id,
            ]);
        }

        // Salvar o arquivo de mídia, se enviado
        $mediaPath = null;
        $mediaType = null;

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $mediaPath = $file->store('messages', 'public'); // Armazena o arquivo na pasta 'storage/app/public/messages'
            $mediaType = $file->getMimeType(); // Obtém o tipo MIME do arquivo
        }

        // Define o título da mensagem
        $title = $request->input('title') ?? substr($request->message, 0, 30) . '...';  

        // Cria a mensagem
        $message = Message::create([
            'from_email' => auth("sanctum")->user()->email,
            'to_email'   => $OtherUserEmail,
            'title'      => $title,
            'content'    => $request->message,
            'chat_id'    => $collection == false ? $chat->id : $collection[0]->chat_id,
            'media_path' => $mediaPath,
            'media_type' => $mediaType,
        ]);

        // Envia a mensagem para outros usuários conectados
        broadcast(new SendMessageEvent($message->toArray()))->toOthers();

        return response()->json(['message' => 'Mensagem enviada com sucesso.'], 200);
    }

    public function IsTherePreviousChat($OtherUserEmail, $userEmail)
    {
        // Verifica se há mensagens existentes no chat entre os dois e-mails
        $collection = Message::where('from_email', $OtherUserEmail)
            ->where('to_email', $userEmail)
            ->orWhere(function ($q) use ($OtherUserEmail, $userEmail) {
                $q->where('from_email', $userEmail)
                    ->where('to_email', $OtherUserEmail);
            })->get();

        return count($collection) > 0 ? $collection : false;
    }
}
