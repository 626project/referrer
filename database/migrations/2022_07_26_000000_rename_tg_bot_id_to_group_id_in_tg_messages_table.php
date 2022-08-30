<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTgBotIdToGroupIdInTgMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tg_messages', function (Blueprint $table) {
            $table->bigInteger('tg_bot_id')->nullable()->change();
            $table->renameColumn('tg_bot_id', 'group_id');
        });
    }
}
