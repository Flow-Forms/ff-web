<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class DeleteUser
{
    /**
     * Delete the given user after validating password.
     */
    public function delete(User $user, string $password): void
    {
        if (! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => [__('This password does not match our records.')],
            ]);
        }

        $user->tokens->each->delete();
        $user->delete();
    }
}
