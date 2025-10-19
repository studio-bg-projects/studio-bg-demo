<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12 col-xl-6">
        <label class="app-form-label required" for="f-nameBg">Име [BG]</label>
        <input type="text" class="form-control @if($errors->has('nameBg')) is-invalid @endif" id="f-nameBg" name="nameBg" value="{{ $salesRepresentative->nameBg }}" placeholder="Иван Иванов..." required/>
        @if($errors->has('nameBg'))
          <div class="invalid-feedback">
            {{ $errors->first('nameBg') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label required" for="f-nameEn">Име [EN]</label>
        <input type="text" class="form-control @if($errors->has('nameEn')) is-invalid @endif" id="f-nameEn" name="nameEn" value="{{ $salesRepresentative->nameEn }}" placeholder="Ivan Ivanov..." required/>
        @if($errors->has('nameEn'))
          <div class="invalid-feedback">
            {{ $errors->first('nameEn') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-titleBg">Длъжност [BG]</label>
        <input type="text" class="form-control @if($errors->has('titleBg')) is-invalid @endif" id="f-titleBg" name="titleBg" value="{{ $salesRepresentative->titleBg }}" placeholder="Търговски Представител..."/>
        @if($errors->has('titleBg'))
          <div class="invalid-feedback">
            {{ $errors->first('titleBg') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-titleEn">Длъжност [EN]</label>
        <input type="text" class="form-control @if($errors->has('titleEn')) is-invalid @endif" id="f-titleEn" name="titleEn" value="{{ $salesRepresentative->titleEn }}" placeholder="Sales Manager..."/>
        @if($errors->has('titleEn'))
          <div class="invalid-feedback">
            {{ $errors->first('titleEn') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label required" for="f-phone1">Телефон - основен</label>
        <input type="text" class="form-control @if($errors->has('phone1')) is-invalid @endif" id="f-phone1" name="phone1" value="{{ $salesRepresentative->phone1 }}" placeholder="+359888123123" required/>
        @if($errors->has('phone1'))
          <div class="invalid-feedback">
            {{ $errors->first('phone1') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-phone2">Телефон - допълнителен</label>
        <input type="text" class="form-control @if($errors->has('phone2')) is-invalid @endif" id="f-phone2" name="phone2" value="{{ $salesRepresentative->phone2 }}" placeholder="+359888123321"/>
        @if($errors->has('phone2'))
          <div class="invalid-feedback">
            {{ $errors->first('phone2') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label required" for="f-email1">Имейл - основен</label>
        <input type="email" class="form-control @if($errors->has('email1')) is-invalid @endif" id="f-email1" name="email1" value="{{ $salesRepresentative->email1 }}" placeholder="ivan.ivanov@insidetrading.bg..." required/>
        @if($errors->has('email1'))
          <div class="invalid-feedback">
            {{ $errors->first('email1') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-email2">Имейл - допълнителен</label>
        <input type="email" class="form-control @if($errors->has('email2')) is-invalid @endif" id="f-email2" name="email2" value="{{ $salesRepresentative->email2 }}" placeholder="sales@insidetrading.bg..."/>
        @if($errors->has('email2'))
          <div class="invalid-feedback">
            {{ $errors->first('email2') }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<hr class="my-3"/>

<div class="card">
  <div class="card-body pb-1">
    <h2 class="h4 card-title mb-4">Добавяне на снимка</h2>
    @include('erp.uploads.uploader', [
      'groupType' => \App\Enums\UploadGroupType::SalesRepresentatives->value,
      'groupId' => $salesRepresentative->fileGroupId,
      'fieldName' => 'fileGroupId',
      'maxFiles' => 1,
      'acceptedFiles' => 'image/*',
    ])
  </div>
</div>
