<section class="mx-n4 px-4 mx-lg-n6 px-lg-6 pt-6 pb-9">
  <div class="bg-get-app"></div>
  <div class="container-medium position-relative">
    <div class="row g-0 justify-content-center">
      <div class="col-lg-10 col-xl-8 col-xxl-7">
        <div class="d-md-flex align-items-center gap-5 text-center text-md-start">
          <div class="mt-5 mt-md-0">
            <div class="d-none d-md-block">
              <img class="d-dark-none" src="{{ asset('/img/spot-illustrations/24.png') }}" alt="" width="200">
              <img class="d-light-none" src="{{ asset('/img/spot-illustrations/dark_24.png') }}" alt="" width="200">
            </div>
            <h3 class="fw-bolder mt-4">
              @if (isset($noRsTitle))
                {{ $noRsTitle }}
              @else
                No results found
              @endif
            </h3>
            <p class="text-body-tertiary">
              @if (isset($noRsSubTitle))
                {{ $noRsSubTitle }}
              @else
                Results may be absent or there may be
                <span class="@if (request()->filter) text-primary @endif">filters set</span>
                that hide them.
              @endif
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
