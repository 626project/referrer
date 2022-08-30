<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as BuilderAlias;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;

class TgUserExportCollection implements FromQuery
{
    use Exportable;

    private $link_id;
    private $start_date;
    private $end_date;

    /**
     * TgUserExportCollection constructor.
     * @param int $link_id
     * @param string|null $start_date
     * @param string|null $end_date
     */
    public function __construct(
        int $link_id,
        ?string $start_date,
        ?string $end_date
    )
    {
        $this->link_id = $link_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    /**
     * @return Builder|BuilderAlias
     */
    public function query()
    {
        $tg_users_request = TgUser::where(['link_id' => $this->link_id])
            ->whereIn('last_action', ['/start', 'variant 1', 'variant 2', 'variant 3', 'call manager', 'send a scan of your passport', 'need info about banks', 'i am ready', 'want a card', 'want a card. not resident', 'want a card. have inn', 'your question', 'participation in the action']);
        if ($this->start_date) {
            $tg_users_request->where('created_at', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $tg_users_request->where('created_at', '<', $this->end_date);
        }
        info($tg_users_request->count());

        return $tg_users_request->get();
    }
}
