@extends('layouts.app')

@section('content')
  @include('erp.products.partials.navbar')

  <h1 class="h4 mb-5">Добавяне на продукт</h1>

  <div class="alert alert-subtle-info" role="alert">
    <i class="fa-regular fa-sparkles"></i>
    Създайте нов продукт, след което системата ще може да провери за данни от външни източници.
  </div>

  <form method="post" action="?" class="mb-5" data-disable-on-submit>
    @csrf
    @include('erp.products.partials.form')

    <div class="text-end">
      <button class="btn btn-primary btn-lg mt-3" type="submit">
        <i class="fa-regular fa-plus me-2"></i>
        Добави
      </button>
    </div>
  </form>
@endsection
