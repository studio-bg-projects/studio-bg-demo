<div class="row">
  <div class="col-12 col-xl-4 pb-3 pb-lg-0">
    <h4 class="card-title mt-4 mb-2">Изображения</h4>
    <p class="text-body-secondary mb-0 fs-8">Прикачете изображения от
      <strong class="text-primary">една композиция</strong>
      . Може да прикачите до
      <strong>10 снимки</strong>
      .
    </p>
  </div>

  <div class="col-12 col-xl-8">
    <div class="card">
      <div class="card-body pb-5">
        <h4 class="card-title mb-4">Прикачване на изображения</h4>
        <input type="file" class="form-control " id="f-photos" name="photos[]" accept="image/*" multiple/>

        {{--        @include('erp.uploads.uploader', [--}}
        {{--          'groupType' => 'visual-detector',--}}
        {{--          'groupId' => $gptRequest->fileGroupId,--}}
        {{--          'fieldName' => 'fileGroupId',--}}
        {{--        ])--}}
        {{--        @if($errors->has('fileGroupId'))--}}
        {{--          <div class="alert alert-outline-danger py-2 fs-8">--}}
        {{--            {{ $errors->first('fileGroupId') }}--}}
        {{--          </div>--}}
        {{--        @endif--}}
      </div>
    </div>
  </div>
</div>
