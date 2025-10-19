@if (count($products))
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start gap-2 mb-3">
    <p class="text-body-secondary fs-9 mb-0">Показани са продуктите с налични артикули към {{ $inventoryDate->format('d.m.Y') }} г.</p>
    <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-sm btn-phoenix-secondary">
      <i class="fa-regular fa-file-excel"></i>
      Експорт в Excel
    </a>
  </div>

  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if (method_exists($products, 'lastPage') && $products->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $products->links('pagination::bootstrap-5') }}
      </div>
    @endif

    @foreach ($products as $product)
      <div class="d-flex align-items-center bg-body-highlight-hover py-2">
        <button class="btn btn-link p-0 me-2 inventory-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#inventory-items-{{ $product->id }}" aria-expanded="false" data-product-id="{{ $product->id }}" data-date="{{ $selectedDate }}">
          <i class="fa-solid fa-chevron-right toggle-icon text-body me-2 js-icon"></i>
        </button>

        <div class="d-block border border-translucent rounded-2 table-preview">
          <a href="{{ url('/erp/products/update/' . $product->id) }}">
            @if ($product->uploads->isNotEmpty())
              <img src="{{ $product->uploads->first()->urls->tiny }}" alt=""/>
            @else
              <img src="{{ asset('img/icons/file-placeholder.svg') }}" alt=""/>
            @endif
          </a>
        </div>
        <div class="ms-3">
          <div>
            {{ $product->inventorySummary['name'] ?? $product->nameBg }}
          </div>
          <div class="text-body-secondary fs-9">
            {{ $product->inventorySummary['details'] ?? '' }}
          </div>
        </div>
        <span class="badge badge-phoenix badge-phoenix-primary ms-auto">
          {{ $product->inventorySummary['countLabel'] ?? ('Налично: ' . $product->inventoryCount . ' бр.') }}
        </span>
      </div>
      <div id="inventory-items-{{ $product->id }}" class="collapse inventory-collapse mt-2" data-loaded="0">
        <div class="inventory-items-content border border-dashed border-translucent rounded-2 p-3 text-body-secondary fs-9">
          Натиснете бутона, за да заредите наличните артикули.
        </div>
      </div>
    @endforeach

    @if (method_exists($products, 'lastPage') && $products->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $products->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif

<script type="module">
  $(function () {
    $('.inventory-toggle').each(function () {
      $(this).on('click', function () {
        const $icon = $(this).find('.js-icon');
        $icon.toggleClass('fa-chevron-right');
        $icon.toggleClass('fa-chevron-down');
      });
    });

    $('.inventory-collapse').each(function () {
      $(this).on('show.bs.collapse', function () {
        const $wrapper = $(this);

        if ($wrapper.attr('data-loaded') === '1') {
          return;
        }

        const $trigger = $(`[data-bs-target='#${$wrapper.attr('id')}']`);
        const productId = $trigger.data('productId');
        const date = $trigger.data('date');
        const $container = $wrapper.find('.inventory-items-content');

        if (!productId || !date || !$container.length) {
          $container.html('<span class="text-danger">Липсват данни за зареждане.</span>');
          return;
        }

        $container.html('<div class="d-flex align-items-center text-body-secondary"><span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Зареждане...</div>');

        $.ajax({
          url: `/erp/storage-report/inventory/items`,
          method: 'GET',
          data: {
            productId,
            date,
            returnHtml: true
          }
        })
          .done(html => {
            $container.html(html);
            $wrapper.attr('data-loaded', '1');
          })
          .fail(() => {
            $container.html('<span class="text-danger">Възникна грешка при зареждането.</span>');
          });
      });
    });
  });
</script>

@include('erp.storage-report.partials.storage-item-quick-view')
