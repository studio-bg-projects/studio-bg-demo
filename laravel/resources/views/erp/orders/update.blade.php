@extends('layouts.app')

@section('content')
  @include('erp.orders.partials.navbar')

  <h1 class="h4 mb-5">Поръчка #{{ $order->getOriginal('id') }} - Редакция</h1>

  <form method="post" action="?" class="mb-5" data-disable-on-submit>
    @csrf
    @include('erp.orders.partials.form')

    <div class="text-end">
      <button class="btn btn-primary btn-lg mt-3" type="submit">
        <i class="fa-regular fa-pen-to-square me-2"></i>
        Редакция
      </button>
    </div>
  </form>
@endsection
