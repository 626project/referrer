<?php

namespace App\Services;

use App\Models\TgUser;

class UserService
{
    /**
     * @param int $link_id
     * @param string|null $start_date
     * @param string|null $end_date
     * @return mixed
     */
    public function get_tg_users(
        int $link_id,
        ?string $start_date,
        ?string $end_date
    ) {
        $tg_users_request = TgUser::where(['link_id' => $link_id]);
//            ->whereIn('last_action', ['/start', 'variant 1', 'variant 2', 'variant 3', 'call manager', 'send a scan of your passport', 'need info about banks', 'i am ready', 'want a card', 'want a card. not resident', 'want a card. have inn', 'your question', 'participation in the action']);
        if ($start_date) {
            $tg_users_request->where('created_at', '>=', $start_date);
        }
        if ($end_date) {
            $tg_users_request->where('created_at', '<', $end_date);
        }

        return $tg_users_request->get();
    }
}
