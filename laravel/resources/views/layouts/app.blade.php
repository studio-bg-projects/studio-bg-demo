<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
@include('layouts.partials.head')

<body>

@include('shared.alerts')

@if (Request()->emptyLayout)
  @yield('content')
@else
  <main class="main" id="top">
    <nav class="navbar navbar-vertical navbar-expand-lg">
      <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        @include('/layouts/partials/sidebar')
      </div>
      <div class="navbar-vertical-footer">
        <button class="btn navbar-vertical-toggle border-0 fw-semibold w-100 white-space-nowrap d-flex align-items-center" data-app-sidebar-toggle>
          <span class="navbar-footer-icon fs-8">
            {{-- .uil-left-arrow-to-left used ftom laravel/resources/scss/theme/_navbar-vertical.scss --}}
            <i class="fa-regular fa-arrow-left-from-line uil-left-arrow-to-left"></i>
          </span>
          <span class="navbar-footer-icon-collapsed fs-8">
            {{-- .uil-arrow-from-right used ftom laravel/resources/scss/theme/_navbar-vertical.scss --}}
            <i class="fa-regular fa-arrow-right-from-line uil-arrow-from-right"></i>
          </span>
          <span class="navbar-vertical-footer-text ms-2">Прибран изглед</span>
        </button>
      </div>
    </nav>
    <nav class="navbar navbar-top fixed-top navbar-expand" data-navbar-appearance="darker">
      <div class="collapse navbar-collapse justify-content-between">
        <div class="navbar-logo">
          <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation">
            <span class="navbar-toggle-icon">
              <span class="toggle-line"></span>
            </span>
          </button>
          <a class="navbar-brand me-1 me-sm-3" href="{{ url('/erp/') }}">
            <div class="d-flex align-items-center">
              <div class="d-flex align-items-center">
                <img src="{{ asset('/img/logo-white.svg') }}" alt="Gavazov.net - Demo" style="height: 40px;"/>
              </div>
            </div>
          </a>
        </div>

        <ul class="navbar-nav navbar-nav-icons flex-row">
          @if (session()->has('impersonateAdminId'))
            <li class="nav-item anim anim-pulse" data-bs-theme="dark">
              <a href="{{ url('/erp/impersonate/stop') }}" class="btn btn-subtle-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Върни се като стария потребител">
                <i class="fa-regular fa-eyes fs-8"></i>
              </a>
            </li>
          @endif
          <li class="nav-item dropdown">
            <div class="theme-control-toggle px-2" data-bs-toggle="dropdown" data-bs-display="static" id="bd-theme">
              <div class="mb-0 theme-control-toggle-label theme-control-toggle-light" style="height: 40px;width: 40px;" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Избор на тема">
                <i class="fa-regular fa-lightbulb theme-icon-active fs-8"></i>
              </div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end navbar-dropdown-caret shadow p-0">
              <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light">
                  <i class="fa-regular fa-lightbulb me-2"></i>
                  Светло
                </button>
              </li>
              <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark">
                  <i class="fa-regular fa-sunglasses me-2"></i>
                  Тъмно
                </button>
              </li>
              <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto">
                  <i class="fa-regular fa-sun-bright me-2"></i>
                  Автоматично
                </button>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>

    <div class="content">
      @yield('content')

      <footer class="footer position-absolute">
        <div class="row g-0 justify-content-between align-items-center h-100">
          <div class="col-12 col-sm-auto text-center">
            <p class="mb-0 mt-2 mt-sm-0 text-body">Gavazov.net &mdash; Demo
              <span class="d-none d-sm-inline-block"></span>
              <br class="d-sm-none"/>{{ date('Y') }} &copy;
              <span class="d-none d-sm-inline-block mx-1">|</span>
              Developed by
              <a class="mx-1" href="https://gavazov.net" target="_blank">Gavazov.net</a>
            </p>
          </div>
        </div>
      </footer>
    </div>
  </main>
@endif

</body>
</html>
