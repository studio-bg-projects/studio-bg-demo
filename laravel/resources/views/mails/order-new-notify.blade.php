@extends('layouts.mail')

@section('content')
  <h1 style="color: #000; text-align: center;">Системна поща</h1>

  <h2 style="color: #000; text-align: center;">Имате нова поръчка #{{ $order->id }}</h2>

  <hr/>

  <h3 style="color: #000;">Поръчка</h3>
  <p>
    <strong>ID:</strong>
    #{{ $order->id }}
  </p>
  @foreach ($order->shopData->order_total as $row)
    <p>
      <strong>{{ $row->title }}:</strong>
      {{ price($row->value) }}
    </p>
  @endforeach
  <p>
    <strong>Линк към поръчката:</strong>
    <a href="{{ url('erp/orders/view/' . $order->id) }}" target="_blank">{{ url('erp/orders/view/' . $order->id) }}</a>
  </p>

  <hr/>

  <h3 style="color: #000;">Клиент</h3>
  @if ($order->customer)
    <p>
      <strong>Име:</strong>
      {{ $order->customer->firstName }} {{ $order->customer->lastName }}
    </p>

    <p>
      <strong>Фирма:</strong>
      {{ $order->customer->companyName }}
    </p>

    <p>
      <strong>Линк към клиента:</strong>
      <a href="{{ url('erp/customers/update/' . $order->customer->id) }}" target="_blank">{{ url('erp/customers/update/' . $order->customer->id) }}</a>
    </p>
  @else
    <strong>Няма намерен</strong>
  @endif

  <hr/>

  <h3 style="color: #000;">Търговски представител</h3>
  @if ($order->customer && $order->customer->salesRepresentative)
    <p>
      <strong>Име:</strong>
      {{ $order->customer->salesRepresentative->nameBg }}
    </p>
  @else
    <strong>Няма намерен</strong>
  @endif
@endsection
