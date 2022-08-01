<?php

namespace App\Http\Controllers;

use App\Models\ReferrerLink;
use App\Models\ReferrerRedirect;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class DashboardController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $referrer_links = ReferrerLink::get();

        return view('dashboard', [
            'referrer_links' => $referrer_links,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create_link_page()
    {
        return view('create_link');
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector|void
     */
    public function create_link(Request $request)
    {
        $label = $request->get('label', '');
        $link_label = $label ? $label : rand(0, 100) . time() . rand(0, 100);
        ReferrerLink::create([
            'link' => config('app.url') . '/invite/' . $link_label,
            'label' => $label,
            'caption' => $request->get('caption', '') ?? '',
            'count' => 0,
            'uniq_count' => 0,
        ]);

        return redirect('dashboard');
    }
    /*
     * На данный момент мы можем предложить вам открытие в двух банках Казахстана:

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

*Оплата делится на 2 платежа: 50% предоплата в момент предоставления скана занранпаспорта и 50% по факту открытия счета.
     */

    /**
     * @param Request $request
     * @param int $link_id
     * @return Application|RedirectResponse|Redirector|void
     */
    public function delete_link(
        Request $request,
        int $link_id
    )
    {
        $link = ReferrerLink::find($link_id);
        if ($link) {
            $link->delete();
        }

        return redirect('dashboard');
    }
}
