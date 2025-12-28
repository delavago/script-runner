<?php

namespace App\Policies;

use App\Models\User;

class CredentialPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny()
    {
        return auth()->user()->hasRole('Admin');
    }
}
