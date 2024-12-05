<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string("from_email"); // E-mail do remetente
            $table->string("to_email"); // E-mail do destinatário
            $table->foreignId("chat_id")->references("id")->on("chats");
            $table->text('title');
            $table->text("content")->nullable(); // O conteúdo da mensagem pode ser nulo se for apenas uma mídia
            $table->string("media_path")->nullable(); // Caminho do arquivo de mídia
            $table->string("media_type")->nullable(); // Tipo da mídia (imagem/vídeo)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
