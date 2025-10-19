<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class ConfigController extends Controller
{
  public function index(Request $request)
  {
    $configs = Config::all();
    $errors = session()->get('errors') ?? new MessageBag();

    if ($request->isMethod('post')) {
      foreach ($configs as $config) {
        if ($request->post($config->key) === null) {
          continue;
        }

        $config->value = $request->post($config->key);
        $config->save();
      }

      return redirect('/erp/config')
        ->with('success', 'Успешно редактирахте конфигурацията.');
    }

    return view('erp.config.index', [
      'configs' => $configs,
      'errors' => $errors,
    ]);
  }
}
