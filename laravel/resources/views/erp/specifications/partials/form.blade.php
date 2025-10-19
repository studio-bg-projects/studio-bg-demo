<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-nameBg">Име на спецификацията [BG]</label>
        <input type="text" class="form-control @if($errors->has('nameBg')) is-invalid @endif" id="f-nameBg" name="nameBg" value="{{ $specification->nameBg }}" placeholder="Името на спецификацията..." required/>
        @if($errors->has('nameBg'))
          <div class="invalid-feedback">
            {{ $errors->first('nameBg') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-nameEn">Име на спецификацията [EN]</label>
        <input type="text" class="form-control @if($errors->has('nameEn')) is-invalid @endif" id="f-nameEn" name="nameEn" value="{{ $specification->nameEn }}" placeholder="Името на спецификацията..." required/>
        @if($errors->has('nameEn'))
          <div class="invalid-feedback">
            {{ $errors->first('nameEn') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="form-label" for="f-isActive">Активен запис</label>
        <select class="form-select @if($errors->has('isActive')) is-invalid @endif" id="f-isActive" name="isActive">
          <option value="1" @if ($specification->isActive) selected @endif>Активен запис</option>
          <option value="0" @if (!$specification->isActive) selected @endif>Спрян (премахната от всички платформи)</option>
        </select>
      </div>
    </div>
  </div>
</div>

<hr class="my-3"/>

<div class="card">
  <div class="card-body pb-1">
    <h2 class="h4 card-title mb-4">Тип на данните</h2>

    <div class="mb-4">
      <label class="app-form-label" for="f-valueType">Вид</label>
      <select class="form-select @if($errors->has('valueType')) is-invalid @endif" id="f-valueType" name="valueType">
        @foreach (App\Enums\SpecificationValueType::cases() as $valueType)
          <option value="{{ $valueType->value }}" {{ $specification->valueType && $specification->valueType->value == $valueType->value ? 'selected' : '' }}>
            {{ \App\Services\MapService::specificationValueTypes($valueType)->label }}
          </option>
        @endforeach
      </select>
      @if($errors->has('valueType'))
        <div class="invalid-feedback">
          {{ $errors->first('valueType') }}
        </div>
      @endif
      <p class="text-body-tertiary fs-9 mt-2">
        <i class="fa-regular fa-circle-info"></i>
        <span id="js-value-type-info"></span>
      </p>
    </div>

    <div class="mb-4">
      <label class="app-form-label" for="f-options">Опции</label>
      <textarea type="text" class="form-control @if($errors->has('options')) is-invalid @endif" id="f-options" name="options" rows="4" placeholder="{{ "1=Червен\n2=Зелен\n3=..." }}">{{ $specification->options }}</textarea>
      @if($errors->has('options'))
        <div class="invalid-feedback">
          {{ $errors->first('options') }}
        </div>
      @endif
      <p class="text-body-tertiary fs-9 mt-2">
        <i class="fa-regular fa-circle-info"></i>
        Въведете всяка опция на нов ред, във формат
        <code>Key=Value[BG]|Value[EN]</code>
      </p>
    </div>
  </div>
</div>

<script type="module">
  $('#f-valueType').change(function () {
    const types = {};
    @foreach (App\Enums\SpecificationValueType::cases() as $valueType)
      types['{{ $valueType->value }}'] = @json(\App\Services\MapService::specificationValueTypes($valueType));
    @endforeach

    const currentVal = $(this).val();

    $('#js-value-type-info')
      .text(`${types[currentVal].label} - ${types[currentVal].description}`)
      .hide()
      .fadeIn();

    const disableOptions = currentVal !== 'option' && currentVal !== 'multiOptions';
    $('#f-options').prop('disabled', disableOptions);
  })
    .change();
</script>
