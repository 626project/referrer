<?php

namespace App\Http\Controllers;

use App\Models\ReferrerLink;
use App\Models\TgMessage;
use App\Models\ReferrerRedirect;
use App\Models\TgUser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Telegram\Bot\Api;

class TgBotController extends Controller
{
    const TOKEN = '5735288330:AAEGY6PQjnu3RuBLEMPgH6Dh_SWD_xRrPKM';

    private $telegram;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->telegram = new Api(self::TOKEN);
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
            $message_from = $input_data['callback_query']['from'];
            $chat_id = $message['chat']['id'];
            $data = isset($input_data['callback_query']['data']) ? json_decode($input_data['callback_query']['data'], true) : [];
            $action = isset($data['action']) ? $data['action'] : '';
        } elseif (isset($input_data['message'])) {
            $message = $input_data['message'];
            $message_from = $message['from'];
            $chat_id = $message['chat'] ['id'];
            if (!isset($message['text']) && !isset($message['data'])) {
                return;
            }
            $action = isset($message['text']) ? $message['text'] : $message['data'];
        } else {
            return;
        }
        $action = mb_strtolower($action);
        $disable_web_page_preview = false;
        if (strripos($action, "start")) {
            $link_id = (int)str_replace('/start ', '', $action);
            info('link_id: ' . print_r($link_id, 1));
            $action = '/start';
        } else {
            $tg_user = TgUser::where(['tg_id' => $message_from['id']])
                ->orderBy('id', 'DESC')
                ->first();
            $link_id = $tg_user->link_id;
        }
        TgUser::create([
            'link_id' => $link_id ?? 0,
            'tg_id' => $message_from['id'] ?? 0,
            'username' => $message_from['username'] ?? '',
            'first_name' => $message_from['first_name'] ?? '',
            'last_name' => $message_from['last_name'] ?? '',
            'phone' => $message_from['phone'] ?? '',
            'last_action' => $action,
        ]);

        $send_message = true;
        $group_id = 0;
        switch ($action) {
            case '/start':
                $send_data = [
                    'text' => 'Добрый день!
На данный момент мы может предложить вам следующие варианты карт:

1️⃣ вариант

Не именная Мультивалютная карта Master Card Standart в 4х валютах: USD, EUR, RUB, KZT.

Преимущества:
☑ Оплата товаров и услуг по всему миру
☑ Apple Pay/Samsung Pay
☑ Удобное приложение банка с возможностью конвертации между своими счетами.
☑ Интеграция с российской сим-картой.
☑ Исходящие (с ограничениями) и входящие SWIFT-переводы.
☑ Бесплатное годовое обслуживание карты.
При первом пополнении счета единоразово списывается комиссия за открытие и доставку пластиковой карты 20 000 тенге (около 4 000 руб., зависит от курса рубль/тенге).
Стоимость 25 000 руб .

2️⃣ вариант

Мультивалютная карта Master Card в 4х валютах: USD, EUR, RUB, KZT.
Преимущества:
☑ Программа страхования для путешествий до 1 500 000
☑ Доступ в бизнес залы аэропортов / Программа Lounge Key.
☑ Консьерж-сервис.
☑ Доступы к сервису к Boingo WI-FI.
☑ Apple Pay/Samsung Pay
☑ Удобное приложение банка с возможностью конвертации между своими счетами.
☑ Исходящие и входящие SWIFT-переводы.
☑ Читается как CREDIT и подходит для RENT CAR.
Годовое обслуживание 100 000 тенге (около 20 000 руб., зависит от курса)

Стоимость дистанционного открытия - 65 000 рублей*

3️⃣ (Киргизия)

Моновалютная карта Visa
Валюта для открытия счетов: USD или EUR (на выбор)
(Дополнительные счета RUB, сом )
☑️ Срок действия карты  6 лет
☑️Стоимость обслуживания:
Gold - со второго года 100$
Platinum -со второго года 250$
Infinite - со второго года 450$

✔️Исходящие SWIFT переводы EUR
✔️Перевод P2P в рамках VISA
✔️Подключены Google Pay
✔️Пополнение через: SWIFT, Сбербанк и Тинькофф по номеру телефона, Korona Pay, МТС  банк,Qiwi
✔️Счёт привязывается к РФ сим-карте
✔️Русскоязычная поддержка внутри приложения 24/7

✅Visa Gold 77$
✅Visa Platinum 220$ (выпуск карты)
✅Visa Infinite 270$

Лимитов на Свифт переводы у банка нет .

✅Стоимость оформления - 40 000 руб


4️⃣ вариант

Моновалютнкя карта VISА
Валюта для открытия счетов: USD или EUR (на выбор)
☑ Стоимость обслуживания:
Gold economy: 59$ за 6 месяцев, далее 5$ в месяц;
Platinum: 299 $ за 2 года вперед, далее 10$ в месяц.
☑ Привязка к российскому номеру телефону.
☑ Лимиты по операциям можно временно увеличивать через поддержку банка.
☑ Удобные варианты пополнения карты: Валютный SWIFT, через мобильное приложение некоторых банков (Сбербанк и Тинькофф).
* Есть Visa Direct - возможность делать исходящие переводы внутри платежной системы VISA и внутри валюты (USD = USD, EUR = EUR).
☑ Читается как CREDIT и подходит для RENT CAR.

Стоимость дистанционного открытия банковского счёта/карты - 45 000 руб.

Варианты других карт и подробное описание, можно уточнить у менеджера ☎',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => 'Мне подходит вариант 1', 'callback_data'=>'{"action":"variant 1"}'],
                            ],
                            [
                                ['text' => 'Мне подходит вариант 2', 'callback_data'=>'{"action":"variant 2"}'],
                            ],
                            [
                                ['text' => 'Мне подходит вариант 3', 'callback_data'=>'{"action":"variant 3"}'],
                            ],
                            [
                                ['text' => 'Мне подходит вариант 4', 'callback_data'=>'{"action":"variant 4"}'],
                            ],
                            [
                                ['text' => 'Связаться с менеджером', 'callback_data'=>'{"action":"call manager"}'],
                            ],
                            [
                                ['text' => 'Частые вопросы', 'callback_data'=>'{"action":"faq"}'],
                            ],
                            [
                                ['text' => 'Отзывы', 'callback_data'=>'{"action":"reviews"}'],
                            ],
//                            [
//                                ['text' => 'Акция', 'callback_data'=>'{"action":"stock"}'],
//                            ],
                            [
                                ['text' => 'Договор оферта', 'callback_data'=>'{"action":"show offer"}'],
                            ],
                        ],
                    ]),
                    'parse_mode' => 'markdown',
                ];
                break;
            case 'free card':
            case 'variant 1':
            case 'variant 2':
            case 'variant 3':
            case 'variant 4':
            case 'call manager':
            case 'send a scan of your passport':
            case 'need info about banks':
            case 'i am ready':
            case 'want a card':
            case 'want a card. not resident':
            case 'want a card. have inn':
            case 'your question':
            case 'participation in the action':
                $send_data = [
                    'parse_mode' => 'HTML',
                    'text' => '<a href="https://t.me/AlternativeAssistance">Перейти на менеджера</a>',
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
                            [
                                ['text' => 'вернуться назад', 'callback_data'=>'{"action":"go back"}'],
                            ],
                        ],
                    ]),
                ];
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
            case 'which banks':
                $send_data = [
                    'text' => 'На данный момент мы работаем с двумя надежными банками, которые выдают карты работающие во всем мире (кроме России), с входящим и исходящим SWIFT, возможность работы с Apple и Google Pay,  возможность конвертации между своими счетами в приложении банка. Также мы постоянно ведем переговоры с новыми банками, чтобы у вас было больше выбора.',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => 'Хочу узнать какие банки открывают сейчас', 'callback_data'=>'{"action":"need info about banks"}'],
                            ],
                            [
                                ['text' => 'вернуться назад', 'callback_data'=>'{"action":"go back"}'],
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
                $send_data = [
                    'document' => config('app.url') . '/files/oferta.pdf',
                    'chat_id' => $chat_id,
                    'disable_web_page_preview' => $disable_web_page_preview,
                ];
                $this->telegram->sendDocument($send_data);
                $send_message = false;
                break;
            case 'go back':
                $this->delete_messages($message_from['id'], $message['message_id']);
                $send_message = false;
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
                                ['text' => 'Виктория Кабочкина', 'callback_data'=>'{"action":"reviews 1"}'],
                            ],
                            [
                                ['text' => 'Инна Аминова', 'callback_data'=>'{"action":"reviews 2"}'],
                            ],
                            [
                                ['text' => 'Никита', 'callback_data'=>'{"action":"reviews 3"}'],
                            ],
                            [
                                ['text' => 'Юлия Бездарь', 'callback_data'=>'{"action":"reviews 4"}'],
                            ],
                            [
                                ['text' => 'Яна Левенцева', 'callback_data'=>'{"action":"reviews 5"}'],
                            ],
                            [
                                ['text' => 'вернуться назад', 'callback_data'=>'{"action":"go back"}'],
                            ],
                        ],
                    ]),
                ];
                break;
            case 'reviews 1':
                $send_data = [
                    'chat_id' => $chat_id,
                    'disable_web_page_preview' => $disable_web_page_preview,
                ];
                $media_files = [
                    'photo_2022-08-08 15.34.44.jpeg' => 'photo',
                    'photo_2022-08-08 15.34.47.jpeg' => 'photo',
                    'photo_2022-08-08 15.34.49.jpeg' => 'photo',
                    'photo_2022-08-08 15.34.53.jpeg' => 'photo',
                ];
                $this->send_media_files($send_data, $media_files, 1, $message['message_id'], $action);
                $group_id = $message['message_id'];
                break;
            case 'reviews 2':
                $send_data = [
                    'chat_id' => $chat_id,
                    'disable_web_page_preview' => $disable_web_page_preview,
                ];
                $media_files = [
                    'photo_2022-08-08 15.44.51.jpeg' => 'photo',
                    'photo_2022-08-08 15.44.54.jpeg' => 'photo',
                    '510067915_295975166_1072048663687545_5176148860360225848_n.mp4' => 'video',
                    '510067915_296797225_627808898513213_3549416364628311978_n.mp4' => 'video',
                ];
                $this->send_media_files($send_data, $media_files, 2, $message['message_id'], $action);
                $group_id = $message['message_id'];
                break;
            case 'reviews 3':
                $send_data = [
                    'chat_id' => $chat_id,
                    'disable_web_page_preview' => $disable_web_page_preview,
                ];
                $media_files = [
                    'photo_2022-08-08 15.41.37.jpeg' => 'photo',
                    'photo_2022-08-08 15.41.40.jpeg' => 'photo',
                    'photo_2022-08-08 15.41.43.jpeg' => 'photo',
                    'photo_2022-08-08 15.41.47.jpeg' => 'photo',
                    '510067915_296221184_341341454853135_7851361796163644737_n.mp4' => 'video',
                ];
                $this->send_media_files($send_data, $media_files, 3, $message['message_id'], $action);
                $group_id = $message['message_id'];
                break;
            case 'reviews 4':
                $send_data = [
                    'chat_id' => $chat_id,
                    'disable_web_page_preview' => $disable_web_page_preview,
                ];
                $media_files = [
                    'IMG_1223.jpg' => 'photo',
                    'IMG_1224.jpg' => 'photo',
                    'photo_2022-08-08 15.30.15.jpeg' => 'photo',
                    'photo_2022-08-08 15.30.22.jpeg' => 'photo',
                    'photo_2022-08-08 15.30.27.jpeg' => 'photo',
                    'photo_2022-08-08 15.30.32.jpeg' => 'photo',
                    'photo_2022-08-08 15.30.35.jpeg' => 'photo',
                    'photo_2022-08-08 15.30.43.jpeg' => 'photo',
                    'photo_2022-08-08 15.30.47.jpeg' => 'photo',
                    'photo_2022-08-08 15.30.53.jpeg' => 'photo',
                ];
                $this->send_media_files($send_data, $media_files, 4, $message['message_id'], $action);
                $group_id = $message['message_id'];
                break;
            case 'reviews 5':
                $send_data = [
                    'chat_id' => $chat_id,
                    'disable_web_page_preview' => $disable_web_page_preview,
                ];
                $media_files = [
                    'photo_2022-08-08 15.42.41.jpeg' => 'photo',
                    'photo_2022-08-08 15.43.18.jpeg' => 'photo',
                    'photo_2022-08-08 15.43.21.jpeg' => 'photo',
                    '510067915_295528928_466234748283841_1263878676191726693_n.mp4' => 'video',
                    '510067915_296401522_734136984484264_3715331654570136976_n.mp4' => 'video',
                ];
                $this->send_media_files($send_data, $media_files, 5, $message['message_id'], $action);
                $group_id = $message['message_id'];
                break;
            default:
                $send_data = [
                    'text' => 'Команда не найдена. Повторите попытку',
                ];
        }
        if ($group_id) {
            $send_data = [
                'text' => 'Нажмите для возврата',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => 'вернуться назад', 'callback_data'=>'{"action":"go back"}'],
                        ],
                    ],
                ]),
            ];
        }
        $send_data['chat_id'] = $chat_id;
        $send_data['disable_web_page_preview'] = $disable_web_page_preview;

        if ($send_message) {
            self::send_telegram($send_data, $action, $group_id);
        }
    }

    /**
     * @param array $data
     * @param string $action
     * @param int $group_id
     * @return void
     */
    private function send_telegram(
        array $data,
        string $action,
        int $group_id = 0
    ) {
        $result = $this->telegram->sendMessage($data);

        TgMessage::create([
            'action' => $action,
            'tg_message_id' => $result->getMessageId(),
            'tg_user_id' => $result->getChat()->getId(),
            'group_id' => $group_id,
        ]);
    }

    /**
     * @param array $send_data
     * @param array $media_files
     * @param int $review_code
     * @param int $group_id
     * @param string $action
     */
    private function send_media_files(
        array $send_data,
        array $media_files,
        int $review_code,
        int $group_id,
        string $action
    ) {

        foreach ($media_files as $media_name => $type) {
            $url = config('app.url') . '/reviews/' . $review_code . '/' . $media_name;
            if ($type === 'photo') {
                unset($send_data['video']);
                $send_data['photo'] = $url;
                $result = $this->telegram->sendPhoto($send_data);
            } else {
                unset($send_data['photo']);
                $send_data['video'] = $url;
                $result = $this->telegram->sendVideo($send_data);
            }
            TgMessage::create([
                'action' => $action,
                'tg_message_id' => $result->getMessageId(),
                'tg_user_id' => $result->getChat()->getId(),
                'group_id' => $group_id,
            ]);
        }
    }

    /**
     * @param $chat_id
     * @param $message_id
     * @return false|string
     */
    private function delete_message($chat_id, $message_id)
    {
        $api_uri = 'https://api.telegram.org/bot' . self::TOKEN . '/deleteMessage?'
            . 'chat_id=' . $chat_id
            . '&message_id=' . $message_id;
        info('$api_uri : ' . $api_uri);

        return file_get_contents($api_uri);
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

        return redirect('https://t.me/alternative_assistance_bot?start=' . $referrer_link->id);
    }

    /**
     * @param int $tg_user_id
     * @param int $message_id
     */
    private function delete_messages(int $tg_user_id, int $message_id)
    {
        $tg_messages = TgMessage::where([
            'tg_user_id' => $tg_user_id,
        ])
            ->where(function($query) use ($message_id) {
                $query->where('group_id', $message_id)
                    ->orWhere('tg_message_id', $message_id);
            })
            ->get();
        foreach ($tg_messages as $tg_message) {
            info('delete tg message id: ' . $tg_message->id);
            $this->delete_message($tg_message->tg_user_id, $tg_message->tg_message_id);
            $tg_message->delete();
            if ($tg_message->group_id && $tg_message->group_id !== $message_id) {
                $this->delete_messages($tg_user_id, $tg_message->group_id);
            }
        }
    }
}
