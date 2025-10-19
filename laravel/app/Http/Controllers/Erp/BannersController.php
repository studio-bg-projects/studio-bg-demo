<?php

namespace App\Http\Controllers\Erp;

use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;

class BannersController extends Controller
{
  use FilterAndSort;

  public function index(Request $request)
  {
    $banners = json_decode(dbConfig('banner:home'));

    if ($request->isMethod('post')) {
      foreach ($banners as $i => $banner) {
        foreach ($banner as $key => $value) {
          $banners[$i]->{$key} = $request->post('banner-' . $i . '-' . $key);
        }
      }

      $config = Config::where(['key' => 'banner:home'])->first();
      $config->value = json_encode($banners);
      $config->save();

      return redirect('/erp/banners')
        ->with('success', 'Успешно редактирахте информацията.');
    }

    $shopSliderLink = env('SHOP_URL') . 'admin/index.php?route=common/erp.login&key=' . env('SHOP_LOGIN_KEY') . '&goto_route=design/banner&__no_layout=true';

    return view('erp.banners.index', [
      'banners' => $banners,
      'shopSliderLink' => $shopSliderLink,
    ]);
  }
}
