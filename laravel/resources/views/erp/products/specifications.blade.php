@extends('layouts.app')

@section('content')
  @include('erp.products.partials.navbar')

  <h1 class="h4 mb-5">{{ $product->getOriginal('nameBg') }} - Спецификации</h1>

  @if ($product->categories->isEmpty())
    @include('shared.no-rs', [
      'noRsTitle' => 'Няма налични спецификации',
      'noRsSubTitle' => 'За да зададете спецификации към продукта, първо трябва да свържете категории с него.',
    ])
  @else
    <p class="text-body-tertiary">Попълнете спецификациите към всяка категория.</p>

    <hr class="my-3"/>

    <form method="post" action="?" class="mb-5" data-disable-on-submit>
      @csrf

      @foreach($product->categories as $category)
        @if ( $category->specifications->isNotEmpty())
          <div class="row">
            <div class="col-12 col-xl-4 pb-3 pb-lg-0">
              <h2 class="h4 card-title mt-4 mb-2">{{ $category->nameBg }}</h2>
              @if ($category->parent)
                <p class="text-body-secondary mb-0 fs-8">
                  {{ $category->parent->nameBg }}
                </p>
              @endif
            </div>
            <div class="col-12 col-xl-8">
              <div class="card">
                <div class="card-body pb-1">
                  <h2 class="h4 card-title mb-4">{{ $category->nameBg }} &mdash; Спецификации</h2>

                  @foreach($category->specifications as $specification)
                    @php($spId = 'sp-' . $specification->id)

                    <div class="mb-4 {{ !$specification->isActive ? 'opacity-50' : '' }}">
                      <label class="app-form-label" for="{{ $spId }}">
                        {{ $specification->nameBg }}

                        @if (!$specification->isActive)
                          <span class="badge badge-phoenix badge-phoenix-secondary">Неактивен</span>
                        @endif
                      </label>
                      <div class="row">
                        <div class="col-6">
                          <label class="app-form-label" for="{{ $spId }}-bg">
                            [BG]
                          </label>
                        </div>
                        <div class="col-6">
                          <label class="app-form-label" for="{{ $spId }}-en">
                            [EN]
                          </label>
                        </div>
                      </div>

                      <div class="row" data-group="{{ $spId }}" data-type="{{ $specification->valueType }}">
                        @foreach(['bg', 'en'] as $langKey => $lang)
                          @php($value = $valuesMap[$category->id][$specification->id][$lang] ?? '')
                          @php($errKey = $category->id . '.' . $specification->id . '.' . $lang)

                          <div class="col-6" data-lang="{{ $lang }}">
                            @if ($specification->valueType === \App\Enums\SpecificationValueType::String)
                              <input type="text" maxlength="255" class="form-control @if($errors->has($errKey)) is-invalid @endif" id="{{ $spId }}-{{ $lang }}" name="s[{{ $category->id }}][{{ $specification->id }}][{{ $lang }}]" value="{{ $value }}" maxlength="255"/>
                            @elseif ($specification->valueType === \App\Enums\SpecificationValueType::Boolean)
                              <div class="form-check form-switch">
                                <input type="hidden" name="s[{{ $category->id }}][{{ $specification->id }}][{{ $lang }}]" value="0"/>

                                <input class="form-check-input @if($errors->has($errKey)) is-invalid @endif" id="{{ $spId }}-{{ $lang }}" name="s[{{ $category->id }}][{{ $specification->id }}][{{ $lang }}]" value="1" type="checkbox" @if ($value) checked @endif />
                                <label class="form-check-label" for="{{ $spId }}-{{ $lang }}">Да/Не</label>
                                @if($errors->has($errKey))
                                  <div class="invalid-feedback">
                                    {{ $errors->first($errKey) }}
                                  </div>
                                @endif
                              </div>
                            @elseif ($specification->valueType === \App\Enums\SpecificationValueType::Decimal)
                              <input type="number" step="0.01" class="form-control @if($errors->has($errKey)) is-invalid @endif" id="{{ $spId }}-{{ $lang }}" name="s[{{ $category->id }}][{{ $specification->id }}][{{ $lang }}]" value="{{ $value }}"/>
                            @elseif ($specification->valueType === \App\Enums\SpecificationValueType::Number)
                              <input type="number" step="1" class="form-control @if($errors->has($errKey)) is-invalid @endif" id="{{ $spId }}-{{ $lang }}" name="s[{{ $category->id }}][{{ $specification->id }}][{{ $lang }}]" value="{{ $value }}"/>
                            @elseif ($specification->valueType === \App\Enums\SpecificationValueType::Option)
                              <select class="form-select @if($errors->has($errKey)) is-invalid @endif" id="{{ $spId }}-{{ $lang }}" name="s[{{ $category->id }}][{{ $specification->id }}][{{ $lang }}]">
                                <option value="">- Избери -</option>
                                @foreach(explode("\n", $specification->options) as $option)
                                  @php(list($key, $fullTitle) = explode('=', $option, 2))
                                  @php($titles = explode('|', $fullTitle, 2))
                                  @php($title = $titles[$langKey] ?? $fullTitle)
                                  <option value="{{ $title }}" @if($value === $title) selected @endif>{{ $title }}</option>
                                @endforeach
                              </select>
                            @else
                              <div class="alert alert-phoenix-danger py-3">
                                Непознат тип данни:
                                <strong>{{ $specification->valueType->value }}</strong>
                              </div>
                            @endif

                            @if($errors->has($errKey))
                              <div class="invalid-feedback">
                                {{ $errors->first($errKey) }}
                              </div>
                            @endif
                          </div>
                        @endforeach
                      </div>
                    </div>

                    <hr/>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          <hr class="my-3"/>
        @endif
      @endforeach

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
  @endif

  <script type="module">
    $('[data-group]').each(function () {
      const $group = $(this);
      const groupId = $group.data('group');
      const type = $group.data('type');

      const $en = $(`#${groupId}-en`);
      const $bg = $(`#${groupId}-bg`);

      if (type !== 'string') {
        $en.attr('readonly', true);
        $group.find('[data-lang="en"]').css('opacity', 0.5);

        // Change
        $bg.on('change', function () {
          if (type === 'number') {
            $bg.val(parseInt($bg.val() || 0));
          }

          if (type === 'decimal') {
            $bg.val(parseFloat($bg.val() || 0));
          }
        });

        $bg.on('change keyup', function () {
          if ($bg.is(':checkbox')) {
            $en.prop('checked', $bg.prop('checked'));
          } else if ($bg.is('select')) {
            const selectedIndex = $bg.prop('selectedIndex');
            $en.find('option').eq(selectedIndex).prop('selected', true);
          } else {
            $en.val($bg.val());
          }
        });

        $bg.change();

        // Prevent change
        $en.on('change', function (event) {
          console.log('change');
          $bg.change();
        });
      }
    });
  </script>
@endsection
