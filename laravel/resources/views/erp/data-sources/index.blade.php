@extends('layouts.app')

@section('content')
  @include('erp.data-sources.partials.navbar')

  <div class="row g-0">
    <div class="col-12 col-md-3">
      <div class="btn-group mb-3">
        <button class="btn btn-phoenix-secondary">
          <span class="fas fa-align-left fs-8"></span>
        </button>

        <button class="btn btn-phoenix-secondary">
          <span class="fas fa-align-left fs-8"></span>
        </button>

        <button class="btn btn-phoenix-secondary">
          <span class="fas fa-align-left fs-8"></span>
        </button>
      </div>

      <div class="scrollbar top-stock-tab w-100 pe-xl-3" style="height: calc(100vh - 18rem);" id="js-scroll-wrapper">
        @if ($products)
          <ul class="nav gap-3 gap-xl-2 flex-nowrap flex-xl-column" role="tablist">
            @foreach ($products as $row)
              <li class="nav-item" role="presentation">
                <a class="nav-link card company-card p-3" data-product-link="{{ $row->id }}" href="{{ url("/erp/products/update/{$row->id}?emptyLayout=true&dataSourceMode=true") }}" target="product-preview-frame" data-bs-toggle="tab">
                  <div class="card-body p-0">
                    <div class="d-flex gap-3 gap-xl-2 gap-xxl-3 align-items-center">
                      @if ($row->uploads->isNotEmpty())
                        <img src="{{ $row->uploads->first()->urls->tiny }}" style="width: 50px; height: 50px;" alt=""/>
                      @else
                        <img src="{{ asset('img/icons/file-placeholder.svg') }}" style="width: 50px; height: 50px;" alt=""/>
                      @endif

                      <div class="d-flex gap-3 flex-between-center flex-1">
                        <div style="max-width: 15rem;">
                          <h6 class="fw-semibold text-body-secondary mb-1 lh-sm text-nowrap text-truncate w-100">
                            {{ $row->nameBg ?: 'Без име' }}
                          </h6>

                          @if ($row->mpn)
                            <div class="fs-10 text-body-emphasis">MPN:
                              <strong>{{ $row->mpn }}</strong>
                            </div>
                          @endif

                          @if ($row->ean)
                            <div class="fs-10 text-body-emphasis">EAN:
                              <strong>{{ $row->ean }}</strong>
                            </div>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
              </li>
            @endforeach
          </ul>
        @else
          @include('shared.no-rs')
        @endif
      </div>
    </div>
    <div class="col-12 col-md-9">
      <iframe id="product-preview-frame" name="product-preview-frame" src="about:blank" class="w-100" style="min-height: 100%;"></iframe>

      <script type="module">
        $('[data-product-link]').click(function () {
          const $link = $(this);
          $('#product-preview-frame').attr('src', $link.attr('href'));
        });
      </script>
    </div>
  </div>

  <script type="module">
    $(function () {
      $('[data-product-link]').click(function () {
        const productId = $(this).data('product-link');
        localStorage.setItem('data-sources-id', productId);
        document.location = `#${productId}`;
      });

      let productHashId = parseInt(window.location.hash.replace(/#/, ''));
      let latestDataSourceId = productHashId ? productHashId : localStorage.getItem('data-sources-id');

      if (latestDataSourceId) {
        const $link = $(`[data-product-link="${latestDataSourceId}"]`);

        $link.click();
        $link.addClass('active');

        $('#js-scroll-wrapper').animate({
          scrollTop: $link.offset().top - ($('#js-scroll-wrapper').height() / 2 + 200)
        }, 350);
      }
    });
  </script>
@endsection
