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
            'text' => 'Добрый день !

В честь нашего дня рождения, оформляем карту бесплатно.

Одним из условий бесплатного оформления является подписка и репост последнего рилс « бесплатная карта» у себя в сторис на 24 часа .
Ссылка на наш инстаграмм https://www.instagram.com/alternative_assistance?igsh=bmVmN3U0dnRzeG50

Для ознакомления.
Бесплатное открытие иностранной карты 💳
Карта не именная.
Мультивалютная карта Master Card Standart в 4х валютах: USD, EUR, RUB, KZT.

Преимущества:
☑ Оплата товаров и услуг по всему миру
☑ Apple Pay/Samsung Pay
☑ Удобное приложение банка с возможностью конвертации между своими счетами.
☑ Интеграция с российской сим-картой.
☑ Исходящие (с ограничениями) и входящие SWIFT-переводы.
☑ Читается как CREDIT и подходит для RENT CAR.
☑ Бесплатное годовое обслуживание карты.
При первом пополнении счета единоразово списывается комиссия за открытие карты 30 000 тенге (около 6 000 руб., зависит от курса рубль/тенге).
❌ Нежелательно пополнение счетов криптовалютой. Возможна блокировка счетов..

Для оформления карты нужен ИИН . Стоимость дистанционного выпуска 5 тыс руб

Так же хотим обратить ваше внимание, что любой банк в праве отказать в открытии счета ( без объяснения причин ) , такое бывает крайне редко в 1% из 100 . В этом случае деньги за оформление ИИН Казахстана , мы не возвращаем ( тк данная услуга выполнена успешна , и в дальнейшем может вам так же пригодится для открытия счета в другом банке и тд )

Если вы готовы войти в оформление , пишите менеджеру',
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
                    //'group_id' => $tg_id->tg_id,
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
