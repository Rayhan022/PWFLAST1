<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.register');
});

Route::post('/', function () {
    return redirect('/login');
});
Route::middleware(['guest'])->group(function () {
    Route::get('/posts/index', function () {
        return view('/posts/index');
    })->name('posts.index');
});

Route::get('/posts/index', function () {
    return view('/posts/index');
});
Route::post('/redirect-to-login', function () {
    return redirect('/login');
})->name('redirect.to.login');

Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('posts.index');
    });

    Route::get('/posts/index', [PostController::class, 'index'])->name('posts.index');
});


Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register');
Route::view('/login', 'login')->name('login');

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('auth');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->name('logout');

Route::get('/posts/index', function () {
    // Remove the direct view return here
    // Instead, redirect to the controller's index method
    return redirect()->route('posts.index');
});

Route::resource('posts', PostController::class);

Route::post('/comments/{post}', [CommentController::class, 'store'])->name('comments.store');


Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
Route::post('/posts/{post}/dislike', [PostController::class, 'dislike'])->name('posts.dislike');
