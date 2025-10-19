@extends('layouts.app')

@section('content')
  @include('erp.banners.partials.navbar')

  <h1 class="h4 mb-5">Начални банери</h1>

  <form method="post" action="?" class="mb-5" data-disable-on-submit>
    @csrf
    <div class="card">
      <div class="card-body pb-1">
        <div class="row">
          @foreach ($banners as $i => $banner)
            <div class="col-12 col-xl-6">
              <h2 class="h4 card-title mb-4">Банер {{ $i + 1 }}</h2>

              <div class="mb-4">
                <label class="app-form-label mt-n1" for="f-banner-{{ $i }}-url">URL</label>
                <input type="text" class="form-control @if($errors->has('banner-' . $i . '-url')) is-invalid @endif" id="f-banner-{{ $i }}-url" name="banner-{{ $i }}-url" value="{{ $banner->url }}" placeholder="https://shop.insidetrading.bg/..."/>
                @if($errors->has('banner-' . $i . '-url'))
                  <div class="invalid-feedback">
                    {{ $errors->first('banner-' . $i . '-url') }}
                  </div>
                @endif
              </div>

              <div class="mb-4">
                <label class="app-form-label mt-n1" for="f-banner-{{ $i }}-color">Цвят</label>
                <input type="color" class="form-control @if($errors->has('banner-' . $i . '-color')) is-invalid @endif" id="f-banner-{{ $i }}-color" name="banner-{{ $i }}-color" value="{{ $banner->color }}" placeholder="#FF0000..."/>
                @if($errors->has('banner-' . $i . '-color'))
                  <div class="invalid-feedback">
                    {{ $errors->first('banner-' . $i . '-color') }}
                  </div>
                @endif
              </div>

              <div class="row">
                <div class="col">
                  <label class="app-form-label" for="f-banner-{{ $i }}-textBg">Бутон текст [BG]</label>
                  <input type="text" class="form-control @if($errors->has('banner-' . $i . '-textBg')) is-invalid @endif" id="f-banner-{{ $i }}-textBg" name="banner-{{ $i }}-textBg" value="{{ $banner->{'textBg'} }}" placeholder="Вижте нашите предложения..."/>
                  @if($errors->has('banner-' . $i . '-textBg'))
                    <div class="invalid-feedback">
                      {{ $errors->first('banner-' . $i . '-textBg') }}
                    </div>
                  @endif
                </div>
                <div class="col">
                  <label class="app-form-label" for="f-banner-{{ $i }}-textEn">Бутон текст [EN]</label>
                  <input type="text" class="form-control @if($errors->has('banner-' . $i . '-textEn')) is-invalid @endif" id="f-banner-{{ $i }}-textEn" name="banner-{{ $i }}-textEn" value="{{ $banner->{'textEn'} }}" placeholder="More details..."/>
                  @if($errors->has('banner-' . $i . '-textEn'))
                    <div class="invalid-feedback">
                      {{ $errors->first('banner-' . $i . '-textEn') }}
                    </div>
                  @endif
                </div>
              </div>

              <hr class="max-3"/>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <hr class="my-3"/>

    <div class="card">
      <div class="card-body pb-1">
        <h2 class="h4 card-title mb-4">Банери</h2>

        <script type="module">
          window.bannersLabelCallback = (seq) => {
            return 'Банер: ' + (seq + 1);
          };
        </script>

        @include('erp.uploads.uploader', [
          'groupType' => \App\Enums\UploadGroupType::Banners->value,
          'groupId' => 'home-banners',
          'maxFiles' => count($banners),
          'labelCallback' => 'window.bannersLabelCallback',
        ])
      </div>
    </div>

    <div class="text-end">
      <button class="btn btn-primary btn-lg mt-3" type="submit">
        <i class="fa-regular fa-pen-to-square me-2"></i>
        Редакция
      </button>
    </div>
  </form>
@endsection
