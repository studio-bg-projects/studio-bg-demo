@extends('layouts.app')

@section('content')
  @include('erp.demos.partials.navbar')

  <h1 class="h4 mb-5">Добавяне на демо</h1>

  <form method="post" action="?" class="mb-5" data-disable-on-submit>
    @csrf
    @include('erp.demos.partials.form')

    <div class="text-end">
      <button class="btn btn-primary btn-lg mt-3" type="submit">
        <i class="fa-regular fa-plus me-2"></i>
        Добави
      </button>
    </div>
  </form>
@endsection
