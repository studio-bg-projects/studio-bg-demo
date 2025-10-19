<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class ProfileController extends Controller
{
  public function index()
  {
    return view('public.profile.index');
  }

  public function update(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $user User */
    $user = Auth::user();

    if ($request->isMethod('post')) {
      $user->fill($request->all());

      $validator = Validator::make($request->all(), [
        'fullName' => ['required', 'string', 'max:255'],
      ]);

      if (!$validator->fails()) {
        $user->save();

        return redirect('/profile/update')
          ->with('success', 'Успешно редактирахте профила си.');
      } else {
        $errors->merge($validator->errors());
      }
    }

    return view('public.profile.update', [
      'user' => $user,
      'errors' => $errors,
    ]);
  }

  public function passwordChange(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $user User */
    $user = Auth::user();


    if ($request->isMethod('post')) {
      $user->fill($request->all());

      $validator = Validator::make($request->all(), [
        'oldPassword' => ['required', 'min:6'],
        'newPassword' => ['required', 'min:6', 'confirmed'],
      ], [
        'newPassword.confirmed' => 'Новите пароли трябва да съвпадат.',
      ]);

      if (!$validator->fails()) {
        if (!Hash::check($request->input('oldPassword'), $user->password)) {
          $errors->add('oldPassword', 'Старата парола е неправилна.');
        } else {
          $user->password = Hash::make($request->input('newPassword'));
          $user->save();

          return redirect('/profile/password-change')
            ->with('success', 'Паролата е сменена успешно.');
        }
      } else {
        $errors->merge($validator->errors());
      }
    }


    return view('public.profile.password-change', [
      'user' => $user,
      'errors' => $errors,
    ]);
  }
}
