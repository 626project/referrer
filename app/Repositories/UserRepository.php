<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * @param int $id
     * @return User|null
     */
    public function find(int $id)
    {
        return User::find($id);
    }
}
