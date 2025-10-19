<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12 col-xl-6">
        <label class="app-form-label required" for="f-nameBg">Име на категорията [BG]</label>
        <input type="text" class="form-control @if($errors->has('nameBg')) is-invalid @endif" id="f-nameBg" name="nameBg" value="{{ $category->nameBg }}" placeholder="Име на категория..." required/>
        @if($errors->has('nameBg'))
          <div class="invalid-feedback">
            {{ $errors->first('nameBg') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label required" for="f-nameEn">Име на категорията [EN]</label>
        <input type="text" class="form-control @if($errors->has('nameEn')) is-invalid @endif" id="f-nameEn" name="nameEn" value="{{ $category->nameEn }}" placeholder="Category name..." required/>
        @if($errors->has('nameEn'))
          <div class="invalid-feedback">
            {{ $errors->first('nameEn') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-parentId">Родител</label>
        <select class="form-select @if($errors->has('parentId')) is-invalid @endif" id="f-parentId" name="parentId">
          <option value="">Без категория</option>
          @foreach ($categories as $row)
            <option value="{{ $row->id }}" {{ $category->parentId == $row->id ? 'selected' : '' }}>
              {{ $row->nameBg }}
            </option>
          @endforeach
        </select>
        @if($errors->has('parentId'))
          <div class="invalid-feedback">
            {{ $errors->first('parentId') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-sortOrder">Подредба</label>
        <input type="number" step="1" min="0" class="form-control @if($errors->has('sortOrder')) is-invalid @endif" id="f-sortOrder" name="sortOrder" value="{{ $category->sortOrder }}" placeholder="1"/>
        @if($errors->has('sortOrder'))
          <div class="invalid-feedback">
            {{ $errors->first('sortOrder') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="form-label" for="f-isActive">Активна категория</label>
        <select class="form-select @if($errors->has('isActive')) is-invalid @endif" id="f-isActive" name="isActive">
          <option value="1" @if ($category->isActive) selected @endif>Активна</option>
          <option value="0" @if (!$category->isActive) selected @endif>Спряна (премахната от всички платформи)
          </option>
        </select>
      </div>

      <div class="col-12 col-xl-4">
        <label class="form-label" for="f-isHidden">Скрита категория</label>
        <select class="form-select @if($errors->has('isHidden')) is-invalid @endif" id="f-isHidden" name="isHidden">
          <option value="0" @if (!$category->isHidden) selected @endif>Да се вижда в магазина</option>
          <option value="1" @if ($category->isHidden) selected @endif>Нека бъде скрита</option>
        </select>
      </div>

      <div class="col-12 col-xl-4">
        <label class="form-label" for="f-isHomeSlider">Слайдър</label>
        <select class="form-select @if($errors->has('isHomeSlider')) is-invalid @endif" id="f-isHomeSlider" name="isHomeSlider">
          <option value="0" @if (!$category->isHomeSlider) selected @endif>Стандартна категория</option>
          <option value="1" @if ($category->isHomeSlider) selected @endif>Да има слайдър в магазина</option>
        </select>
      </div>
    </div>
  </div>
</div>

<hr class="my-3"/>

<div class="card">
  <div class="card-body pb-1">
    <h2 class="h4 card-title mb-4">Добавяне на изображения</h2>
    @include('erp.uploads.uploader', [
      'groupType' => \App\Enums\UploadGroupType::Categories->value,
      'groupId' => $category->fileGroupId,
      'fieldName' => 'fileGroupId',
      'maxFiles' => 1,
      'acceptedFiles' => 'image/*',
    ])
  </div>
</div>
