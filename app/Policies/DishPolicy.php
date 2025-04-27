<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Dish;

class DishPolicy
{
    public function viewAny(User $user)    { return $user->hasPermissionTo('view dishes'); }
    public function create(User $user)     { return $user->hasPermissionTo('create dishes'); }
    public function update(User $user)     { return $user->hasPermissionTo('edit dishes'); }
    public function delete(User $user)     { return $user->hasPermissionTo('delete dishes'); }
}