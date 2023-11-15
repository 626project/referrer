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
            'text' => '🔥Ввиду возможных ужесточений мер по открытию карт Казахстана для нерезидентов, мы расширяем спектр наших возможностей!
Теперь у нас вы можете оформить банковские карты не только Казахстана, а еще и:
✔Киргизия -Бакай банк 💳35000р*
✔Киргизия - Бай-Тумуш банк 29500р*
✔Банк N (банк средней Азии, название страны и банка держится в секрете🤫) очень хорошая по функционалу карта - 35000р*
✔Турция - TravelWorldCard - 31000р*

*цена по акции! С декабря повышение цен по новым странам!

В прикрепленном сообщении можно увидеть список всех карт, доступных к оформлению сейчас, подробное описание и получить бесплатную консультацию можно запросить у менеджера 📲

Что оставить заявку на карту, пиши менеджеру: хочу карту! ❤',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'СВЯЗЬ С МЕНЕДЖЕРОМ', 'callback_data'=>'{"action":"call manager"}'],
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
                $send_data = [
                    'document' => config('app.url') . '/files/Bankovskie_karty_raznye_strany.pdf',
                    'chat_id' => $tg_id->tg_id,
                    'disable_web_page_preview' => false,
                ];
                $this->telegram->sendDocument($send_data);
                TgMessage::create([
                    'action' => 'sender',
                    'tg_message_id' => $result->getMessageId(),
                    'tg_user_id' => $result->getChat()->getId(),
                    'group_id' => $tg_id->tg_id,
                ]);
                $success++;
            } catch (Exception $exception) {
                $this->log('error send: ' . $tg_id->tg_id);
                $this->log('error stacktrace: ' . $exception->getTraceAsString());
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
