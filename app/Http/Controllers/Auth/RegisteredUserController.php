<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Response;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(UserRequest $request): Response
    {
        $data = $request->validated();

        $user = User::create($data);

        event(new Registered($user));

        return response()->noContent();
    }
}
