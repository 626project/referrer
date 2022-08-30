<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTgMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tg_messages', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('action');
            $table->bigInteger('tg_message_id');
            $table->bigInteger('tg_user_id');
            $table->bigInteger('tg_bot_id');
            $table->softDeletes();
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
        Schema::dropIfExists('tg_messages');
    }
}
