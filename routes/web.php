<?php

use App\Models\ExternalAuthUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/auth/redirect/{provider}', function ($provider) {
    return Socialite::driver($provider)->redirect();
});

Route::get('/auth/callback/{provider}', function ($provider) {

    $providerUser = Socialite::driver($provider)->user();
    $main_user = User::select('id')->where('email', $providerUser->getEmail())->first();


    if($main_user)
    {
        $external_user_exist = ExternalAuthUser::where('provider', $provider)->where('id', $main_user->id)->first();
        if($external_user_exist)
        {
            auth()->login($main_user, true);
            return redirect('dashboard');
        }
        else
        {
            $new_external_user = ExternalAuthUser::create([
                'provider_id' => $providerUser->getId(),
                'id' => $main_user->id,
                'provider' => $provider,
            ]);
            auth()->login($main_user, true);
            return redirect('dashboard');
        }
    }
    else
    {
        $headers = get_headers($providerUser->getAvatar(), 1);
        $type = "." . explode('/', $headers["Content-Type"])[1];
        $image_title = Str::random(40);

        DB::transaction(function () use ($providerUser, $image_title, $type, $provider)
        {
            $img = file_get_contents($providerUser->getAvatar());
            file_put_contents(base_path('public/storage/profile-photos/' . $image_title . $type), $img);

            //insert into Users table
            $new_user = User::create([
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
                'username' => $providerUser->getEmail(),
                'profile_photo_path' => '/profile-photos/' . $image_title . $type,
            ]);

            //insert into ExternalAuthUser table
            $new_external_user = ExternalAuthUser::create([
                'provider_id' => $providerUser->getId(),
                'id' => $new_user->id,
                'provider' => $provider,
            ]);

            $new_user->markEmailAsVerified();
            auth()->login($new_user, true);
        });
        return redirect('dashboard');
    }
});
