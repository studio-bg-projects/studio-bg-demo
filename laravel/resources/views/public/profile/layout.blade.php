@extends('layouts.empty')

@section('content')
  <main class="main bg-body-tertiary dark__bg-gray-1200">
    <div class="row flex-center min-vh-100 g-0 py-5">
      <div class="col-11 col-sm-10 col-xl-8">
        <div class="card border">
          <div class="card-body">
            <div class="text-center mb-5">
              <div class="avatar avatar-4xl mb-4">
                <img class="rounded-circle" src="{{ auth()->user()->avatarUrl }}" alt="{{ auth()->user()->fullName }}"/>
              </div>
              <h2 class="text-body-highlight">
                <span class="fw-normal">Здравей,</span>
                {{ auth()->user()->fullName }}
              </h2>
            </div>

            <ul class="nav nav-underline d-flex justify-content-center">
              <li class="nav-item">
                <a href="{{ url('/profile') }}" class="nav-link px-3 text-uppercase  {{ Request::is('profile') ? 'active' : '' }}">
                  <i class="me-2 fa-regular fa-user"></i>
                  Профил
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('/profile/update') }}" class="nav-link px-3 text-uppercase  {{ Request::is('profile/update') ? 'active' : '' }}">
                  <i class="me-2 fa-regular fa-user-pen"></i>
                  Редакция
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('/profile/password-change') }}" class="nav-link px-3 text-uppercase  {{ Request::is('profile/password-change') ? 'active' : '' }}">
                  <i class="me-2 fa-regular fa-key"></i>
                  Смяна на парола
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('/auth/logout') }}" class="nav-link px-3 text-uppercase  {{ Request::is('auth/logout') ? 'active' : '' }}">
                  <i class="fa-regular fa-right-from-bracket me-2"></i>
                  Изход
                </a>
              </li>
            </ul>

            @yield('profile-content')

            <hr/>

            <div class="text-center">
              <a href="{{ url('/erp') }}" class="btn btn-lg btn-phoenix-primary mt-2">
                <i class="fa-regular fa-rocket me-2"></i>
                ERP
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection
