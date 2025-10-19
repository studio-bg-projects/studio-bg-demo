<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
  public function loginAs(int $userId)
  {
    if (!session()->has('impersonateAdminId')) {
      session()->put('impersonateAdminId', Auth::id());
    }

    Auth::loginUsingId($userId);

    $impersonateUser = Auth::user();

    return redirect('/erp/dashboard')
      ->with('success', sprintf('Успешно влязохте като потребител %s (%s).', $impersonateUser->fullName, $impersonateUser->email));
  }

  public function stopImpersonating()
  {
    $currentUserId = Auth::id();

    if (session()->has('impersonateAdminId')) {
      $adminId = session()->get('impersonateAdminId');
      Auth::loginUsingId($adminId);

      session()->forget('impersonateAdminId');
    }

    return redirect('/erp/users/update/' . $currentUserId)
      ->with('success', 'Успешно се върнахте към стария си потребител.');
  }
}
