<?php

namespace App\Policies;

use App\Models\Combo;
use App\Models\User;

class ComboPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view combos');
    }
    public function create(User $user)
    {
        return $user->hasPermissionTo('create combos');
    }
    public function update(User $user, Combo $combo)
    {
        return $user->hasPermissionTo('edit combos');
    }
    public function delete(User $user, Combo $combo)
    {
        return $user->hasPermissionTo('delete combos');
    }
}
