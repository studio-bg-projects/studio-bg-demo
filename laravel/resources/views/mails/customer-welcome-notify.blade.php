@extends('layouts.mail')

@section('content')
  <h1 style="color: #000; text-align: center;">Системна поща</h1>

  <h2 style="color: #000; text-align: center;">Има нова регистрация</h2>

  <hr/>

  <h3 style="color: #000;">Клиент</h3>
  <p>
    <strong>Име:</strong>
    {{ $customer->firstName }} {{ $customer->lastName }}
  </p>

  <p>
    <strong>Фирма:</strong>
    {{ $customer->companyName }}
  </p>

  <p>
    <strong>Линк към клиента:</strong>
    <a href="{{ url('erp/customers/update/' . $customer->id) }}" target="_blank">{{ url('erp/customers/update/' . $customer->id) }}</a>
  </p>
@endsection
