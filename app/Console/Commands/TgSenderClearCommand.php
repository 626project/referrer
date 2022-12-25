<?php

namespace App\Console\Commands;

use App\Http\Controllers\TgBotController;
use App\Models\TgMessage;
use App\Models\TgUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Telegram\Bot\Api;

class TgSenderClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:tg_sender_clear_command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Telegram sender clear command';

    private $telegram;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->telegram = new Api(TgBotController::TOKEN);
        $this->log('start');
        $deleted_count = 0;
        $tg_messages = TgMessage::where(['action' => 'sender'])
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->get();
        foreach ($tg_messages as $tg_message) {
            $this->log('success delete: ' . $tg_message->id);
            $this->delete_message($tg_message->tg_user_id, $tg_message->tg_message_id);
            $deleted_count++;
        }

        $this->log('success delete: ' . $deleted_count);


        $this->log('finish');
    }

    /**
     * @param $chat_id
     * @param $message_id
     * @return false|string
     */
    private function delete_message($chat_id, $message_id)
    {
        $api_uri = 'https://api.telegram.org/bot' . TgBotController::TOKEN . '/deleteMessage?'
            . 'chat_id=' . $chat_id
            . '&message_id=' . $message_id;

        return file_get_contents($api_uri);
    }

    /**
     * @param $text
     * @param array $params
     */
    private function log($text, array $params = [])
    {
        info('[Command:tg_sender_clear_command]: ' . vsprintf($text, $params));
    }
}
