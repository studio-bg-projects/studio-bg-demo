@if (count($items))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($items->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $items->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="border-top border-translucent text-center" style="width: 10px;">
            <input class="form-check-input" type="checkbox" data-bulk-node="check-all"/>
          </th>
          <th class="border-top border-translucent text-center" style="width: 40px;">
            <span class="visually-hidden">Съвпадения от Icecat</span>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'itemMpn') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'itemMpn', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              MPN
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'itemEan') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'itemEan', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              EAN
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'itemName') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'itemName', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Име
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'parentId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'parentId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Доставчик
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'itemPrice') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'itemPrice', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Цена
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'itemQuantity') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'itemQuantity', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Количество
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'productId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'productId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Продукт
            </a>
          </th>
          <th class="nosort border-top border-translucent">
            -
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($items as $row)
          <tr data-item-id="{{ $row->id }}" data-skip-sync="{{ $row->skipSync ? 1 : 0 }}" data-product-id="{{ $row->product->id ?? '' }}" data-do-not-sync="{{ $row->doNotSync ? 1 : 0 }}" data-auto-product="{{ $row->autoProduct ? 1 : 0 }}">
            <td>
              <input class="form-check-input" type="checkbox" value="{{ $row->id }}" data-bulk-node="checkbox"/>
            </td>
            <td class="text-center">
              @php($dataSourceProduct = $row->dataSourceProduct ?? null)
              @if($dataSourceProduct)
                <span data-bs-toggle="tooltip" data-bs-html="true" data-datasource-tooltip="1" data-model-name="{{ $dataSourceProduct->modelName ?? '' }}" data-category-id="{{ $dataSourceProduct->categoryId ?? '' }}" data-picture-url="{{ $dataSourceProduct->pictureUrl ?? '' }}" data-item-id="{{ $row->id }}">
                  <span class="text-decoration-none text-primary fs-7 text-info me-1">
                    <i class="fa-regular fa-sparkles"></i>
                  </span>
                </span>
              @endif
            </td>
            <td>
              {{ $row->itemMpn }}
            </td>
            <td>
              {{ $row->itemEan }}
            </td>
            <td>
              {{ $row->itemName }}
            </td>
            <td>
              {{ $row->feedImport->providerName ?? '-' }}
            </td>
            <td class="white-space-nowrap">
              <i>{{ price($row->itemPrice) }} + {{ $row->feedImport->markupPercent }}% =</i>
              <b>{{ price($row->itemPrice + ($row->itemPrice * $row->feedImport->markupPercent / 100)) }}</b>
            </td>
            <td class="white-space-nowrap">
              {{ $row->itemQuantity }} бр.
            </td>
            <td style="width: 300px;">
              <div style="width: 300px;">
                <select class="form-select js-select-product" data-id="{{ $row->id }}">
                  <option value=""></option>
                  @if($row->product)
                    <option value="{{ $row->product->id }}" selected>{{ $row->product->mpn }} - {{ $row->product->nameBg }}</option>
                  @endif
                </select>
              </div>
            </td>
            <td style="min-width: 200px;">
              <button type="button" style="display: none; width: 50px;" class="btn btn-sm btn-phoenix-primary" data-action="auto-link" data-item-id="{{ $row->id }}" data-product-id="{{ $row->autoProduct->id ?? '' }}" data-product-text="{{ $row->autoProduct ? ($row->autoProduct->mpn.' - '.$row->autoProduct->nameBg) : '' }}" data-bs-toggle="tooltip" data-bs-title="Свържи с намерения продукт: {{ $row->autoProduct ? ($row->autoProduct->mpn.' - '.$row->autoProduct->nameBg) : '' }}">
                <i class="fa-regular fa-link"></i>
              </button>
              <button type="button" style="display: none; width: 50px;" class="btn btn-sm btn-phoenix-primary" data-action="create-product" data-item-id="{{ $row->id }}" data-bs-toggle="tooltip" data-bs-title="Създай нов продукт">
                <i class="fa-regular fa-circle-plus"></i>
              </button>
              <button type="button" style="display: none; width: 50px;" class="btn btn-sm btn-phoenix-primary" data-action="start-sync" data-item-id="{{ $row->id }}" data-bs-toggle="tooltip" data-bs-title="Пусни синхронизацията">
                <i class="fa-regular fa-play"></i>
              </button>
              <button type="button" style="display: none; width: 50px;" class="btn btn-sm btn-phoenix-primary" data-action="stop-sync" data-item-id="{{ $row->id }}" data-bs-toggle="tooltip" data-bs-title="Пауза на синхронизацията">
                <i class="fa-regular fa-pause"></i>
              </button>
              <button type="button" style="display: none; width: 50px;" class="btn btn-sm btn-phoenix-primary" data-action="unlink" data-item-id="{{ $row->id }}" data-bs-toggle="tooltip" data-bs-title="Изтрий синхронизацията с продукта">
                <i class="fa-regular fa-link-slash"></i>
              </button>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>

    @include('erp.feeds-imports-items.partials.related.partials.results-check')

    @if ($items->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $items->links('pagination::bootstrap-5') }}
      </div>
    @endif

    <div class="mt-4 text-end">
      <button type="button" style="display: none;" class="btn btn-primary" data-action="link-all" data-bs-toggle="tooltip" data-bs-title="Свържи всички намерени продукти">
        <i class="fa-regular fa-link me-2"></i>
        Свържи всички
      </button>
    </div>
  </div>
@else
  @include('shared.no-rs')
@endif

<script type="module">
  const escapeHtml = (value) => {
    return $('<div>').text(value ?? '').html();
  };

  const initDataSourceTooltips = () => {
    if (!window.bootstrap || !window.bootstrap.Tooltip) {
      return;
    }

    $('[data-datasource-tooltip]').each((_, el) => {
      const existing = window.bootstrap.Tooltip.getInstance(el);
      if (existing) {
        existing.dispose();
      }

      const $el = $(el);

      new window.bootstrap.Tooltip(el, {
        html: true,
        trigger: 'hover focus',
        title: () => {
          const modelName = $el.data('model-name') || '—';
          const categoryId = $el.data('category-id');
          const categoryText = categoryId !== undefined && categoryId !== null && `${categoryId}` !== '' ? `${categoryId}` : '—';
          const pictureUrl = $el.data('picture-url');
          const imageHtml = pictureUrl ? `<div class="mt-2 text-center"><img alt="" src="${escapeHtml(pictureUrl)}" style="max-width: 150px; max-height: 150px;;" class="img-fluid rounded"/></div>` : '';
          console.log('pictureUrl', imageHtml);

          return `<div class="text-start">`
            + `<div class="fw-semibold">${escapeHtml(modelName)}</div>`
            + `<div class="small">Категория: ${escapeHtml(categoryText)}</div>`
            + imageHtml
            + `</div>`;
        }
      });
    });
  };

  $(() => {
    initDataSourceTooltips();
  });
</script>

<script type="module">
  const hideTooltip = ($elements) => {
    if (!window.bootstrap || !window.bootstrap.Tooltip) {
      return;
    }

    $elements.each((_, el) => {
      const tooltip = window.bootstrap.Tooltip.getInstance(el);
      if (tooltip) {
        tooltip.hide();
      }
    });
  };

  const applyRowState = ($row) => {
    const skipSync = Number($row.data('skip-sync')) === 1;
    const productId = $row.data('product-id');
    const doNotSync = Number($row.data('do-not-sync')) === 1;
    const autoProduct = Number($row.data('auto-product')) === 1;

    hideTooltip($row.find('[data-bs-toggle="tooltip"]'));

    $row.removeClass('table-warning table-danger');

    if (skipSync) {
      $row.addClass('table-warning');
      $row.find('[data-action="start-sync"]').show();
      $row.find('[data-action="stop-sync"]').hide();
      $row.find('[data-action="unlink"]').hide();
      $row.find('.js-select-product').addClass('d-none');
      $row.find('[data-action="create-product"], [data-action="auto-link"]').hide();
    } else {
      $row.find('[data-action="start-sync"]').hide();
      $row.find('[data-action="stop-sync"]').show();
      if (productId) {
        $row.find('[data-action="unlink"]').show();
      } else {
        $row.find('[data-action="unlink"]').hide();
      }
      $row.find('.js-select-product').removeClass('d-none');

      if (!productId && autoProduct) {
        $row.find('[data-action="auto-link"]').show();
      } else {
        $row.find('[data-action="auto-link"]').hide();
      }

      if (!productId) {
        $row.find('[data-action="create-product"]').show();
      } else {
        $row.find('[data-action="create-product"]').hide();
      }

      if (!productId && !doNotSync) {
        $row.addClass('table-danger');
      }
    }

    // Update link all visibility
    const hasLinks = $('[data-action="auto-link"]:visible').length > 0;
    $('[data-action="link-all"]').toggle(hasLinks);
  };

  const setLinked = (itemId, productId, productText) => {
    if (productId && productText) {
      const $select = $('.js-select-product[data-id="' + itemId + '"]');
      if ($select.length) {
        const option = new Option(productText, productId, true, true);
        $select.append(option);
        $select.data('skip-change', true);
        $select.trigger('change');
      }
    }

    const $row = $(`tr[data-item-id="${itemId}"]`);
    if (productId && productText) {
      $row.data('auto-product', 1);
      $row
        .find('[data-action="auto-link"]')
        .data('product-id', productId)
        .data('product-text', productText)
        .attr('data-bs-title', 'Свържи с намерения продукт: ' + productText);
    }
    const $select = $row.find('.js-select-product');
    const currentId = $select.val();
    $row.data('product-id', currentId || '');

    applyRowState($row);
  }

  $('.js-select-product').each(function () {
    const $select = $(this);
    const itemId = $select.data('id');
    $select.select2({
      allowClear: true,
      placeholder: 'Изберете продукт...',
      minimumInputLength: 1,
      ajax: {
        url: "{{ url('/erp/products/') }}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            filter: {
              q: params.term
            },
            page: params.page
          };
        },
        processResults: (rs) => {
          return {results: rs.products.data};
        },
        cache: true
      },
      templateSelection: (item) => (item.text || [item.mpn, item.nameBg].filter(Boolean).join(' | ')),
      templateResult: (item) => {
        if (item.loading) {
          return item.text;
        }
        const preview = item?.uploads?.[0]?.urls?.tiny;
        return $(
          `<div class="d-flex">
            <div style="width: 50px; height: 50px;">
              ${preview ? `<img src="${preview}" style="height: 50px; width: 50px; object-fit: cover;" alt=""/>` : ''}
            </div>
            <div class="d-flex align-items-center ps-2">
              ${[item.mpn, item.ean, item.nameBg].filter(Boolean).join(' | ')}
            </div>
          </div>`
        );
      }
    }).on('change', function () {
      if ($select.data('skip-change')) {
        $select.data('skip-change', false);
        setLinked(itemId);
        return;
      }

      $.ajax({
        url: '{{ url('/erp/feeds-imports-items/set-related-product') }}/' + itemId,
        type: 'POST',
        data: {productId: $select.val()},
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: () => {
          setLinked(itemId);
        }
      });
    });
  });

  $(document).on('click', '[data-action="auto-link"]', function () {
    const $btn = $(this);
    hideTooltip($btn);
    const itemId = $btn.data('item-id');
    const productId = $btn.data('product-id');
    const productText = $btn.data('product-text');

    $.ajax({
      url: '{{ url('/erp/feeds-imports-items/set-related-product') }}/' + itemId,
      type: 'POST',
      data: {productId},
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: () => {
        setLinked(itemId, productId, productText);
      }
    });
  });

  $(document).on('click', '[data-action="unlink"]', function () {
    const $btn = $(this);
    hideTooltip($btn);
    const itemId = $btn.data('item-id');

    if (!window.confirm('Сигурни ли сте, че искате да премахнете връзката с продукта?')) {
      return;
    }

    $.ajax({
      url: '{{ url('/erp/feeds-imports-items/unset-related-product') }}/' + itemId,
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: () => {
        const $row = $(`tr[data-item-id="${itemId}"]`);
        const $select = $row.find('.js-select-product');

        $select.data('skip-change', true);
        $select.val(null).trigger('change');

        window.appToast({
          body: 'Успешно премахнахте връзката с продукта',
          type: 'success',
          icon: 'fa-check'
        });
      }
    });
  });

  $(document).on('click', '[data-action="link-all"]', async function () {
    const $linkAllBtn = $(this);
    hideTooltip($linkAllBtn);
    const buttons = $('[data-action="auto-link"]:visible').toArray();
    for (const btn of buttons) {
      const $btn = $(btn);
      hideTooltip($btn);
      const itemId = $btn.data('item-id');
      const productId = $btn.data('product-id');
      const productText = $btn.data('product-text');

      const $row = $btn.closest('tr');
      $row.get(0).scrollIntoView({behavior: 'smooth', block: 'center'});

      $btn.prop('disabled', true);
      $btn.prepend('<span class="spinner-border spinner-border-sm me-2" role="status"></span>');

      try {
        await $.ajax({
          url: '{{ url('/erp/feeds-imports-items/set-related-product') }}/' + itemId,
          type: 'POST',
          data: {productId},
          headers: {
            'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
          }
        });

        setLinked(itemId, productId, productText);
      } finally {
        await new Promise(resolve => setTimeout(resolve, 400));
      }
    }
  });

  $(document).on('click', '[data-action="create-product"]', function () {
    const $btn = $(this);
    hideTooltip($btn);
    const itemId = $btn.data('item-id');

    $.ajax({
      url: '{{ url('/erp/feeds-imports-items/related/add-product') }}/' + itemId,
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: (rs) => {
        window.appToast({
          body: `Успешно създадохте продукта <a href="/erp/products/update/${rs.product.id}" target="_blank">${rs.product.nameBg}</a>`,
          type: 'success',
          icon: 'fa-check'
        });

        setLinked(itemId, rs.product.id, rs.product.mpn + ' - ' + rs.product.nameBg);
      }
    });
  });

  $(document).on('click', '[data-action="start-sync"], [data-action="stop-sync"]', function () {
    const $btn = $(this);
    hideTooltip($btn);
    const itemId = $btn.data('item-id');
    const skipSync = $btn.data('action') === 'start-sync' ? 0 : 1;

    $.ajax({
      url: '{{ url('/erp/feeds-imports-items/set-skip-sync') }}/' + itemId,
      type: 'POST',
      data: {skipSync},
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: () => {
        const $row = $(`tr[data-item-id="${itemId}"]`);
        $row.data('skip-sync', skipSync);

        if (skipSync) {
          window.appToast({
            body: 'Успешно спряхте синхронизацията за този запис',
            type: 'success',
            icon: 'fa-check'
          });
        } else {
          window.appToast({
            body: 'Синхронизацията е активна за този запис',
            type: 'success',
            icon: 'fa-check'
          });
        }

        applyRowState($row);
      }
    });
  });

  $(() => {
    $('tr[data-item-id]').each(function () {
      applyRowState($(this));
    });
  });
</script>
