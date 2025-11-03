<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait CheckSelfAccess
{
    protected function isSelf($model): bool
    {
        $user = auth()->user();

        if (! $user || ! $model){
            return false;
        }

        if ($model instanceof \App\Models\User) {
            return $user->id === $model->id;
        }

        if (isset($model->user_id)) {
            return $user->id === $model->user_id;
        }

        return false;
    }
}
