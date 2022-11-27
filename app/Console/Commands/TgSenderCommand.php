<?php

namespace App\Console\Commands;

use App\Http\Controllers\TgBotController;
use App\Models\TgMessage;
use App\Models\TgUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Telegram\Bot\Api;

class TgSenderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:tg_sender_command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Telegram sender command';

    private $telegram;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->telegram = new Api(TgBotController::TOKEN);
        $this->log('start');

        $send_data = [
            'text' => 'Такого у нас ещё не было…

🔥Скидка 10000 рублей!!! 🔥
Черная пятница в самом разгаре 💣

При оформлении до 1 декабря, дарим скидку 10000 рублей!
Количество мест со скидкой ограничено, поэтому не упусти свой шанс!

Для оформления пиши сюда ЧЕРНАЯ ПЯТНИЦА🔽',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Связаться с менеджером', 'callback_data'=>'{"action":"call manager"}'],
                    ],
                ],
            ]),
            'disable_web_page_preview' => false
        ];
        $success = 0;
        $error = 0;
        $tg_ids = TgUser::select('tg_id')->distinct()->get();
        foreach ($tg_ids as $tg_id) {
            $send_data['chat_id'] = $tg_id->tg_id;
            try {
                $result = $this->telegram->sendMessage($send_data);
                TgMessage::create([
                    'action' => 'sender',
                    'tg_message_id' => $result->getMessageId(),
                    'tg_user_id' => $result->getChat()->getId(),
                    'group_id' => $tg_id->tg_id,
                ]);
                $success++;
            } catch (Exception $exception) {
                $this->log('error send: ' . $tg_id->tg_id);
                $error++;
            }
            $this->log('success: ' . $success . ', error: ' . $error);
        }


        $this->log('finish');
    }

    /**
     * @param $text
     * @param array $params
     */
    private function log($text, array $params = [])
    {
        info('[Command:tg_sender_command]: ' . vsprintf($text, $params));
    }
}
