<?php

namespace App\Services\Jobs;

use App\Enums\UploadGroupType;
use App\Http\Controllers\Erp\BannersController;
use App\Models\Config;
use App\Models\SalesRepresentative;
use App\Models\Upload;

class SyncMiscJob extends BaseSyncJob
{
  public function run(): void
  {
    $this->syncSalesRepresentatives();
    $this->syncBanners();
    $this->out('All good :)');
  }

  public function syncSalesRepresentatives(): void
  {
    $salesRepresentatives = SalesRepresentative::all();

    $shopSalesRepresentatives = $this->dictionarizeShopRecords('extend_sales_representative', 'extend_sales_representative_id');
    $setInactive = $shopSalesRepresentatives; // Collect records to be inactive

    /* @var $salesRepresentatives SalesRepresentative[] */
    foreach ($salesRepresentatives as $salesRepresentative) {
      // Add new record
      if (!isset($shopSalesRepresentatives[$salesRepresentative->id])) {
        $this->out(sprintf('Add sales representative %s', $salesRepresentative->id));

        $data = [
          'extend_sales_representative_id' => $salesRepresentative->id,
          'phone1' => $salesRepresentative->phone1,
          'phone2' => $salesRepresentative->phone2,
          'email1' => $salesRepresentative->email1,
          'email2' => $salesRepresentative->email2,
          'photo_file' => null,
        ];

        foreach (self::$languages as $langId => $langName) {
          $data['name_' . $langId] = $salesRepresentative->{'name' . $langName};
          $data['title_' . $langId] = $salesRepresentative->{'title' . $langName};
        }

        $this->shopConn()->table(self::PREFIX . 'extend_sales_representative')->insert($data);

        $shopSalesRepresentatives[$salesRepresentative->id] = $this->shopConn()->table(self::PREFIX . 'extend_sales_representative')
          ->where('extend_sales_representative_id', $salesRepresentative->id)
          ->first();
      }

      // Remove from inactive
      if (isset($setInactive[$salesRepresentative->id])) unset($setInactive[$salesRepresentative->id]);

      // Compare the records and put them in a map to debug where the differences
      $updates = [];

      // Record compare
      if ((string)$salesRepresentative->phone1 !== (string)$shopSalesRepresentatives[$salesRepresentative->id]->phone1) $updates['phone1'] = (string)$salesRepresentative->phone1;
      if ((string)$salesRepresentative->phone2 !== (string)$shopSalesRepresentatives[$salesRepresentative->id]->phone2) $updates['phone2'] = (string)$salesRepresentative->phone2;
      if ((string)$salesRepresentative->email1 !== (string)$shopSalesRepresentatives[$salesRepresentative->id]->email1) $updates['email1'] = (string)$salesRepresentative->email1;
      if ((string)$salesRepresentative->email2 !== (string)$shopSalesRepresentatives[$salesRepresentative->id]->email2) $updates['email2'] = (string)$salesRepresentative->email2;

      foreach (self::$languages as $langId => $langName) {
        if ((string)$salesRepresentative->{'name' . $langName} !== (string)$shopSalesRepresentatives[$salesRepresentative->id]->{'name_' . $langId}) $updates['name_' . $langId] = (string)$salesRepresentative->{'name' . $langName};
        if ((string)$salesRepresentative->{'title' . $langName} !== (string)$shopSalesRepresentatives[$salesRepresentative->id]->{'title_' . $langId}) $updates['title_' . $langId] = (string)$salesRepresentative->{'title' . $langName};
      }

      // File compare
      $photoFilePath = $salesRepresentative->uploads->first() ? 'erp/' . $salesRepresentative->uploads->first()->urls->path : '';
      if ($photoFilePath !== (string)$shopSalesRepresentatives[$salesRepresentative->id]->photo_file) $updates['photo_file'] = $photoFilePath;

      // Do the update
      if ($updates) {
        $this->out(sprintf('Update sales representatives %s with differences %s', $salesRepresentative->id, json_encode($updates)));

        $this->shopConn()->table(self::PREFIX . 'extend_sales_representative')
          ->where('extend_sales_representative_id', $salesRepresentative->id)
          ->update($updates);
      }
    }

    // Delete or set inactive
    foreach ($setInactive as $shopSalesRepresentative) {
      $this->out(sprintf('Delete inactive sales representatives %s (not exists in ERP)', $shopSalesRepresentative->extend_sales_representative_id));

      $this->shopConn()->table(self::PREFIX . 'extend_sales_representative')
        ->where('extend_sales_representative_id', $shopSalesRepresentative->extend_sales_representative_id)
        ->delete();
    }
  }

  public function syncBanners(): void
  {
    $settingRecord = $this->shopConn()->table(self::PREFIX . 'setting')->select()->where([
      'store_id' => $this->storeId,
      'code' => 'custom',
      'key' => 'custom.home_banners',
    ])->first();

    if (!$settingRecord) {
      $insertId = $this->shopConn()->table(self::PREFIX . 'setting')->insertGetId([
        'store_id' => $this->storeId,
        'code' => 'custom',
        'key' => 'custom.home_banners',
        'value' => '[]',
      ]);

      $settingRecord = $this->shopConn()->table(self::PREFIX . 'setting')->select()->where([
        'setting_id' => $insertId,
      ])->first();
    }

    $uploads = [];
    foreach (Upload::where(['groupType' => UploadGroupType::Banners->value, 'groupId' => 'home-banners'])->get() as $upload) {
      $uploads[$upload->sortOrder] = $upload;
    }

    /* @var $configs Config[] */
    $bannersData = [];
    foreach (json_decode(dbConfig('banner:home')) as $i => $bannerDb) {
      $banner = [];
      $banner['color'] = $bannerDb->color;
      $banner['url'] = $bannerDb->url;
      $banner['img_path'] = isset($uploads[$i + 1]) ? 'erp/' . $uploads[$i + 1]->urls->path : null;

      foreach (self::$languages as $langId => $langName) {
        $banner['text_' . $langId] = $bannerDb->{'text' . $langName};
      }

      $bannersData[$i + 1] = $banner;
    }

    $bannersDataJson = json_encode($bannersData);

    if ($settingRecord->value !== $bannersDataJson) {
      $this->out('Update custom.home_banners');

      $this->shopConn()->table(self::PREFIX . 'setting')->where(['setting_id' => $settingRecord->setting_id])->update([
        'value' => $bannersDataJson,
      ]);
    }
  }
}
