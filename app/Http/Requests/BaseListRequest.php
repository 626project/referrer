<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseListRequest extends FormRequest
{
    /**
     * @return mixed
     */
    public function get_sort_column()
    {
        return $this->get('sort_column', 'id');
    }

    /**
     * @return mixed
     */
    public function get_sort_direction()
    {
        $sort_direction = $this->get('sort_direction', 'desc');

        return in_array($sort_direction, ['asc', 'desc'])
            ? $sort_direction
            : 'desc';
    }
}
