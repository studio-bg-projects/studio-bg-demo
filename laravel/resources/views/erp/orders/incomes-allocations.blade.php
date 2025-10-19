@extends('layouts.app')

@section('content')
  @include('erp.orders.partials.navbar')

  <h1 class="h4 mb-5">Поръчка #{{ $order->id }} - Свързани плащания</h1>

  @include('erp.incomes.partials.results-incomes-allocations')
@endsection
