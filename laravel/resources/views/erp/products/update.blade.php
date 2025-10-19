@extends('layouts.app')

@section('content')
  @if (!Request()->emptyLayout)
    @include('erp.products.partials.navbar')
  @endif

  <h1 class="h4 mb-5">{{ $product->getOriginal('nameBg') }} - Редакция</h1>

  <div class="text-end mt-n8 mb-4 fw-bold">
    @php($source = App\Services\MapService::productSource($product->source))
    <div class="text-body-secondary fs-9">
      Източник:
      <i class="fa-solid {{ $source->icon }} ms-1"></i>
      {{ $source->title }}
    </div>
  </div>

  <form method="post" action="?{{ request()->getQueryString() }}" class="mb-5" data-disable-on-submit id="js-product-update-form">
    @csrf

    @include('erp.products.partials.form')

    <div class="text-end">
      <button class="btn btn-primary btn-lg mt-3" type="submit">
        <i class="fa-regular fa-pen-to-square me-2"></i>
        Редакция
      </button>
    </div>

    @if (request()->dataSourceMode)
      <div style="height: 3rem;"></div>

      <script type="module">
        $(function () {
          console.info('dataSourceMode - Remove all required attributes, because when the items are not visible and have no values, the form cannot be submitted.');

          $('#js-product-update-form [required]').removeAttr('required');
        });
      </script>
    @endif
  </form>
@endsection
