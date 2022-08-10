<?php

namespace App\Http\Controllers;

use App\Models\ReferrerLink;
use App\Models\ReferrerRedirect;
use App\Models\TgUser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Telegram\Bot\Api;

class TgBotController extends Controller
{
    const TOKEN = '5402169870:AAGd1B4gqLVz_1F6pLWVjh5fBVXuOadRqgw';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @return void
     */
    public function index()
    {
        $input_data = json_decode(file_get_contents('php://input'), TRUE);
        // log input data
        info('input data: ' . print_r($input_data, 1));

        if (isset($input_data['callback_query'])) {
            $message = $input_data['callback_query']['message'];
            $chat_id = $message['chat']['id'];
            $data = isset($input_data['callback_query']['data']) ? json_decode($input_data['callback_query']['data'], true) : [];
            $action = isset($data['action']) ? $data['action'] : '';
        } else {
            $message = $input_data['message'];
            $chat_id = $input_data['message']['chat'] ['id'];
            $action = isset($input_data['message']['text']) ? $input_data['message']['text'] : $input_data['message']['data'];
        }
        $action = mb_strtolower($action);
        info('action: ' . print_r($action, 1));
        $disable_web_page_preview = false;
        if (strripos($action, "start")) {
            $link_id = str_replace('/start ', '', $action);
            info('link_id: ' . print_r($link_id, 1));
            $action = '/start';
        } else {
            $tg_user = TgUser::where(['tg_id' => $input_data['message']['from']['id']])
                ->orderBy('id', 'DESC')
                ->first();
            $link_id = $tg_user->link_id;
        }
        TgUser::create([
            'link_id' => $link_id,
            'tg_id' => $message['from']['id'] ?? 0,
            'username' => $message['from']['username'] ?? '',
            'first_name' => $message['from']['first_name'] ?? '',
            'last_name' => $message['from']['last_name'] ?? '',
            'phone' => $message['from']['phone'] ?? '',
            'last_action' => $action,
        ]);

        switch ($action) {
            case '/start':
                $send_data = [
                    'text' => 'На данный момент мы можем предложить вам открытие в двух банках Казахстана:

1️⃣ вариант:
Мультивалютная карта в 4 валютах (доллар, евро, рубль, тенге) открывается дистанционно, бесплатное годовое обслуживание.
Простое и удобное приложение банка с возможностью конвертации между своими счетами. Интеграция с российскими сим-картами, в момент открытия счёта на ваш российский номер телефона придёт смс с доступом к банк-клиенту, вы сразу увидите  счета, номер карты, сможете подключить ее к Apple Pay.
Полноценная международная карта Master card с исходящим и входящим SWIFT.
Читается как CREDIT и подходит для RENT CAR.
Ознакомиться более подробно со всеми тарифами банка можно, запросив их у менеджера.  Получение  пластиковой карты осуществляется  в течении 2 недель, карта отправляется на ваш адрес по России (сроки доставки зависит от региона и ТК). Для оформления нужен цветной скан загранпаспорта (со сканера) + ИИН (индивидуальный идентификационный номер). Стоимость дистанционного открытия ИИН, банковского счёта, доставка до крупных городов - 60 тыс .рублей*

2️⃣ вариант:
 Мультивалютная карта в 4 валютах (доллар, евро, рубль, тенге) открывается дистанционно, бесплатное годовое обслуживание при поддержании остатков на счете (депозиты, текущие счета ..) 40 тыс долларов, при снижении остатков комиссия 10тыс.тенге/месяц (около 1200-1500 руб - зависит от курса рубль/тенге). Выпуск металической карты по тарифам банка 30 тыс тенге (около 4000 рублей - зависит от курса рубль/тенге).
VIP тариф, куда входит персональный менеджер, 1200 бизнес залов в аэропортах по всему миру, кэш бэк 4%, биржевой курс Forex на конвертацию.
Простое и удобное приложение банка с возможностью конвертации между своими счетами. Активация интернет банка осуществляется с казахстанской сим-карты (предоставляется в комплекте с картой банка).
Полноценная международная карта VISA с исходящим и входящим SWIFT.
Ознакомиться более подробно со всеми тарифами банка можно, запросив их у менеджера.  Получение  пластиковой карты осуществляется  в течении 2 недель, карта отправляется на ваш адрес по России (сроки доставки зависит от региона и ТК). Для оформления нужен цветной скан загранпаспорта (со сканера) + ИИН (индивидуальный идентификационный номер). Стоимость дистанционного открытия ИИН, банковского счёта, доставка до крупных городов - 60 тыс .рублей*

*Оплата делится на 2 платежа: 50% предоплата в момент предоставления скана занранпаспорта и 50% по факту открытия счета.',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => 'Мне подходит вариант 1', 'callback_data'=>'{"action":"variant 1"}'],
                                ['text' => 'Мне подходит вариант 2', 'callback_data'=>'{"action":"variant 2"}'],
                            ],
                            [
                                ['text' => 'Связаться с менеджером', 'callback_data'=>'{"action":"call manager"}'],
                                ['text' => 'Частые вопросы', 'callback_data'=>'{"action":"faq"}'],
                            ],
                            [
                                ['text' => 'Отзывы', 'callback_data'=>'{"action":"reviews"}'],
                                ['text' => 'Акция', 'callback_data'=>'{"action":"stock"}'],
                                ['text' => 'Договор оферта', 'callback_data'=>'{"action":"show offer"}'],
                            ],
                        ],
                    ]),
                ];
                break;
            case 'variant 1':
            case 'variant 2':
            case 'call manager':
            case 'which banks':
            case 'i am ready':
            case 'want a card':
            case 'want a card. not resident':
            case 'want a card. have inn':
            case 'your question':
            case 'participation in the action':
                // todo отправить сообщение с данными о пользователе
                $send_data = [
                    'parse_mode' => 'HTML',
                    'text' => '<a href="https://t.me/AlternativeAssistance">Переход на менеджера</a>',
                ];
                break;
            case 'faq':
                $send_data = [
                    'text' => 'В этом разделе мы собрали наиболее распространенные вопросы, если у вас есть дополнительные вопросы, то вы можете нажать кнопку связаться с менеджером и мы с радостью проконсультируем вас',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => 'Какие документы нужны для оформления карты?', 'callback_data'=>'{"action":"which documents"}'],
                            ],
                            [
                                ['text' => 'Какие банки в предложенных вариантах?', 'callback_data'=>'{"action":"which banks"}'],
                            ],
                            [
                                ['text' => 'Как я смогу пополнять карту?', 'callback_data'=>'{"action":"card replenishment"}'],
                            ],
                            [
                                ['text' => 'Как оплачиваются ваши услуги?', 'callback_data'=>'{"action":"payment for services"}'],
                            ],
                            [
                                ['text' => 'Я не резидент РФ. Могу ли я получить карту?', 'callback_data'=>'{"action":"not a resident"}'],
                            ],
                            [
                                ['text' => 'У меня уже есть иин, какая цена в этом случае?', 'callback_data'=>'{"action":"have inn"}'],
                            ],
                            [
                                ['text' => 'Моего вопроса нет в этом списке, хочу задать свой вопрос.', 'callback_data'=>'{"action":"your question"}'],
                            ],
                        ],
                    ]),
                ];
                break;
            case 'stock':
                $send_data = [
                    'text' => 'Акция на Открытие банковской карты в Казахстане. Услуга вызвала большой отклик, но цена не всем подошла.
Мы решили 1 раз в неделю делать специальное предложение для 5-10 человек со скидкой 25-35%. Стоимость карты будет составлять 40-45 тыс руб.
Цену по акции получат первые откликнувшиеся на наше сообщение . Если вам Интересно, нажмите кнопку  ХОЧУ УЧАСТВОВАТЬ  и мы уведомим вас при старте акции!',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => 'Хочу участвовать в акции', 'callback_data'=>'{"action":"participation in the action"}'],
                            ],
                            [
                                ['text' => 'вернуться назад', 'callback_data'=>'{"action":"go back"}'],
                            ],
                        ],
                    ]),
                ];
                break;
            case 'show offer':
                // todo отправка файла на договор оферты
                $send_data = ['text' => 'Договор офераты пдф'];
                break;
            case 'which documents':
                $send_data = [
                    'text' => 'Для оформления карты вы должны предоставить скан загранпаспорта. Разворот с фото, именно со сканера',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => 'Хочу карту!  Отправить скан паспорта', 'callback_data'=>'{"action":"send a scan of your passport"}'],
                            ],
                            [
                                ['text' => 'вернуться назад', 'callback_data'=>'{"action":"go back"}'],
                            ],
                        ],
                    ]),
                ];
                break;
            case 'send a scan of your passport':
                $send_data = ['text' => 'Отправьте скан паспорта для продолжения'];
                break;
            case 'go back':
                // todo удаление лишних сообщений,
                $send_data = ['text' => '...'];
                break;
            case 'card replenishment':
                $send_data = [
                    'text' => 'Способы и лимиты пополнения:
1️⃣ Пополнение карты возможно через платежное поручение на перевод рублей с любого банка РФ, без ограничений по сумме.
2️⃣ Со своего счёта в российском банке на свой счёт или другому физическому лицу зарубежом не более 1 млн. долларов США или эквиваленте в другой валюте в течение календарного месяца. Пополнение карты возможно при помощи SWIFT перевода с российского счета на Ваш счёт в банке Казахстана. Подойдет любой банк, не попавший под отключение SWIFT. Перевод осуществляется через сотрудников банка отправителя.
3️⃣ Через Золотую корону
•Минимальная сумма одного перевода 5 000 тенге или эквивалент в другой валюте;
• Максимальная сумма одного перевода 2 500 долларов США или эквивалент в другой валюте;
• Максимальная сумма двух переводов за 1 календарный день 5 000 долларов США или эквивалент в другой валюте;
• Максимальная сумма трех переводов за 7 календарных дней 7 500 долларов США или эквивалент в другой валюте;
• Максимальная сумма 10 переводов за 30 календарных дней 15 000 долларов США или эквивалент в валюте.
4️⃣ Через любую удобную вам систему денежных переводов (Western Union, Qiwi, Юнистрим и другие)
5️⃣ Сервис «моментального зачисления» платежей только для наших клиентов. Подробности можно уточнить у менеджера.
',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => 'Готов оформить!', 'callback_data'=>'{"action":"i am ready"}'],
                                ['text' => 'вернуться назад', 'callback_data'=>'{"action":"go back"}'],
                            ],
                        ],
                    ]),
                ];
                break;
            case 'payment for services':
                $send_data = [
                    'text' => 'Условия оплаты:
1. часть: Предоплата 50%. Оплачиваете в момент предоставления скана паспорта.
2. часть: Оплата 50%. Оплачиваете по факту открытия счета.',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => 'По условиям оплаты все отлично. Хочу Карту!', 'callback_data'=>'{"action":"want a card"}'],
                            ],
                            [
                                ['text' => 'вернуться назад', 'callback_data'=>'{"action":"go back"}'],
                            ],
                        ],
                    ]),
                ];
                break;
            case 'not a resident':
                $send_data = [
                    'text' => 'Да, если вы резидент стран СНГ. В этом случае от вас нужен дополнительно нотариально заверенный в Казахстане перевод на русский язык вашего загранпаспорта. Эту услугу оказывают наши партнеры*',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => 'Я не резидент РФ и мне это подходит. Хочу карту!', 'callback_data'=>'{"action":"want a card. not resident"}'],
                            ],
                            [
                                ['text' => 'вернуться назад', 'callback_data'=>'{"action":"go back"}'],
                            ],
                        ],
                    ]),
                ];
                break;
            case 'have inn':
                $send_data = [
                    'text' => 'Стоимость открытия для вас составит 55 тыс. рублей.',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => 'У меня уже есть иин и мне это подходит. Хочу карту!', 'callback_data'=>'{"action":"want a card. have inn"}'],
                            ],
                            [
                                ['text' => 'вернуться назад', 'callback_data'=>'{"action":"go back"}'],
                            ],
                        ],
                    ]),
                ];
                break;
            case 'reviews':
                $send_data = [
                    'text' => 'В этом разделе представлены отзывы. Мы решили показать вам отзывы реальных людей, у которых есть аудитория и которые делились нашим сервисом в своих социальных сетях',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => 'вернуться назад', 'callback_data'=>'{"action":"go back"}'],
                            ],
                        ],
                    ]),
                ];
                break;
            default:
                $send_data = [
                    'text' => 'Команда не найдена. Повторите попытку',
                ];
        }
        $send_data['chat_id'] = $chat_id;
        $send_data['disable_web_page_preview'] = $disable_web_page_preview;

        self::sendTelegram($send_data);
    }

    /**
     * @param $data
     * @return void
     */
    private function sendTelegram($data)
    {
        $telegram = new Api(self::TOKEN);
        $telegram->sendMessage($data);
    }

    /**
     * Show the application dashboard.
     *
     * @param string $code
     * @return Application|RedirectResponse|Redirector|void
     */
    public function invite(string $code)
    {
        $referrer_link = ReferrerLink::where([
            'link' => config('app.url') . '/invite/' . $code,
        ])->first();
        if (!$referrer_link) {
            return redirect('login');
        }
        $referrer_link->increment('count');
        $ip = $_SERVER['REMOTE_ADDR'];
        $referrer_uniq_redirect_count = ReferrerRedirect::where([
            'ip' => $ip,
            'referrer_link_id' => $referrer_link->id,
        ])->count();
        if ($referrer_uniq_redirect_count === 0) {
            $referrer_link->increment('uniq_count');
        }
        ReferrerRedirect::create([
            'ip' => $ip,
            'referrer_link_id' => $referrer_link->id,
        ]);

        return redirect('http://t.me/Info24PlatformBot?start=' . $referrer_link->id);
//        return redirect('http://t.me/AlternativeAssistance');
    }
}
