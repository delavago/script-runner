<?php

namespace App\Policies;

use App\Models\User;

class ScriptPolicy
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
        return auth()->user()->can('script:create');
    }

    public function update() 
    {
        return auth()->user()->can('script:update');
    }

    public function delete() 
    {
        return auth()->user()->can('script:delete');
    }

    public function deleteAny() 
    {
        return auth()->user()->can('script:delete');
    }
}
