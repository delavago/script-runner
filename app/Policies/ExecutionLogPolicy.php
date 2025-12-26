<?php

namespace App\Policies;

use App\Models\User;

class ExecutionLogPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create()
    {
        return false;
    }

    public function update()
    {
        return false;
    }

    public function delete()
    {
        return auth()->user()->hasRole('Admin');
    }
}
