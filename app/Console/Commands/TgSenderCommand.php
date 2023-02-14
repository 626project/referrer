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
            'text' => 'Новость №1 - Гражданство Гренады!!! 🇬🇩
Что дает это гражданство:
💥Въезд в более 140 стран мира без визы, включая Европу и Великобританию!
💥Виза Е-2 в США на 10 лет
💥Сохранность капитала, оптимизация налогов
💥Гражданство передается поколениям
💥Можно получить гражданство для всей семьи, включая родителей, братьев и сестер
💥Возможно иметь двойное гражданство
💥Весь процесс дистанционно, не нужно жить в Гренаде

Новость №2 -  Платежный агент для бизнеса😎
💥Актуально для всех компаний, кто столкнулся с проблемами в расчетах с зарубежными партнерами и клиентами.
💥Вам не нужно открывать компанию для ваших взаиморасчетов, мы возьмем это на себя!

Чтобы получить консультацию и презентацию по новым услугам, жми кнопку СВЯЗАТЬСЯ С МЕНЕДЖЕРОМ 🔽',
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
//        $tg_ids = TgUser::select('tg_id')->distinct()->get();
//        foreach ($tg_ids as $tg_id) {
            $send_data['chat_id'] = 316341641;
            try {
                $result = $this->telegram->sendMessage($send_data);
//                TgMessage::create([
//                    'action' => 'sender',
//                    'tg_message_id' => $result->getMessageId(),
//                    'tg_user_id' => $result->getChat()->getId(),
//                    'group_id' => $tg_id->tg_id,
//                ]);
                $success++;
            } catch (Exception $exception) {
                $this->log('error send: ' . $tg_id->tg_id);
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
