<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Loss;

class LossPolicy
{
    public function viewAny(User $user)    { return $user->hasPermissionTo('view losses'); }
    public function create(User $user)     { return $user->hasPermissionTo('create losses'); }
    public function delete(User $user)     { return $user->hasPermissionTo('delete losses'); }
}