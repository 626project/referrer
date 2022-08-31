<?php

namespace App\Models;

use App\Services\UserService;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class TgUserExportCollection implements FromCollection
{
    use Exportable;

    private $link_id;
    private $start_date;
    private $end_date;

    /**
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
     * @return Collection
     */
    public function collection()
    {
        return (new UserService)->get_tg_users($this->link_id, $this->start_date, $this->end_date);
    }
}
