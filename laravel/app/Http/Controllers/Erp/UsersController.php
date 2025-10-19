<?php

namespace App\Http\Controllers\Erp;

use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PermissionsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class UsersController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $usersQuery = User::query();
    $usersQuery = $this->applySort($usersQuery);
    $usersQuery = $this->applyFilter($usersQuery);
    $users = $usersQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.users.index', [
      'users' => $users,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $user = new User();

    if ($request->isMethod('post')) {
      $user->fill($request->all());

      $validator = Validator::make($request->all(), [
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'string', 'min:6'],
        'fullName' => ['required', 'string', 'max:255'],
        'isAdmin' => ['required', 'boolean'],
        'permissions' => ['nullable', 'array'],
        'permissions.*' => ['in:' . implode(',', array_keys(PermissionsService::getAllPermission()))],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $user->password = Hash::make($user->password);
        $user->save();

        return redirect('/erp/users/update/' . $user->id)
          ->with('success', 'Успешно създадохте нов потребител.');
      }
    }

    return view('erp.users.create', [
      'user' => $user,
      'errors' => $errors,
    ]);
  }

  public function update(int $userId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $user User */
    $user = User::where('id', $userId)->firstOrFail();
    $user->password = null;

    if ($request->isMethod('post')) {
      $user->fill($request->all());

      $validator = Validator::make($request->all(), [
        'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        'password' => ['nullable', 'string', 'min:6'],
        'fullName' => ['required', 'string', 'max:255'],
        'isAdmin' => ['required', 'boolean'],
        'permissions' => ['nullable', 'array'],
        'permissions.*' => ['in:' . implode(',', array_keys(PermissionsService::getAllPermission()))],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $user->password = $user->password ? Hash::make($user->password) : $user->getOriginal('password');
        $user->save();

        return redirect('/erp/users/update/' . $user->id)
          ->with('success', 'Успешно редактирахте потребителя.');
      }
    }

    return view('erp.users.update', [
      'user' => $user,
      'errors' => $errors,
    ]);
  }

  public function delete(int $userId)
  {
    /* @var $user User */
    $user = User::where('id', $userId)->firstOrFail();

    if ($user->id === Auth::user()->id) {
      abort(400, 'Не може да изтриете своя потребител.');
    }

    $user->delete();

    return redirect('/erp/users')
      ->with('success', 'Успешно изтрихте потребителя.');
  }
}
