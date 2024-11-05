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
            'text' => '🔥Последний шанс получить 2-е гражданство 🔥

Если для вас актуален вопрос 2-го гражданства, то это информация для Вас :
-страна Вануату 🇻🇺
-срок оформления -1 месяц
-без личного присутствия
-стоимость -135/150 тыс $ ( зависит от состава семьи)

‼️Вануату закрывает программу для Россиян  уже в ноябре ‼️

Дистанционно подать документы можно  до 10.11.2024, паспорт будет готов уже 10.12.2024


Главные плюсы паспорта ВАНУАТУ 🇻🇺
1️⃣ Отсутствие дискриминации по паспорту
2️⃣ Свобода перемещений - 103 страны без визы*
*ожидается возвращение стран Евросоюза в список безвизовых стран
3️⃣ Открытие банковских счетов зарубежом без санкционных условий
4️⃣ Трамплин для следующих паспортов ( Евросоюз …)
5️⃣ Отсутсвие мирового налога
6️⃣ Виза в США на 10 лет

Готовы ответить на ваши вопросы и дать развернутую консультацию ❤️',
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
//                    'group_id' => 316341641,//fixme
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
