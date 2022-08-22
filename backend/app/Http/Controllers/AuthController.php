<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Cookie;
use App\Http\Requests\UpdateInfoRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request) {
        if (Auth::attempt($request->only('email','password')))
        {
        /** @var \App\Models\User|null $user */ /* DA BI Intelephense PREPOZNAO IZ PHP Authenticatable INTERFEJSA */
            $user = Auth::user();
            $token = $user->createToken('admin')->accessToken;
            /* return [
                'token' => $token,
            ]; */
            $cookie = \cookie('jwt', $token, 3600);
            return \response([
                /* 'token' => $token, */
                'user' => $this->user()
            ])->withCookie($cookie);
        }

        return response([
            'error' => __('Invalid Credentials!')
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function logout(Request $request) {
        $cookie = Cookie::forget('jwt');
        return \response(["message" => __('auth.logout_success')])->withCookie($cookie);
        /* MOZE I OBICNI PHP if (isset($_COOKIE['jwt'])) {
            unset($_COOKIE['jwt']);
            setcookie('jwt', '', time() - 3600, '/'); // empty value and old timestamp
        } */
    }

    public function register(RegisterRequest $request) {
        $user = User::create($request->only('first_name', 'last_name', 'email') + [
            'role_id' => 4, /* always register a new user as author */
            'password' => Hash::make($request->input('password') /* 1234 */),
        ]);
        /* NE TREBA KADA JE U MODELU User implements MustVerifyEmail
        if ($user) {*/
            event(new Registered($user));
        //} */
        return response($user, Response::HTTP_CREATED);
    }

    public function user() {
        $user = Auth::user();
        /** @var \App\User|null $user */ /* DA BI Intelephense PREPOZNAO IZ PHP Authenticatable INTERFEJSA */
        return (new UserResource($user))->additional([
            "data" => [
                "permissions" => $user->permissions(),
            ],
        ]);
    }

    public function updateInfo(UpdateInfoRequest $request) {
        $user = Auth::user();
        /** @var \App\Models\User|null $user */
        $user->update($request->only('first_name','last_name','email','role_id'));

        return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }

    public function updatePassword(UpdatePasswordRequest $request) {
        $user = Auth::user();
        // DA LI UZ PROVERU e-POSTE?

        /** @var \App\Models\User|null $user */
        $user->update([
            "password" => Hash::make($request->input('password'))
        ]);

        return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }
}


