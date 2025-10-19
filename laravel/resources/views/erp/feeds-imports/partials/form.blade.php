<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        {{-- no .mt-3 for 1st title --}}
        <h2 class="h5 pb-2 border-bottom border-dashed">Данни за доставчика</h2>
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label required" for="f-providerName">Име на доставчика</label>
        <input type="text" class="form-control @if($errors->has('providerName')) is-invalid @endif" id="f-providerName" name="providerName" value="{{ $feed->providerName }}" required/>
        @if($errors->has('providerName'))
          <div class="invalid-feedback">{{ $errors->first('providerName') }}</div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label required" for="f-adapterName">Адаптер за транформация</label>
        <input type="text" class="form-control @if($errors->has('adapterName')) is-invalid @endif" id="f-adapterName" name="adapterName" value="{{ $feed->adapterName }}" required/>
        @if($errors->has('adapterName'))
          <div class="invalid-feedback">{{ $errors->first('adapterName') }}</div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-feedUrl">Feed URL</label>
        <input type="text" class="form-control @if($errors->has('feedUrl')) is-invalid @endif" id="f-feedUrl" name="feedUrl" value="{{ $feed->feedUrl }}" required/>
        @if($errors->has('feedUrl'))
          <div class="invalid-feedback">{{ $errors->first('feedUrl') }}</div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-techEmail">Имейл за връзка</label>
        <input type="text" class="form-control @if($errors->has('techEmail')) is-invalid @endif" id="f-techEmail" name="techEmail" value="{{ $feed->techEmail }}"/>
        @if($errors->has('techEmail'))
          <div class="invalid-feedback">{{ $errors->first('techEmail') }}</div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-markupPercent">% надценка</label>
        <input type="number" step="0.01" class="form-control @if($errors->has('markupPercent')) is-invalid @endif" id="f-markupPercent" name="markupPercent" value="{{ $feed->markupPercent }}" required/>
        @if($errors->has('markupPercent'))
          <div class="invalid-feedback">{{ $errors->first('markupPercent') }}</div>
        @endif
      </div>

      <div class="col-12 col-xl-12">
        <label class="app-form-label" for="f-syncSchedule">График за синхронизация</label>
        <div id="js-sync-schedule">
          @foreach(($feed->syncSchedule ?: [null]) as $index => $time)
            <div class="input-group mb-2 schedule-row">
              <input type="time" class="form-control @if($errors->has('syncSchedule.' . $index)) is-invalid @endif" name="syncSchedule[]" value="{{ old('syncSchedule.' . $index, $time) }}"/>
              <button type="button" class="btn btn-outline-danger" onclick="window.removeSchedule(this)">
                <i class="fa-regular fa-trash-can"></i>
              </button>
            </div>
            @if($errors->has('syncSchedule.' . $index))
              <div class="invalid-feedback">{{ $errors->first('syncSchedule.' . $index) }}</div>
            @endif
          @endforeach
        </div>
        <button type="button" class="btn btn-sm btn-phoenix-info text-primary w-100" id="js-add-schedule">
          <i class="fa-regular fa-plus"></i>
          Добави час
        </button>
        @if($errors->has('syncSchedule'))
          <div class="invalid-feedback">{{ $errors->first('syncSchedule') }}</div>
        @endif
      </div>

      <div class="col-12">
        <label class="app-form-label" for="f-note">Бележка</label>
        <textarea class="form-control @if($errors->has('note')) is-invalid @endif" id="f-note" name="note" rows="4">{{ $feed->note }}</textarea>
        @if($errors->has('note'))
          <div class="invalid-feedback">{{ $errors->first('note') }}</div>
        @endif
      </div>
    </div>
  </div>
</div>

<script type="module">
  window.removeSchedule = function (btn) {
    $(btn).closest('.schedule-row').remove();
  };

  $('#js-add-schedule').on('click', function () {
    const $row = $(
      `<div class="input-group mb-2 schedule-row">
         <input type="time" class="form-control" name="syncSchedule[]" value=""/>
         <button type="button" class="btn btn-outline-danger" onclick="window.removeSchedule(this)">
           <i class="fa-regular fa-trash-can"></i>
         </button>
       </div>`
    );
    $('#js-sync-schedule').append($row);
  });
</script>
