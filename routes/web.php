<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
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
    dd($providerUser);

//    $user = User::where('provider')
    $user = User::firstOrCreate(
        [
            'provider_id' => $provider,
            'provider' => $providerUser->getId()
        ],
        [
            'email' => $providerUser->getEmail(),
            'name' => $providerUser->getName(),
            'username' => $providerUser->getEmail(),
            'provider' => $provider
        ]);
    $user->markEmailAsVerified();
    auth()->login($user, true);

    return redirect('dashboard');

});
