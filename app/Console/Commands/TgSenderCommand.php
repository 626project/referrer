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
            'text' => 'Важные новости ‼️

24 февраля ожидается очередной пакет санкций.

Сейчас нет официальной информации, что именно в него войдёт и какие иностранные банки это может затронуть — возможно, изменения вообще не повлияют на привычные операции.

Но мы считаем важным заранее напомнить: хранить всю валюту в одном месте иногда рискованно, поэтому разумный подход — диверсифицировать средства.

Чтобы вам было проще распределить финансы, мы подготовили для клиентов
*СКИДКУ* 25%
на оформление любых карт (можно выбрать альтернативный банк/страну под ваши задачи).

Если будут вопросы — напишите нам в ответ, мы на связи и поможем выбрать оптимальное решение 🤝️',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'СВЯЗЬ С МЕНЕДЖЕРОМ', 'callback_data'=>'{"action":"call manager"}'],
                    ],
                ],
            ]),
            'disable_web_page_preview' => false,
            'parse_mode' => 'MarkdownV2',
        ];
        $success = 0;
        $error = 0;
        $tg_ids = TgUser::select('tg_id')->distinct()->get();
//        foreach ($tg_ids as $tg_id) {
//            $send_data['chat_id'] = $tg_id->tg_id;
            $send_data['chat_id'] = 316341641;//fixme
            try {
                $result = $this->telegram->sendMessage($send_data);
//                $send_data_file = [
//                    'document' => config('app.url') . '/files/Bankovskie_karty_raznye_strany.pdf',
//                    'chat_id' => $tg_id->tg_id,
//                    'disable_web_page_preview' => false,
//                ];
//                $this->telegram->sendDocument($send_data_file);
                TgMessage::create([
                    'action' => 'sender',
                    'tg_message_id' => $result->getMessageId(),
                    'tg_user_id' => $result->getChat()->getId(),
                    'group_id' => 316341641,//fixme
//                    'group_id' => $tg_id->tg_id,
                ]);
                $success++;
            } catch (Exception $exception) {
//                $this->log('error send: ' . $tg_id->tg_id);
                $this->log('error stacktrace: ' . $exception->getTraceAsString());
                $error++;
            }
            $this->log('success: ' . $success . ', error: ' . $error);
//        }


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
