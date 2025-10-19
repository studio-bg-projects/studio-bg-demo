<?php

use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [\App\Http\Controllers\Public\HomeController::class, 'index']);

Route::get('/storage/uploads/{file}', [\App\Http\Controllers\Public\StorageController::class, 'resizer'])->where('file', '.*');

Route::get('/auth/login', [\App\Http\Controllers\Public\AuthController::class, 'login'])->name('login');
Route::post('/auth/login', [\App\Http\Controllers\Public\AuthController::class, 'login']);
Route::get('/auth/logout', [\App\Http\Controllers\Public\AuthController::class, 'logout'])->middleware('auth');

Route::group(['middleware' => ['api.key']], function () {
  Route::get('/api/ping', [\App\Http\Controllers\Api\PingController::class, 'index']);

  Route::post('/api/mail/create', [\App\Http\Controllers\Api\MailController::class, 'create']);

  Route::get('/api/feed/products/{lang}/{format}', [\App\Http\Controllers\Api\FeedController::class, 'products']);
  Route::get('/api/feed/available/{lang}/{format}', [\App\Http\Controllers\Api\FeedController::class, 'available']);

  Route::post('/api/id/reserve', [\App\Http\Controllers\Api\IdReservationController::class, 'reserve']);

  Route::post('/api/speedy/search', [\App\Http\Controllers\Api\SpeedyController::class, 'search']);
  Route::post('/api/speedy/calculate', [\App\Http\Controllers\Api\SpeedyController::class, 'calculate']);
});

Route::group(['middleware' => ['auth']], function () {
  // Check -> /app/Services/Permissions.php

  // Profile
  Route::get('/profile', [\App\Http\Controllers\Public\ProfileController::class, 'index']);
  Route::get('/profile/update', [\App\Http\Controllers\Public\ProfileController::class, 'update']);
  Route::post('/profile/update', [\App\Http\Controllers\Public\ProfileController::class, 'update']);
  Route::get('/profile/password-change', [\App\Http\Controllers\Public\ProfileController::class, 'passwordChange']);
  Route::post('/profile/password-change', [\App\Http\Controllers\Public\ProfileController::class, 'passwordChange']);

  // ERP
  Route::get('/erp', [\App\Http\Controllers\Erp\HomeController::class, 'index']);

  Route::get('/erp/dashboard', [\App\Http\Controllers\Erp\DashboardController::class, 'index']);

  Route::get('/erp/impersonate/login-as/{userId}', [\App\Http\Controllers\Erp\ImpersonateController::class, 'loginAs'])->middleware('permission:users');
  Route::get('/erp/impersonate/stop', [\App\Http\Controllers\Erp\ImpersonateController::class, 'stopImpersonating']);

  Route::get('/erp/uploads/{groupType}/{groupId}', [\App\Http\Controllers\Erp\UploadsController::class, 'index']);
  Route::post('/erp/uploads/{groupType}/{groupId}/upload', [\App\Http\Controllers\Erp\UploadsController::class, 'upload']);
  Route::post('/erp/uploads/{groupType}/{groupId}/sort', [\App\Http\Controllers\Erp\UploadsController::class, 'sort']);
  Route::delete('/erp/uploads/{groupType}/{groupId}/remove/{fileId}', [\App\Http\Controllers\Erp\UploadsController::class, 'delete']);

  Route::get('/erp/users', [\App\Http\Controllers\Erp\UsersController::class, 'index'])->middleware('permission:users');
  Route::get('/erp/users/create', [\App\Http\Controllers\Erp\UsersController::class, 'create'])->middleware('permission:users');
  Route::post('/erp/users/create', [\App\Http\Controllers\Erp\UsersController::class, 'create'])->middleware('permission:users');
  Route::get('/erp/users/update/{userId}', [\App\Http\Controllers\Erp\UsersController::class, 'update'])->middleware('permission:users');
  Route::post('/erp/users/update/{userId}', [\App\Http\Controllers\Erp\UsersController::class, 'update'])->middleware('permission:users');
  Route::get('/erp/users/delete/{userId}', [\App\Http\Controllers\Erp\UsersController::class, 'delete'])->middleware('permission:users');

  Route::get('/erp/products', [\App\Http\Controllers\Erp\ProductsController::class, 'index'])->middleware('permission:products');
  Route::get('/erp/products/create', [\App\Http\Controllers\Erp\ProductsController::class, 'create'])->middleware('permission:products');
  Route::post('/erp/products/create', [\App\Http\Controllers\Erp\ProductsController::class, 'create'])->middleware('permission:products');
  Route::get('/erp/products/update/{productId}', [\App\Http\Controllers\Erp\ProductsController::class, 'update'])->middleware('permission:products');
  Route::post('/erp/products/update/{productId}', [\App\Http\Controllers\Erp\ProductsController::class, 'update'])->middleware('permission:products');
  Route::get('/erp/products/delete/{productId}', [\App\Http\Controllers\Erp\ProductsController::class, 'delete'])->middleware('permission:products');
  Route::get('/erp/products/specifications/{productId}', [\App\Http\Controllers\Erp\ProductsController::class, 'specifications'])->middleware('permission:products');
  Route::post('/erp/products/specifications/{productId}', [\App\Http\Controllers\Erp\ProductsController::class, 'specifications'])->middleware('permission:products');
  Route::get('/erp/products/related/{productId}', [\App\Http\Controllers\Erp\ProductsController::class, 'related'])->middleware('permission:products');
  Route::post('/erp/products/related/{productId}', [\App\Http\Controllers\Erp\ProductsController::class, 'related'])->middleware('permission:products');
  Route::get('/erp/products/history/{productId}', [\App\Http\Controllers\Erp\ProductsController::class, 'history'])->middleware('permission:products');
  Route::get('/erp/products/preview/{productId}', [\App\Http\Controllers\Erp\ProductsController::class, 'preview'])->middleware('permission:products');
  Route::get('/erp/products/storage-items/{productId}', [\App\Http\Controllers\Erp\ProductsController::class, 'storageItems'])->middleware('permission:products');

  Route::get('/erp/data-sources/products', [\App\Http\Controllers\Erp\DataSourcesController::class, 'products'])->middleware('permission:products');
  Route::get('/erp/data-sources/product-info/{productId}', [\App\Http\Controllers\Erp\DataSourcesController::class, 'productInfo'])->middleware('permission:products');

  Route::get('/erp/categories', [\App\Http\Controllers\Erp\CategoriesController::class, 'index'])->middleware('permission:products');
  Route::get('/erp/categories/create', [\App\Http\Controllers\Erp\CategoriesController::class, 'create'])->middleware('permission:products');
  Route::post('/erp/categories/create', [\App\Http\Controllers\Erp\CategoriesController::class, 'create'])->middleware('permission:products');
  Route::get('/erp/categories/update/{categoryId}', [\App\Http\Controllers\Erp\CategoriesController::class, 'update'])->middleware('permission:products');
  Route::post('/erp/categories/update/{categoryId}', [\App\Http\Controllers\Erp\CategoriesController::class, 'update'])->middleware('permission:products');
  Route::get('/erp/categories/specifications/{categoryId}', [\App\Http\Controllers\Erp\CategoriesController::class, 'specifications'])->middleware('permission:products');
  Route::post('/erp/categories/specifications/{categoryId}', [\App\Http\Controllers\Erp\CategoriesController::class, 'specifications'])->middleware('permission:products');
  Route::get('/erp/categories/delete/{categoryId}', [\App\Http\Controllers\Erp\CategoriesController::class, 'delete'])->middleware('permission:products');
  Route::get('/erp/categories/products/{categoryId}', [\App\Http\Controllers\Erp\CategoriesController::class, 'products'])->middleware('permission:products');

  Route::get('/erp/manufacturers', [\App\Http\Controllers\Erp\ManufacturersController::class, 'index'])->middleware('permission:products');
  Route::get('/erp/manufacturers/create', [\App\Http\Controllers\Erp\ManufacturersController::class, 'create'])->middleware('permission:products');
  Route::post('/erp/manufacturers/create', [\App\Http\Controllers\Erp\ManufacturersController::class, 'create'])->middleware('permission:products');
  Route::get('/erp/manufacturers/update/{manufacturerId}', [\App\Http\Controllers\Erp\ManufacturersController::class, 'update'])->middleware('permission:products');
  Route::post('/erp/manufacturers/update/{manufacturerId}', [\App\Http\Controllers\Erp\ManufacturersController::class, 'update'])->middleware('permission:products');
  Route::get('/erp/manufacturers/delete/{manufacturerId}', [\App\Http\Controllers\Erp\ManufacturersController::class, 'delete'])->middleware('permission:products');
  Route::get('/erp/manufacturers/products/{manufacturerId}', [\App\Http\Controllers\Erp\ManufacturersController::class, 'products'])->middleware('permission:products');

  Route::get('/erp/specifications', [\App\Http\Controllers\Erp\SpecificationsController::class, 'index'])->middleware('permission:products');
  Route::get('/erp/specifications/create', [\App\Http\Controllers\Erp\SpecificationsController::class, 'create'])->middleware('permission:products');
  Route::post('/erp/specifications/create', [\App\Http\Controllers\Erp\SpecificationsController::class, 'create'])->middleware('permission:products');
  Route::get('/erp/specifications/update/{specificationId}', [\App\Http\Controllers\Erp\SpecificationsController::class, 'update'])->middleware('permission:products');
  Route::post('/erp/specifications/update/{specificationId}', [\App\Http\Controllers\Erp\SpecificationsController::class, 'update'])->middleware('permission:products');
  Route::get('/erp/specifications/delete/{specificationId}', [\App\Http\Controllers\Erp\SpecificationsController::class, 'delete'])->middleware('permission:products');
  Route::get('/erp/specifications/products/{specificationId}', [\App\Http\Controllers\Erp\SpecificationsController::class, 'products'])->middleware('permission:products');

  Route::get('/erp/products-import', [\App\Http\Controllers\Erp\ProductsImportController::class, 'index'])->middleware('permission:products');
  Route::get('/erp/products-import/from-excel', [\App\Http\Controllers\Erp\ProductsImportController::class, 'fromExcel'])->middleware('permission:products');
  Route::post('/erp/products-import/from-excel', [\App\Http\Controllers\Erp\ProductsImportController::class, 'fromExcel'])->middleware('permission:products');
  Route::get('/erp/products-import/export-all', [\App\Http\Controllers\Erp\ProductsImportController::class, 'exportAll'])->middleware('permission:products');

  Route::get('/erp/banners', [\App\Http\Controllers\Erp\BannersController::class, 'index'])->middleware('permission:products');
  Route::post('/erp/banners', [\App\Http\Controllers\Erp\BannersController::class, 'index'])->middleware('permission:products');

  Route::get('/erp/orders', [\App\Http\Controllers\Erp\OrdersController::class, 'index'])->middleware('permission:orders');
  Route::get('/erp/orders/view/{orderId}', [\App\Http\Controllers\Erp\OrdersController::class, 'view'])->middleware('permission:orders');
  Route::get('/erp/orders/prepare', [\App\Http\Controllers\Erp\OrdersController::class, 'prepare'])->middleware('permission:orders');
  Route::get('/erp/orders/create', [\App\Http\Controllers\Erp\OrdersController::class, 'create'])->middleware('permission:orders');
  Route::post('/erp/orders/create', [\App\Http\Controllers\Erp\OrdersController::class, 'create'])->middleware('permission:orders');
  Route::get('/erp/orders/update/{orderId}', [\App\Http\Controllers\Erp\OrdersController::class, 'update'])->middleware('permission:orders');
  Route::post('/erp/orders/update/{orderId}', [\App\Http\Controllers\Erp\OrdersController::class, 'update'])->middleware('permission:orders');
  Route::get('/erp/orders/documents/{orderId}', [\App\Http\Controllers\Erp\OrdersController::class, 'documents'])->middleware('permission:orders');
  Route::get('/erp/orders/incomes-allocations/{orderId}', [\App\Http\Controllers\Erp\OrdersController::class, 'incomesAllocations'])->middleware('permission:orders');

  Route::get('/erp/shipments/speedy', [\App\Http\Controllers\Erp\Shipments\SpeedyController::class, 'index'])->middleware('permission:shipments');
  Route::get('/erp/shipments/speedy/view/{shipmentId}', [\App\Http\Controllers\Erp\Shipments\SpeedyController::class, 'view'])->middleware('permission:shipments');
  Route::get('/erp/shipments/speedy/create', [\App\Http\Controllers\Erp\Shipments\SpeedyController::class, 'create'])->middleware('permission:shipments');
  Route::post('/erp/shipments/speedy/create', [\App\Http\Controllers\Erp\Shipments\SpeedyController::class, 'create'])->middleware('permission:shipments');
  Route::get('/erp/shipments/speedy/search', [\App\Http\Controllers\Erp\Shipments\SpeedyController::class, 'search'])->middleware('permission:shipments');
  Route::post('/erp/shipments/speedy/calculate', [\App\Http\Controllers\Erp\Shipments\SpeedyController::class, 'calculate'])->middleware('permission:shipments');

  Route::get('/erp/incomes', [\App\Http\Controllers\Erp\IncomesController::class, 'index'])->middleware('permission:incomes');
  Route::get('/erp/incomes/create', [\App\Http\Controllers\Erp\IncomesController::class, 'create'])->middleware('permission:incomes');
  Route::post('/erp/incomes/create', [\App\Http\Controllers\Erp\IncomesController::class, 'create'])->middleware('permission:incomes');
  Route::get('/erp/incomes/update/{incomeId}', [\App\Http\Controllers\Erp\IncomesController::class, 'update'])->middleware('permission:incomes');
  Route::post('/erp/incomes/update/{incomeId}', [\App\Http\Controllers\Erp\IncomesController::class, 'update'])->middleware('permission:incomes');
  Route::get('/erp/incomes/delete/{incomeId}', [\App\Http\Controllers\Erp\IncomesController::class, 'delete'])->middleware('permission:incomes');

  Route::get('/erp/offers', [\App\Http\Controllers\Erp\OffersController::class, 'index'])->middleware('permission:documents');
  Route::get('/erp/offers/create', [\App\Http\Controllers\Erp\OffersController::class, 'create'])->middleware('permission:documents');
  Route::post('/erp/offers/create', [\App\Http\Controllers\Erp\OffersController::class, 'create'])->middleware('permission:documents');
  Route::get('/erp/offers/update/{offerId}', [\App\Http\Controllers\Erp\OffersController::class, 'update'])->middleware('permission:documents');
  Route::post('/erp/offers/update/{offerId}', [\App\Http\Controllers\Erp\OffersController::class, 'update'])->middleware('permission:documents');
  Route::get('/erp/offers/delete/{offerId}', [\App\Http\Controllers\Erp\OffersController::class, 'delete'])->middleware('permission:documents');
  Route::get('/erp/offers/preview/{lang}/{offerId}/{format}/', [\App\Http\Controllers\Erp\OffersController::class, 'preview'])->middleware('permission:documents');

  Route::get('/erp/demos', [\App\Http\Controllers\Erp\DemosController::class, 'index'])->middleware('permission:demo');
  Route::get('/erp/demos/create', [\App\Http\Controllers\Erp\DemosController::class, 'create'])->middleware('permission:demo');
  Route::post('/erp/demos/create', [\App\Http\Controllers\Erp\DemosController::class, 'create'])->middleware('permission:demo');
  Route::get('/erp/demos/update/{demoId}', [\App\Http\Controllers\Erp\DemosController::class, 'update'])->middleware('permission:demo');
  Route::post('/erp/demos/update/{demoId}', [\App\Http\Controllers\Erp\DemosController::class, 'update'])->middleware('permission:demo');
  Route::get('/erp/demos/delete/{demoId}', [\App\Http\Controllers\Erp\DemosController::class, 'delete'])->middleware('permission:demo');

  Route::get('/erp/storage-entries', [\App\Http\Controllers\Erp\StorageEntriesController::class, 'index'])->middleware('permission:storage');
  Route::get('/erp/storage-entries/create', [\App\Http\Controllers\Erp\StorageEntriesController::class, 'create'])->middleware('permission:storage');
  Route::post('/erp/storage-entries/create', [\App\Http\Controllers\Erp\StorageEntriesController::class, 'create'])->middleware('permission:storage');
  Route::get('/erp/storage-entries/update/{documentId}', [\App\Http\Controllers\Erp\StorageEntriesController::class, 'update'])->middleware('permission:storage');
  Route::post('/erp/storage-entries/update/{documentId}', [\App\Http\Controllers\Erp\StorageEntriesController::class, 'update'])->middleware('permission:storage');
  Route::get('/erp/storage-entries/delete/{documentId}', [\App\Http\Controllers\Erp\StorageEntriesController::class, 'delete'])->middleware('permission:storage');

  Route::get('/erp/storage-entries/income-credit-memos/{documentId}', [\App\Http\Controllers\Erp\StorageEntriesIncomeCreditMemosController::class, 'index'])->middleware('permission:storage');
  Route::get('/erp/storage-entries/income-credit-memos/create/{documentId}', [\App\Http\Controllers\Erp\StorageEntriesIncomeCreditMemosController::class, 'create'])->middleware('permission:storage');
  Route::post('/erp/storage-entries/income-credit-memos/create/{documentId}', [\App\Http\Controllers\Erp\StorageEntriesIncomeCreditMemosController::class, 'create'])->middleware('permission:storage');
  Route::get('/erp/storage-entries/income-credit-memos/view/{id}', [\App\Http\Controllers\Erp\StorageEntriesIncomeCreditMemosController::class, 'view'])->middleware('permission:storage');

  Route::get('/erp/storage-items', [\App\Http\Controllers\Erp\StorageItemsController::class, 'index'])->middleware('permission:storage');
  Route::get('/erp/storage-report/inventory/items', [\App\Http\Controllers\Erp\StorageReportController::class, 'inventoryItems'])->middleware('permission:storage');
  Route::get('/erp/storage-items/view/{itemId}', [\App\Http\Controllers\Erp\StorageItemsController::class, 'view'])->middleware('permission:storage');
  Route::get('/erp/storage-items/writeoff-protocol/{itemId}', [\App\Http\Controllers\Erp\StorageItemsController::class, 'writeoffProtocol'])->middleware('permission:storage');
  Route::post('/erp/storage-items/writeoff-protocol/{itemId}', [\App\Http\Controllers\Erp\StorageItemsController::class, 'writeoffProtocol'])->middleware('permission:storage');
  Route::get('/erp/storage-items/writeoff-protocol/{writeOffProtocolId}/pdf', [\App\Http\Controllers\Erp\StorageItemsController::class, 'writeoffProtocolPdf'])->middleware('permission:storage');

  Route::get('/erp/storage-report', [\App\Http\Controllers\Erp\StorageReportController::class, 'index'])->middleware('permission:storage');
  Route::get('/erp/storage-report/products', [\App\Http\Controllers\Erp\StorageReportController::class, 'products'])->middleware('permission:storage');
  Route::get('/erp/storage-report/nra', [\App\Http\Controllers\Erp\StorageReportController::class, 'nra'])->middleware('permission:storage');
  Route::get('/erp/storage-report/inventory', [\App\Http\Controllers\Erp\StorageReportController::class, 'inventory'])->middleware('permission:storage');

  Route::get('/erp/documents', [\App\Http\Controllers\Erp\DocumentsController::class, 'index'])->middleware('permission:documents');
  Route::get('/erp/documents/prepare/{type}', [\App\Http\Controllers\Erp\DocumentsController::class, 'prepare'])->middleware('permission:documents');
  Route::get('/erp/documents/create/{type}/{refDocumentId}', [\App\Http\Controllers\Erp\DocumentsController::class, 'create'])->middleware('permission:documents');
  Route::post('/erp/documents/create/{type}/{refDocumentId}', [\App\Http\Controllers\Erp\DocumentsController::class, 'create'])->middleware('permission:documents');
  Route::get('/erp/documents/view/{documentId}', [\App\Http\Controllers\Erp\DocumentsController::class, 'view'])->middleware('permission:documents');
  Route::get('/erp/documents/delete/{documentId}', [\App\Http\Controllers\Erp\DocumentsController::class, 'delete'])->middleware('permission:documents');
  Route::get('/erp/documents/notify/{documentId}', [\App\Http\Controllers\Erp\DocumentsController::class, 'notify'])->middleware('permission:documents');
  Route::get('/erp/documents/preview/{lang}/{documentId}/{type}/{format}/', [\App\Http\Controllers\Erp\DocumentsController::class, 'preview'])->middleware('permission:documents');
  Route::get('/erp/documents/incomes-allocations/{documentId}', [\App\Http\Controllers\Erp\DocumentsController::class, 'incomesAllocations'])->middleware('permission:documents');

  Route::get('/erp/customers', [\App\Http\Controllers\Erp\CustomersController::class, 'index'])->middleware('permission:customers');
  Route::get('/erp/customers/create', [\App\Http\Controllers\Erp\CustomersController::class, 'create'])->middleware('permission:customers');
  Route::post('/erp/customers/create', [\App\Http\Controllers\Erp\CustomersController::class, 'create'])->middleware('permission:customers');
  Route::get('/erp/customers/update/{customerId}', [\App\Http\Controllers\Erp\CustomersController::class, 'update'])->middleware('permission:customers');
  Route::post('/erp/customers/update/{customerId}', [\App\Http\Controllers\Erp\CustomersController::class, 'update'])->middleware('permission:customers');
  Route::get('/erp/customers/delete/{customerId}', [\App\Http\Controllers\Erp\CustomersController::class, 'delete'])->middleware('permission:customers');
  Route::get('/erp/customers/orders/{customerId}', [\App\Http\Controllers\Erp\CustomersController::class, 'orders'])->middleware('permission:customers');
  Route::get('/erp/customers/incomes/{customerId}', [\App\Http\Controllers\Erp\CustomersController::class, 'incomes'])->middleware('permission:customers');
  Route::get('/erp/customers/documents/{customerId}', [\App\Http\Controllers\Erp\CustomersController::class, 'documents'])->middleware('permission:customers');

  Route::get('/erp/customers/addresses/{customerId}', [\App\Http\Controllers\Erp\CustomersAddressesController::class, 'index'])->middleware('permission:customers');
  Route::get('/erp/customers/addresses/create/{customerId}', [\App\Http\Controllers\Erp\CustomersAddressesController::class, 'create'])->middleware('permission:customers');
  Route::post('/erp/customers/addresses/create/{customerId}', [\App\Http\Controllers\Erp\CustomersAddressesController::class, 'create'])->middleware('permission:customers');
  Route::get('/erp/customers/addresses/update/{id}', [\App\Http\Controllers\Erp\CustomersAddressesController::class, 'update'])->middleware('permission:customers');
  Route::post('/erp/customers/addresses/update/{id}', [\App\Http\Controllers\Erp\CustomersAddressesController::class, 'update'])->middleware('permission:customers');
  Route::get('/erp/customers/addresses/delete/{id}', [\App\Http\Controllers\Erp\CustomersAddressesController::class, 'delete'])->middleware('permission:customers');

  Route::get('/erp/customers-groups', [\App\Http\Controllers\Erp\CustomersGroupsController::class, 'index'])->middleware('permission:customers');
  Route::get('/erp/customers-groups/create', [\App\Http\Controllers\Erp\CustomersGroupsController::class, 'create'])->middleware('permission:customers');
  Route::post('/erp/customers-groups/create', [\App\Http\Controllers\Erp\CustomersGroupsController::class, 'create'])->middleware('permission:customers');
  Route::get('/erp/customers-groups/update/{addressId}', [\App\Http\Controllers\Erp\CustomersGroupsController::class, 'update'])->middleware('permission:customers');
  Route::post('/erp/customers-groups/update/{addressId}', [\App\Http\Controllers\Erp\CustomersGroupsController::class, 'update'])->middleware('permission:customers');
  Route::get('/erp/customers-groups/delete/{addressId}', [\App\Http\Controllers\Erp\CustomersGroupsController::class, 'delete'])->middleware('permission:customers');
  Route::get('/erp/customers-groups/customers/{addressId}', [\App\Http\Controllers\Erp\CustomersGroupsController::class, 'customers'])->middleware('permission:customers');

  Route::get('/erp/sales-representatives', [\App\Http\Controllers\Erp\SalesRepresentativesController::class, 'index'])->middleware('permission:customers');
  Route::get('/erp/sales-representatives/create', [\App\Http\Controllers\Erp\SalesRepresentativesController::class, 'create'])->middleware('permission:customers');
  Route::post('/erp/sales-representatives/create', [\App\Http\Controllers\Erp\SalesRepresentativesController::class, 'create'])->middleware('permission:customers');
  Route::get('/erp/sales-representatives/update/{representativeId}', [\App\Http\Controllers\Erp\SalesRepresentativesController::class, 'update'])->middleware('permission:customers');
  Route::post('/erp/sales-representatives/update/{representativeId}', [\App\Http\Controllers\Erp\SalesRepresentativesController::class, 'update'])->middleware('permission:customers');
  Route::get('/erp/sales-representatives/delete/{representativeId}', [\App\Http\Controllers\Erp\SalesRepresentativesController::class, 'delete'])->middleware('permission:customers');
  Route::get('/erp/sales-representatives/customers/{representativeId}', [\App\Http\Controllers\Erp\SalesRepresentativesController::class, 'customers'])->middleware('permission:customers');
  Route::get('/erp/sales-representatives/documents/{representativeId}', [\App\Http\Controllers\Erp\SalesRepresentativesController::class, 'documents'])->middleware('permission:customers');

  Route::get('/erp/feeds-imports', [\App\Http\Controllers\Erp\FeedsImportController::class, 'index'])->middleware('permission:suppliers');
  Route::get('/erp/feeds-imports/create', [\App\Http\Controllers\Erp\FeedsImportController::class, 'create'])->middleware('permission:suppliers');
  Route::post('/erp/feeds-imports/create', [\App\Http\Controllers\Erp\FeedsImportController::class, 'create'])->middleware('permission:suppliers');
  Route::get('/erp/feeds-imports/update/{feedId}', [\App\Http\Controllers\Erp\FeedsImportController::class, 'update'])->middleware('permission:suppliers');
  Route::post('/erp/feeds-imports/update/{feedId}', [\App\Http\Controllers\Erp\FeedsImportController::class, 'update'])->middleware('permission:suppliers');
  Route::get('/erp/feeds-imports/items/{feedId}', [\App\Http\Controllers\Erp\FeedsImportController::class, 'items'])->middleware('permission:suppliers');
  Route::get('/erp/feeds-imports/delete/{feedId}', [\App\Http\Controllers\Erp\FeedsImportController::class, 'delete'])->middleware('permission:suppliers');

  Route::get('/erp/feeds-imports-items/related', [\App\Http\Controllers\Erp\FeedsImportItemsController::class, 'related'])->middleware('permission:suppliers');
  Route::post('/erp/feeds-imports-items/set-related-product/{itemId}', [\App\Http\Controllers\Erp\FeedsImportItemsController::class, 'setRelatedProduct'])->middleware('permission:suppliers');
  Route::post('/erp/feeds-imports-items/unset-related-product/{itemId}', [\App\Http\Controllers\Erp\FeedsImportItemsController::class, 'unsetRelatedProduct'])->middleware('permission:suppliers');
  Route::post('/erp/feeds-imports-items/set-skip-sync/{itemId}', [\App\Http\Controllers\Erp\FeedsImportItemsController::class, 'setSkipSync'])->middleware('permission:suppliers');
  Route::post('/erp/feeds-imports-items/related/add-product/{itemId}', [\App\Http\Controllers\Erp\FeedsImportItemsController::class, 'relatedAddProduct'])->middleware('permission:suppliers');
  Route::get('/erp/feeds-imports-items/conflicts', [\App\Http\Controllers\Erp\FeedsImportItemsController::class, 'conflicts'])->middleware('permission:suppliers');
  Route::post('/erp/feeds-imports-items/conflicts/set-lead/{itemId}', [\App\Http\Controllers\Erp\FeedsImportItemsController::class, 'conflictsSetLeadRecord'])->middleware('permission:suppliers');
  Route::post('/erp/feeds-imports-items/bulk', [\App\Http\Controllers\Erp\FeedsImportItemsController::class, 'bulk'])->middleware('permission:suppliers');

  Route::get('/erp/search-report', [\App\Http\Controllers\Erp\SearchReportController::class, 'index'])->middleware('permission:search');

  Route::get('/erp/feeds-imports-dashboard', [\App\Http\Controllers\Erp\FeedsImportsDashboardController::class, 'index'])->middleware('permission:suppliers');

  Route::get('/erp/schedulers', [\App\Http\Controllers\Erp\SchedulersController::class, 'index'])->middleware('permission:system');
  Route::get('/erp/schedulers/view/{jobId}', [\App\Http\Controllers\Erp\SchedulersController::class, 'view'])->middleware('permission:system');
  Route::get('/erp/schedulers/run/{jobId}', [\App\Http\Controllers\Erp\SchedulersController::class, 'run'])->middleware('permission:system');

  Route::get('/erp/mails', [\App\Http\Controllers\Erp\MailsController::class, 'index'])->middleware('permission:system');
  Route::get('/erp/mails/view/{mailId}', [\App\Http\Controllers\Erp\MailsController::class, 'view'])->middleware('permission:system');
  Route::get('/erp/mails/test', [\App\Http\Controllers\Erp\MailsController::class, 'test'])->middleware('permission:system');
  Route::post('/erp/mails/test', [\App\Http\Controllers\Erp\MailsController::class, 'test'])->middleware('permission:system');

  Route::get('/erp/config', [\App\Http\Controllers\Erp\ConfigController::class, 'index'])->middleware('permission:system');
  Route::post('/erp/config', [\App\Http\Controllers\Erp\ConfigController::class, 'index'])->middleware('permission:system');

  Route::get('/erp/api-keys', [\App\Http\Controllers\Erp\ApiKeysController::class, 'index'])->middleware('permission:system');
});
