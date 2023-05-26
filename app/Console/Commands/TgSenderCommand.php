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
            'text' => '🔥РЕКОРДНО НИЗКАЯ ЦЕНА 4⃣5⃣0⃣0⃣0⃣ рублей В ЧЕСТЬ НАШЕГО ДНЯ РОЖДЕНИЯ!🔥
За год работы мы:
✅Открыли более 5000 карт
✅Выполнили свои обязательства перед всеми клиентами на 100%!
✅Запустили комплекс услуг в Казахстане, ОАЭ и Турции

🔥Цена по акции действует только 1 день - сегодня!
❗Количество мест ограничено❗

P.S.
🔸Возможно открытие карты за 2-3 дня⚡зависит от Вашей ситуации
🔸Мы можем решить любую нестандартную ситуацию 😎

✅Чтобы забрать место по акции: надо написать менеджеру «АКЦИЯ ДР!».
И отправить скан загранпаспорта',
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
