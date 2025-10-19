<div id="js-usage-status-alert"></div>
<div id="js-data-source-alert"></div>

<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        {{-- no .mt-3 for 1st title --}}
        <h2 class="h5 pb-2 border-bottom border-dashed">Данни за продукта</h2>
      </div>

      <div class="col-12 col-xl-6" data-suggest-hidden="no">
        <label class="app-form-label required" for="f-mpn">MPN (Manufacturer Part Number)</label>
        <input type="text" class="form-control @if($errors->has('mpn')) is-invalid @endif" id="f-mpn" name="mpn" value="{{ $product->mpn }}" placeholder="12345..." required/>
        @if($errors->has('mpn'))
          <div class="invalid-feedback">
            {{ $errors->first('mpn') }}
          </div>
        @endif

        <div data-suggest="mpn" style="display: none;"></div>
      </div>

      <div class="col-12 col-xl-6" data-suggest-hidden="no">
        <label class="app-form-label" for="f-ean">EAN (European Article Number)</label>
        <input type="text" class="form-control @if($errors->has('ean')) is-invalid @endif" id="f-ean" name="ean" value="{{ $product->ean }}" placeholder="1234567890123..."/>
        @if($errors->has('ean'))
          <div class="invalid-feedback">
            {{ $errors->first('ean') }}
          </div>
        @endif

        <div data-suggest="ean" style="display: none;"></div>
      </div>

      <div class="col-12 col-xl-6" data-suggest-hidden="no">
        <label class="app-form-label required" for="f-nameBg">Име на продукта [BG]</label>
        <input type="text" class="form-control @if($errors->has('nameBg')) is-invalid @endif" id="f-nameBg" name="nameBg" value="{{ $product->nameBg }}" placeholder="Име на продукта..." required/>
        @if($errors->has('nameBg'))
          <div class="invalid-feedback">
            {{ $errors->first('nameBg') }}
          </div>
        @endif

        <div data-suggest="nameBg" style="display: none;"></div>

        <script type="module">
          $(function () {
            const $nameBg = $('#f-nameBg');
            const $nameEn = $('#f-nameEn');

            let prevValue = $nameBg.val();

            $nameBg.on('change keyup', function () {
              if (!$nameEn.val() || prevValue === $nameEn.val()) {
                $nameEn.val($nameBg.val());
                prevValue = $nameBg.val();
              }
            });
          });
        </script>
      </div>

      <div class="col-12 col-xl-6" data-suggest-hidden="no">
        <label class="app-form-label required" for="f-nameEn">Име на продукта [EN]</label>
        <input type="text" class="form-control @if($errors->has('nameEn')) is-invalid @endif" id="f-nameEn" name="nameEn" value="{{ $product->nameEn }}" placeholder="Name of the product..." required/>
        @if($errors->has('nameEn'))
          <div class="invalid-feedback">
            {{ $errors->first('nameEn') }}
          </div>
        @endif

        <div data-suggest="nameEn" style="display: none;"></div>
      </div>

      <div class="col-12 col-xl-6" data-suggest-hidden="no">
        <label class="app-form-label" for="f-manufacturerId">Производител</label>
        <select class="form-select @if($errors->has('manufacturerId')) is-invalid @endif" id="f-manufacturerId" name="manufacturerId">
          <option value="">Без производител</option>
          @foreach ($manufacturers as $row)
            <option value="{{ $row->id }}" {{ $product->manufacturerId == $row->id ? 'selected' : '' }}>
              {{ $row->name }}
            </option>
          @endforeach
        </select>
        @if($errors->has('manufacturerId'))
          <div class="invalid-feedback">
            {{ $errors->first('manufacturerId') }}
          </div>
        @endif

        <div data-suggest="manufacturerId" style="display: none;"></div>
      </div>

      <div class="col-12 col-xl-6" data-suggest-hidden="no">
        <label id="f-categories-label" class="form-label {{ $product->usageStatus && $product->usageStatus->value === \App\Enums\ProductUsageStatus::ListedOnline->value ? 'required' : '' }}" for="f-categories">Категории и подкатегории</label>
        <select class="form-select" id="f-categories" name="categories[]" multiple="multiple" size="1">
          @include('erp.products.partials.categories-assign', [
            'categories' => $categories,
            'assigned' => $assignedCategories,
          ])
        </select>
        @if($errors->has('categories'))
          <div class="text-danger fs-9">
            {{ $errors->first('categories') }}
          </div>
        @endif

        <div data-suggest="categories" style="display: none;"></div>

        <script type="module">
          $(function () {
            const $categories = $('#f-categories');
            $categories.select2();

            const requiredStatus = '{{ \App\Enums\ProductUsageStatus::ListedOnline->value }}';
            const $usageStatus = $('#f-usageStatus');
            const $label = $('#f-categories-label');

            function toggleCategoriesRequired() {
              $label.toggleClass('required', $usageStatus.val() === requiredStatus);
            }

            toggleCategoriesRequired();
            $usageStatus.change(toggleCategoriesRequired);
          });
        </script>
      </div>

      <div class="col-12 col-xl-4" data-suggest-hidden="yes">
        <label class="app-form-label" for="f-usageStatus">Статус на продукта</label>
        <select class="form-select @if($errors->has('usageStatus')) is-invalid @endif" id="f-usageStatus" name="usageStatus">
          <option value="">-</option>
          @foreach(App\Enums\ProductUsageStatus::cases() as $status)
            <option value="{{ $status->value }}" {{ ($product->usageStatus && $product->usageStatus->value === $status->value) ? 'selected' : '' }}>
              {{ \App\Services\MapService::productUsageStatus($status)->label }}
            </option>
          @endforeach
        </select>
        @if($errors->has('usageStatus'))
          <div class="invalid-feedback">{{ $errors->first('usageStatus') }}</div>
        @endif

        @if(Request::is('erp/products/update/*'))
          <script type="module">
            $('#f-usageStatus')
              .change(function () {
                if ($(this).val() === @json(\App\Enums\ProductUsageStatus::InternalUse->value)) {
                  $('#js-usage-status-alert').html(`
                    <div class="alert alert-danger" role="alert">
                      Продуктът е отбелязан като предназначен за фирмено ползване. Промяната на статуса му на 'Предлага се за продажба' може да доведе до несъответствия в системата.
                    </div>
                  `);
                } else {
                  $('#js-usage-status-alert').html(``);
                }
              })
              .change();
          </script>
        @endif
      </div>

      <div class="col-12 col-xl-4" data-suggest-hidden="yes">
        <label class="form-label" for="f-isFeatured">На фокус</label>
        <select class="form-select @if($errors->has('isFeatured')) is-invalid @endif" id="f-isFeatured" name="isFeatured">
          <option value="0" @if (!$product->isFeatured) selected @endif>Не</option>
          <option value="1" @if ($product->isFeatured) selected @endif>Да се покаже в начална страница в магазина</option>
        </select>
        @if($errors->has('isFeatured'))
          <div class="invalid-feedback">
            {{ $errors->first('isFeatured') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4" data-suggest-hidden="yes">
        <label class="app-form-label" for="f-warrantyPeriod">Гаранция (в месеци)</label>
        <input type="number" step="1" min="0" class="form-control @if($errors->has('warrantyPeriod')) is-invalid @endif" id="f-warrantyPeriod" name="warrantyPeriod" value="{{ $product->warrantyPeriod }}" placeholder="12..."/>
        @if($errors->has('warrantyPeriod'))
          <div class="invalid-feedback">
            {{ $errors->first('warrantyPeriod') }}
          </div>
        @endif

        <div data-suggest="warrantyPeriod" style="display: none;"></div>
      </div>

      <div class="col-12" data-suggest-hidden="yes">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Доставка</h2>
      </div>

      <div class="col-12 col-xl-4" data-suggest-hidden="yes">
        <label class="form-label" for="f-onStock">На склад</label>
        <select class="form-select @if($errors->has('onStock')) is-invalid @endif" id="f-onStock" name="onStock">
          <option value="1" @if ($product->onStock) selected @endif>На склад (може да се изпрати днес)</option>
          <option value="0" @if (!$product->onStock) selected @endif>Изисква доставка</option>
        </select>
        @if($errors->has('onStock'))
          <div class="invalid-feedback">
            {{ $errors->first('onStock') }}
          </div>
        @endif

        <script type="module">
          $(function () {
            $('#f-onStock, #f-deliveryDays')
              .change(function () {
                const onStock = $('#f-onStock').val() === '1';
                const $deliveryDays = $('#f-deliveryDays');
                const $deliveryDaysWrapper = $('#js-deliveryDays-wrapper');

                $deliveryDays.prop('readonly', onStock);

                if (onStock) {
                  $deliveryDays.val(0);
                  $deliveryDaysWrapper.animate({
                    opacity: 0.5
                  });
                } else {
                  if (!parseInt($deliveryDays.val())) {
                    $deliveryDays.val(5);
                  }

                  $deliveryDaysWrapper.animate({
                    opacity: 1
                  });
                }
              })
              .change();
          });
        </script>
      </div>

      <div class="col-12 col-xl-4" id="js-deliveryDays-wrapper" data-suggest-hidden="yes">
        <label class="app-form-label" for="f-deliveryDays">Дни за доставка</label>
        <input type="number" step="1" min="0" max="365" class="form-control @if($errors->has('deliveryDays')) is-invalid @endif" id="f-deliveryDays" name="deliveryDays" value="{{ $product->deliveryDays }}" placeholder="5..." data-bs-toggle="tooltip" data-bs-trigger="focus" data-bs-placement="top"/>
        @if($errors->has('deliveryDays'))
          <div class="invalid-feedback">
            {{ $errors->first('deliveryDays') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4" data-suggest-hidden="yes">
        <label class="app-form-label" for="f-quantity">Налично количество</label>
        <input type="number" step="1" min="0" class="form-control @if($errors->has('quantity')) is-invalid @endif" id="f-quantity" name="quantity" value="{{ $product->quantity }}" placeholder="1234..." data-bs-toggle="tooltip" data-bs-trigger="focus" data-bs-placement="top" title="Ако е празно, няма да се продава"/>
        @if($errors->has('quantity'))
          <div class="invalid-feedback">
            {{ $errors->first('quantity') }}
          </div>
        @endif
      </div>

      <div class="col-12" data-suggest-hidden="yes">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Цени</h2>
      </div>

      <div class="col-12" data-suggest-hidden="yes">
        <div class="row gy-2">
          <div class="col-12 col-xl">
            <label class="app-form-label required" for="f-price">Продажна цена</label>
            <div class="input-group">
              <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
              <input type="number" step="0.01" min="0" class="form-control @if($errors->has('price')) is-invalid @endif" id="f-price" name="price" value="{{ $product->price }}" placeholder="1234..." data-bs-toggle="tooltip" data-bs-trigger="focus" data-bs-placement="top" title="Цената на която продуктът се продава в магазина" required/>
              @if($errors->has('price'))
                <div class="invalid-feedback">
                  {{ $errors->first('price') }}
                </div>
              @endif
            </div>
          </div>

          <div class="col-12 col-xl">
            <label class="app-form-label" for="f-purchasePrice">Доставна цена</label>
            <div class="input-group">
              <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
              <input type="number" step="0.01" min="0" class="form-control @if($errors->has('purchasePrice')) is-invalid @endif" id="f-purchasePrice" name="purchasePrice" value="{{ $product->purchasePrice }}" placeholder="1123..." data-bs-toggle="tooltip" data-bs-trigger="focus" data-bs-placement="top" title="Цената на която продуктът се закупува от доставчика"/>
              @if($errors->has('purchasePrice'))
                <div class="invalid-feedback">
                  {{ $errors->first('purchasePrice') }}
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      <div class="col-12" data-suggest-hidden="no">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Размер и тегло</h2>
      </div>

      <div class="col-12 col-xl-3" data-suggest-hidden="no">
        <label class="app-form-label" for="f-weight">Тегло (с опаковка) кг.</label>
        <input type="number" step="0.001" min="0" class="form-control @if($errors->has('weight')) is-invalid @endif" id="f-weight" name="weight" value="{{ $product->weight }}" placeholder="123.45..."/>
        @if($errors->has('weight'))
          <div class="invalid-feedback">
            {{ $errors->first('weight') }}
          </div>
        @endif

        <div data-suggest="weight" style="display: none;"></div>
      </div>

      <div class="col-12 col-xl-3" data-suggest-hidden="no">
        <label class="form-label" for="f-width">Широчина (Width) см.</label>
        <input type="number" step="0.01" min="0" class="form-control @if($errors->has('width')) is-invalid @endif" id="f-width" name="width" value="{{ $product->width }}" placeholder="50.00..."/>
        @if($errors->has('width'))
          <div class="invalid-feedback">
            {{ $errors->first('width') }}
          </div>
        @endif

        <div data-suggest="width" style="display: none;"></div>
      </div>

      <div class="col-12 col-xl-3" data-suggest-hidden="no">
        <label class="app-form-label" for="f-height">Височина (Height) см.</label>
        <input type="number" step="0.01" min="0" class="form-control @if($errors->has('height')) is-invalid @endif" id="f-height" name="height" value="{{ $product->height }}" placeholder="60.00..."/>
        @if($errors->has('height'))
          <div class="invalid-feedback">
            {{ $errors->first('height') }}
          </div>
        @endif

        <div data-suggest="height" style="display: none;"></div>
      </div>

      <div class="col-12 col-xl-3" data-suggest-hidden="no">
        <label class="app-form-label" for="f-length">Дълбочина (Length) см.</label>
        <input type="number" step="0.01" min="0" class="form-control @if($errors->has('length')) is-invalid @endif" id="f-length" name="length" value="{{ $product->length }}" placeholder="70.00..."/>
        @if($errors->has('length'))
          <div class="invalid-feedback">
            {{ $errors->first('length') }}
          </div>
        @endif

        <div data-suggest="length" style="display: none;"></div>
      </div>

      <div class="col-12" data-suggest-hidden="no">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Описание на продукта</h2>
      </div>

      <div class="col-12 col-xl-6" data-suggest-hidden="no">
        <h2 class="app-form-label">Описание [BG]</h2>

        <div>
          <div id="product-editor-bg">
            {!! $product->descriptionBg !!}
          </div>

          <input type="hidden" id="f-descriptionBg" name="descriptionBg"/>

          <script type="module">
            const editor = new Quill('#product-editor-bg', {
              placeholder: 'Описание на продукта...',
              theme: 'snow'
            });

            const setValue = () => $('#f-descriptionBg').val(editor.container.firstChild.innerHTML);
            setValue();

            editor.on('text-change', setValue);

            window._descriptionBg = editor;
          </script>

          <div data-suggest="descriptionBg" style="display: none;"></div>
        </div>
      </div>

      <div class="col-12 col-xl-6" data-suggest-hidden="no">
        <h2 class="app-form-label">Описание [EN]</h2>

        <div>
          <div id="product-editor-en">
            {!! $product->descriptionEn !!}
          </div>

          <input type="hidden" id="f-descriptionEn" name="descriptionEn"/>

          <script type="module">
            const editor = new Quill('#product-editor-en', {
              placeholder: 'Описание на продукта...',
              theme: 'snow'
            });

            const setValue = () => $('#f-descriptionEn').val(editor.container.firstChild.innerHTML);
            setValue();

            editor.on('text-change', setValue);

            window._descriptionEn = editor;
          </script>
        </div>

        <div data-suggest="descriptionEn" style="display: none;"></div>
      </div>
    </div>
  </div>
</div>

<hr class="my-3"/>

<div class="card">
  <div class="card-body pb-1">
    <h2 class="h4 card-title mb-4">Добавяне на изображения</h2>
    <script type="module">
      window.productsLabelCallback = (seq) => {
        return seq === 0 ? 'Cover' : seq;
      };
    </script>

    @include('erp.uploads.uploader', [
      'groupType' => \App\Enums\UploadGroupType::Products->value,
      'groupId' => $product->fileGroupId,
      'fieldName' => 'fileGroupId',
      'acceptedFiles' => 'image/*',
      'labelCallback' => 'window.productsLabelCallback',
    ])
  </div>

  <div class="card-body pb-4" data-suggest="gallery" style="display: none;">
    <hr/>

    <h2 class="h4 card-title mb-4">
      <i class="fa-regular fa-sparkles"></i>
      Предложения за изображения
    </h2>

    <div class="row g-3" data-suggest="gallery-images"></div>
  </div>
</div>

<hr class="my-3"/>

<div class="card" data-suggest-hidden="yes">
  <div class="card-body pb-1">
    <h2 class="h4 card-title mb-4">Добавяне на файлове</h2>
    @include('erp.uploads.uploader', [
      'groupType' => \App\Enums\UploadGroupType::ProductDownloads->value,
      'groupId' => $product->downloadsGroupId,
      'fieldName' => 'downloadsGroupId',
    ])
  </div>
</div>

@if(isset($feedItems) && count($feedItems))
  <hr class="my-3"/>

  <div class="card" data-suggest-hidden="yes">
    <div class="card-body">
      <h2 class="h4 card-title mb-4">Спиране на синхронизация</h2>
      <p class="fs-9">Този продукт е свързан с {{ count($feedItems) }} записа от автоматичната XML синхронизация с доставчици. Цената и количеството му може да бъдат автоматично обновявани. Ако не желаете това да се случва, изберете една от наличните опции по-долу.</p>
      <div class="table-responsive mb-3">
        <table class="table table-sm fs-9">
          <thead>
          <tr>
            <th>Име на доставчик</th>
            <th>Име запис</th>
            <th>Цена</th>
            <th>Количество</th>
          </tr>
          </thead>
          <tbody>
          @foreach($feedItems as $row)
            <tr>
              <td>{{ $row->feedImport->providerName ?? '-' }}</td>
              <td>{{ $row->itemName }}</td>
              <td>{{ price($row->itemPrice) }}</td>
              <td>{{ $row->itemQuantity }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>

      <div>
        <label class="fw-bold" for="f-nonSyncStatus">Спиране на синхронизацията</label>
        <select class="form-select @if($errors->has('nonSyncStatus')) is-invalid @endif" id="f-nonSyncStatus" name="nonSyncStatus">
          <option value="">-</option>
          @foreach(App\Enums\ProductNonSyncStatus::cases() as $status)
            <option value="{{ $status->value }}" {{ ($product->nonSyncStatus && $product->nonSyncStatus->value === $status->value) ? 'selected' : '' }}>
              {{ \App\Services\MapService::productNonSyncStatus($status)->label }}
            </option>
          @endforeach
        </select>
        @if($errors->has('nonSyncStatus'))
          <div class="invalid-feedback">{{ $errors->first('nonSyncStatus') }}</div>
        @endif
      </div>
    </div>
  </div>
@endif

@if ($product->id)
  @if (request()->dataSourceMode)
    <div class="fixed-bottom bg-body border-top w-100 pt-4">
      <div class="d-flex justify-content-center">
        <a class="btn btn-info btn-sm" id="js-fill-all-btn">
          <i class="fa-regular fa-sparkles me-2"></i>
          Попълни всичко
        </a>

        <div class="form-check form-switch ms-2 mt-1">
          <input class="form-check-input" id="js-toggle-hidden" type="checkbox"/>
          <label class="form-check-label" for="js-toggle-hidden">Покажи скритите полета</label>
        </div>
      </div>
    </div>

    <script type="module">
      $(function () {
        $('[data-suggest-hidden="yes"]').hide();

        $('#js-fill-all-btn').click(function () {
          $('[data-suggest]').click();
          $('input[name="gallery[]"]').attr('checked', true);
        });

        $('#js-toggle-hidden').change(function () {
          const isChecked = $(this).prop('checked');
          if (isChecked) {
            $('[data-suggest-hidden="yes"]').fadeIn();
          } else {
            $('[data-suggest-hidden="yes"]').fadeOut();
          }
        });

        @if (request()->isMethod('post'))
        $('#js-toggle-hidden').prop('checked', true).change();
        @endif
      });
    </script>
  @endif

  <script type="module">
    $(function () {
      function findFeature(data, featureId) {
        let rs = null;
        data?.FeaturesGroups?.forEach(featureGroup => {
          featureGroup.Features.forEach(feature => {
            if (parseInt(feature?.Feature?.ID) === featureId) {
              rs = feature?.RawValue;
            }
          })
        });
        return rs;
      }

      // Get product info
      // @todo loading
      $.ajax({
        url: @json(url('/erp/data-sources/product-info/' . $product->id)),
        success: function (rs) {
          let data = rs?.[0]?.data;

          if (!data) {
            $('#js-data-source-alert').html(`
              <div class="alert alert-subtle-info" role="alert">
                <i class="fa-regular fa-sparkles"></i>
                Не са намерени външни данни за автоматично попълване.
              </div>
            `);

            return;
          } else {
            $('#js-data-source-alert').html(``);
          }

          console.log('Data Source', data);

          let eanSuggest = data?.GeneralInfo?.GTIN.find(gtin => gtin === $('#f-ean').val()) || data?.GeneralInfo?.GTIN?.[0];

          const actions = {
            mpn: {
              $node: $('[data-suggest="mpn"]'),
              $target: $('#f-mpn'),
              actionType: 'fillIfEmpty',
              value: data?.GeneralInfo?.BrandPartCode,
              previewValue: data?.GeneralInfo?.BrandPartCode,
            },
            ean: {
              $node: $('[data-suggest="ean"]'),
              $target: $('#f-ean'),
              actionType: 'fillIfEmpty',
              value: eanSuggest,
              previewValue: eanSuggest,
            },
            nameBg: {
              $node: $('[data-suggest="nameBg"]'),
              $target: $('#f-nameBg'),
              actionType: 'fill',
              value: data?.GeneralInfo?.Title,
              previewValue: $(`<div>${data?.GeneralInfo?.Title}</div>`).text().substr(0, 70) + '...',
            },
            nameEn: {
              $node: $('[data-suggest="nameEn"]'),
              $target: $('#f-nameEn'),
              actionType: 'fill',
              value: data?.GeneralInfo?.Title,
              previewValue: $(`<div>${data?.GeneralInfo?.Title}</div>`).text().substr(0, 70) + '...',
            },
            manufacturerId: {
              $node: $('[data-suggest="manufacturerId"]'),
              $target: $('#f-manufacturerId'),
              actionType: 'select-match',
              value: data?.GeneralInfo?.Brand,
              previewValue: data?.GeneralInfo?.Brand,
            },
            warrantyPeriod: {
              $node: $('[data-suggest="warrantyPeriod"]'),
              $target: null,
              actionType: 'none',
              value: data?.GeneralInfo?.Description?.WarrantyInfo,
              previewValue: $(`<div>${data?.GeneralInfo?.Description?.WarrantyInfo}</div>`).text().substr(0, 45) + '...',
            },
            categories: {
              $node: $('[data-suggest="categories"]'),
              $target: null,
              actionType: 'none',
              value: data?.GeneralInfo?.Category?.Name?.Value,
              previewValue: data?.GeneralInfo?.Category?.Name?.Value,
            },
            descriptionBg: {
              $node: $('[data-suggest="descriptionBg"]'),
              $target: '_descriptionBg',
              actionType: 'editor',
              value: data?.GeneralInfo?.Description?.LongDesc,
              previewValue: $(`<div>${data?.GeneralInfo?.Description?.LongDesc}</div>`).text().substr(0, 70) + '...',
            },
            descriptionEn: {
              $node: $('[data-suggest="descriptionEn"]'),
              $target: '_descriptionEn',
              actionType: 'editor',
              value: data?.GeneralInfo?.Description?.LongDesc,
              previewValue: $(`<div>${data?.GeneralInfo?.Description?.LongDesc}</div>`).text().substr(0, 70) + '...',
            },
            weight: {
              $node: $('[data-suggest="weight"]'),
              $target: $('#f-weight'),
              actionType: 'fill',
              value: null,
              previewValue: null,
            },
            width: {
              $node: $('[data-suggest="width"]'),
              $target: $('#f-width'),
              actionType: 'fill',
              value: null,
              previewValue: null,
            },
            height: {
              $node: $('[data-suggest="height"]'),
              $target: $('#f-height'),
              actionType: 'fill',
              value: null,
              previewValue: null,
            },
            length: {
              $node: $('[data-suggest="length"]'),
              $target: $('#f-length'),
              actionType: 'fill',
              value: null,
              previewValue: null,
            },
          };

          if (findFeature(data, 762) > 0) {
            // Package weight: 762
            actions.weight.value = parseFloat(findFeature(data, 762) / 1000).toFixed(2);
            actions.weight.previewValue = `${actions.weight.value} кг. (с опаковка)`;
          } else if (findFeature(data, 94) > 0) {
            // Weight: 94
            actions.weight.value = parseFloat(findFeature(data, 94) / 1000).toFixed(2);
            actions.weight.previewValue = `${actions.weight.value} кг. (без опаковка)`;
          }

          if (findFeature(data, 3808) > 0) {
            // Package width: 3808
            actions.width.value = parseFloat(findFeature(data, 3808) / 10).toFixed(2);
            actions.width.previewValue = `${actions.width.value} см. (с опаковка)`;
          } else if (findFeature(data, 1649) > 0) {
            // Width: 1649
            actions.width.value = parseFloat(findFeature(data, 1649) / 10).toFixed(2);
            actions.width.previewValue = `${actions.width.value} см. (без опаковка)`;
          }

          if (findFeature(data, 3807) > 0) {
            // Package height: 3807
            actions.height.value = parseFloat(findFeature(data, 3807) / 10).toFixed(2);
            actions.height.previewValue = `${actions.height.value} см. (с опаковка)`;
          } else if (findFeature(data, 1464) > 0) {
            // Height: 1464
            actions.height.value = parseFloat(findFeature(data, 1464) / 10).toFixed(2);
            actions.height.previewValue = `${actions.height.value} см. (без опаковка)`;
          }

          if (findFeature(data, 3806) > 0) {
            // Package depth: 3806
            actions.length.value = parseFloat(findFeature(data, 3806) / 10).toFixed(2);
            actions.length.previewValue = `${actions.length.value} см. (с опаковка)`;
          } else if (findFeature(data, 1650) > 0) {
            // Depth: 1650
            actions.length.value = parseFloat(findFeature(data, 1650) / 10).toFixed(2);
            actions.length.previewValue = `${actions.length.value} см. (без опаковка)`;
          }

          for (const [key, action] of Object.entries(actions)) {
            if (!action.value) {
              action.value = 'N/A';
              action.previewValue = action.value;
              action.actionType = 'none';
              action.$target = null;
            }

            let cursor = action.actionType === 'none' ? 'default' : 'pointer';
            let bg = !action.$target ? 'secondary' : 'primary';

            if (action?.actionType === 'fillIfEmpty' && action?.$target?.val()) {
              cursor = 'default';
              bg = 'secondary';
            }

            action.$node.html(`
              <div class="mt-1">
                <span class="badge badge-phoenix badge-phoenix-${bg} fs-9" style="cursor: ${cursor};">
                  <i class="fa-regular fa-sparkles"></i>
                  ${action.previewValue}
                </span>
              </div>
            `);
            action.$node.fadeIn();

            action.$node.click(() => {
              if (action.actionType === 'select-match') {
                action.$target.find('option').each(function () {
                  const $option = $(this);
                  if ($option.text().trim() === action.value) {
                    action.$target.val($option.attr('value'));
                  }
                });
              } else if (action.actionType === 'editor') {
                if (window[action.$target]) {
                  let text = action.value;
                  text = text.replace(/<br>/, "\n");
                  text = text.replace(/<br\/>/, "\n");
                  text = $(`<div>${text}</div>`).text();
                  window[action.$target].setText(text);
                }
              } else if (action.actionType === 'fill') {
                action.$target.val(action.value);
              } else if (action.actionType === 'fillIfEmpty') {
                if (!action.$target.val()) {
                  action.$target.val(action.value);
                }
              } else {
                // do nothing
              }
            });
          }

          // Gallery
          if (data?.Gallery.length) {
            $('[data-suggest="gallery"]').fadeIn();

            data?.Gallery?.forEach(item => {
              $('[data-suggest="gallery-images"]').append(`
              <div class="col-6 col-md-3 col-lg-2">
                <label class="d-block position-relative" style="border:1px solid rgba(0,0,0,.125);border-radius:0.5rem;overflow:hidden;cursor:pointer;">
                  <input type="checkbox" class="form-check-input position-absolute" style="top:.5rem;left:.5rem;z-index:2;width:1.15rem;height:1.15rem;cursor:pointer;" name="gallery[]" value="${item.Pic}">
                  <div class="ratio ratio-1x1 d-flex align-items-center justify-content-center bg-white">
                    <img src="${item.Pic500x500}" style="width:100%;height:100%;display:block;margin:auto;padding:3rem;object-fit: contain;" />
                  </div>
                </label>
              </div>
            `);
            });
          }
        }
      });
    });
  </script>
@endif
