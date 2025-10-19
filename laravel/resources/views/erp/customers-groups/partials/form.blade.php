<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-nameBg">Име на групата [BG]</label>
        <input type="text" class="form-control @if($errors->has('nameBg')) is-invalid @endif" id="f-nameBg" name="nameBg" value="{{ $customersGroup->nameBg }}" placeholder="Име на групата..." required/>
        @if($errors->has('nameBg'))
          <div class="invalid-feedback">
            {{ $errors->first('nameBg') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-nameEn">Име на групата [EN]</label>
        <input type="text" class="form-control @if($errors->has('nameEn')) is-invalid @endif" id="f-nameEn" name="nameEn" value="{{ $customersGroup->nameEn }}" placeholder="Name of the group..." required/>
        @if($errors->has('nameEn'))
          <div class="invalid-feedback">
            {{ $errors->first('nameEn') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-discountPercent">Процент отстъпка</label>
        <input type="number" step="0.01" min="-100" max="100" class="form-control @if($errors->has('discountPercent')) is-invalid @endif" id="f-discountPercent" name="discountPercent" value="{{ $customersGroup->discountPercent }}" placeholder="12.34%..." required/>
        @if($errors->has('discountPercent'))
          <div class="invalid-feedback">
            {{ $errors->first('discountPercent') }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
