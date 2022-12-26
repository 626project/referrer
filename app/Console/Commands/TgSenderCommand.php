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
            'text' => '🔥Вы просили-мы сделали!🔥

Спрос рождает предложение, и именно вы-наши клиенты задаёте направление нашего развития!🔝

Все наши клиенты люди умные, деловые, все развиваются и выходят на Новый уровень - международный!!!🌏

Поэтому не будем томить!
В январе мы запускаем 3 новые услуги!!!
1️⃣Дистанционное открытие  юридического лица и сопровождение в Казахстане
2️⃣Резидентство в ОАЭ, без приобретения недвижимости
3️⃣Открытие расчетного счета в ОАЭ физ.лица по личному присутствию
Поставим и реализуем вместе  амбициозные цели в 2023 году!🔥🔥🔥

Остались вопросы⁉️
Хочешь подать заявку⁉️
🔽🔽🔽',
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
