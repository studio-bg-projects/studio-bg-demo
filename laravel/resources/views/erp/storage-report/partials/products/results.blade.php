@if (count($products))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if (!$noPagination && $products->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $products->links('pagination::bootstrap-5') }}
      </div>
    @endif

    <div class="d-flex justify-content-end gap-2 mb-3" data-btn-collapse-all="data-btn-collapse-all">
      <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-sm btn-phoenix-secondary">
        <i class="fa-regular fa-file-excel"></i>
        Експорт в Excel
      </a>
      <button type="button" class="btn btn-sm btn-phoenix-secondary" id="toggle-all">Покажи всички</button>
    </div>

    @foreach ($products as $product)
      <div class="d-flex align-items-center bg-body-highlight-hover py-2">
        <button class="btn btn-link p-0 me-2 product-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#storage-items-{{ $product->id }}" aria-expanded="false">
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
            {{ $product->nameBg }}
          </div>
          <div class="text-body-secondary fs-9">
            MPN: {{ $product->mpn }} | EAN: {{ $product->ean }}
          </div>
        </div>
        <span class="badge badge-phoenix badge-phoenix-primary ms-auto">
          {{ $product->storageItems->count() }} бр.
        </span>
      </div>
      <div id="storage-items-{{ $product->id }}" class="collapse storage-items mt-2">
        @if ($product->storageItems->isNotEmpty())
          <div class="table-responsive">
            <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle text-nowrap table-hover">
              <thead>
              <tr class="bg-body-highlight">
                <th class="nosort border-top border-translucent"></th>
                <th class="nosort border-top border-translucent">ID</th>
                <th class="nosort border-top border-translucent">SN</th>
                <th class="nosort border-top border-translucent">Заприхождаване</th>
                <th class="nosort border-top border-translucent">Изписване</th>
                <th class="nosort border-top border-translucent">Цена на закупуване</th>
                <th class="nosort border-top border-translucent" colspan="2">Цена на продажба</th>
                <th class="nosort border-top border-translucent">Бележка</th>
              </tr>
              </thead>
              <tbody>
              @foreach ($product->storageItems as $item)
                <tr>
                  <td style="width: 30px;">
                    @include('erp.storage-report.partials.view-item-button', ['itemId' => $item->id])
                  </td>
                  <td style="width: 30px;">
                    #{{ $item->id }}
                  </td>
                  <td style="width: 200px;">
                    {{ $item->serialNumber }}
                  </td>
                  <td style="width: 350px;">
                    {{ $item->formattedEntryInfo }}
                  </td>
                  <td style="width: 350px;">
                    {{ $item->formattedExitInfo }}
                  </td>
                  <td style="width: 150px;">
                    {{ $item->formattedPurchasePrice }}
                  </td>
                  @if ($item->hasDifferentSellPrices)
                    <td style="width: 150px;">
                      {{ $item->formattedOriginalSellPrice }}
                    </td>
                    <td style="width: 150px;">
                      {{ $item->formattedSellPrice }}
                    </td>
                  @else
                    <td style="width: 300px;" colspan="2">
                      {{ $item->formattedSellPrice }}
                    </td>
                  @endif
                  <td>
                    {{ $item->note }}
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-body-secondary fs-9 mb-0">Няма артикули</p>
        @endif
      </div>
    @endforeach

    @if (!$noPagination && $products->lastPage() > 1)
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
    $('.product-toggle').each(function () {
      $(this).on('click', function () {
        const $icon = $(this).find('.js-icon');
        $icon.toggleClass('fa-chevron-right');
        $icon.toggleClass('fa-chevron-down');
      });
    });

    const $toggleAllBtn = $('#toggle-all');

    if ($toggleAllBtn.length) {
      let showAll = true;

      $toggleAllBtn.on('click', function () {
        $('.storage-items').each(function () {
          const collapse = bootstrap.Collapse.getOrCreateInstance(this, {toggle: false});
          collapse[showAll ? 'show' : 'hide']();

          const $icon = $(`[data-bs-target='#${this.id}'] span`);

          if ($icon.length) {
            $icon.toggleClass('fa-chevron-right', !showAll);
            $icon.toggleClass('fa-chevron-down', showAll);
          }
        });

        $toggleAllBtn.text(showAll ? 'Скрий всички' : 'Покажи всички');
        showAll = !showAll;
      });
    }
  });
</script>

@include('erp.storage-report.partials.storage-item-quick-view')
