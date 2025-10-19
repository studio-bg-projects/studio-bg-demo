<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12 col-xl-3">
        <label class="app-form-label required" for="f-name">Име на производителя</label>
        <input type="text" class="form-control @if($errors->has('name')) is-invalid @endif" id="f-name" name="name" value="{{ $manufacturer->name }}" placeholder="Samsung..." required/>
        @if($errors->has('name'))
          <div class="invalid-feedback">
            {{ $errors->first('name') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-3">
        <label class="app-form-label" for="f-sortOrder">Подредба</label>
        <input type="number" step="1" min="0" class="form-control @if($errors->has('sortOrder')) is-invalid @endif" id="f-sortOrder" name="sortOrder" value="{{ $manufacturer->sortOrder }}" placeholder="1"/>
        @if($errors->has('sortOrder'))
          <div class="invalid-feedback">
            {{ $errors->first('sortOrder') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-3">
        <label class="form-label" for="f-isHomeSlider">Слайдър</label>
        <select class="form-select @if($errors->has('isHomeSlider')) is-invalid @endif" id="f-isHomeSlider" name="isHomeSlider">
          <option value="0" @if (!$manufacturer->isHomeSlider) selected @endif>Стандартна категория</option>
          <option value="1" @if ($manufacturer->isHomeSlider) selected @endif>Да има слайдър в магазина</option>
        </select>
      </div>

      <div class="col-12 col-xl-3">
        <label class="form-label" for="f-isActive">Активен производител</label>
        <select class="form-select @if($errors->has('isActive')) is-invalid @endif" id="f-isActive" name="isActive">
          <option value="1" @if ($manufacturer->isActive) selected @endif>Активен</option>
          <option value="0" @if (!$manufacturer->isActive) selected @endif>Спрян (премахнат от всички платформи)</option>
        </select>
      </div>
    </div>
  </div>
</div>

<hr class="my-3"/>

<div class="card">
  <div class="card-body pb-1">
    <h2 class="h4 card-title mb-4">Добавяне на лого</h2>
    @include('erp.uploads.uploader', [
      'groupType' => \App\Enums\UploadGroupType::Manufacturers->value,
      'groupId' => $manufacturer->fileGroupId,
      'fieldName' => 'fileGroupId',
      'maxFiles' => 1,
      'acceptedFiles' => 'image/*',
    ])
  </div>
</div>
