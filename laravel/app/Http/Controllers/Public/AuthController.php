<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    if (Auth::check()) {
      return redirect('/');
    }

    $errors = session()->get('errors') ?? new MessageBag();

    $backto = $request->input('backto', '/');

    if ($request->isMethod('post')) {
      $credentials = $request->only('email', 'password');

      $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|min:6',
      ]);

      if (!$validator->fails()) {
        if (Auth::attempt($credentials)) {
          session()->regenerate();
          return redirect($backto)
            ->with('success', 'Успешно влязохте в системата.');
        } else {
          $errors->add('password', 'Грешни данни за вход');
        }
      } else {
        $errors->merge($validator->errors());
      }
    }

    return view('public.auth.login', [
      'errors' => $errors,
      'backto' => $backto,
    ]);
  }

  public function logout()
  {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect('/');
  }
}
