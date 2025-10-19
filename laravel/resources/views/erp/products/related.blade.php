@extends('layouts.app')

@section('content')
  @include('erp.products.partials.navbar')

  <h1 class="h4 mb-5">{{ $product->getOriginal('nameBg') }} - Свързани продукти</h1>

  <hr class="my-3"/>

  <form method="post" action="?" class="mb-5" data-disable-on-submit>
    @csrf
    <div class="row">
      <div class="col-12 col-xl-4 pb-3 pb-lg-0">
        <h2 class="h4 card-title mt-4 mb-2">Свързани продукти</h2>
        <p class="text-body-secondary mb-0 fs-8">Изберете продуктите, които са свързани с текущия. При синхронизация, освен избраните тук продукти, като свързани ще бъдат добавени и всички продукти, в които текущият продукт е посочен като свързан.</p>
      </div>
      <div class="col-12 col-xl-8">
        <div class="card">
          <div class="card-body pb-1">
            <h2 class="h4 card-title mb-4 d-none d-lg-block">Свързани продукти</h2>

            <div class="mb-4">
              <label class="app-form-label" for="f-related">Списък</label>
              <select class="form-select @if($errors->has('related')) is-invalid @endif" id="f-related" name="related[]" multiple size="1">
                @foreach($selectedProducts as $row)
                  <option value="{{ $row->id }}" selected>{{ $row->mpn }} - {{ $row->nameBg }}</option>
                @endforeach
              </select>
              @if($errors->has('related'))
                <div class="text-danger fs-9">
                  {{ $errors->first('related') }}
                </div>
              @endif

              <script type="module">
                $('#f-related').select2({
                  placeholder: 'Избери свързани продукти...',
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
                      return {
                        results: rs.products.data
                      };
                    },
                    cache: true
                  },
                  templateSelection: (item) => (item.text || [item.mpn, item.nameBg].filter(Boolean).join(' | ')),
                  templateResult: (item) => {
                    if (item.loading) {
                      return item.text;
                    }

                    const preview = item?.uploads?.[0]?.urls?.tiny;

                    return $(`
                      <div class="d-flex">
                        <div style="width: 50px; height: 50px;">
                          ${preview ? `<img src="${preview}" style="height: 50px; width: 50px; object-fit: cover;" alt=""/>` : ''}
                        </div>
                        <div class="d-flex align-items-center ps-2">
                          ${[item.mpn, item.ean, item.nameBg].filter(Boolean).join(' | ')}
                        </div>
                      </div>
                    `);
                  }
                });
              </script>
            </div>
          </div>
        </div>
      </div>
    </div>

    <hr class="my-3"/>

    <div class="row justify-content-end">
      <div class="col-12 col-xl-8">
        <div class="text-end">
          <button class="btn btn-primary btn-lg" type="submit">
            <i class="fa-regular fa-pen-to-square me-2"></i>
            Попълни
          </button>
        </div>
      </div>
    </div>
  </form>
@endsection
