@extends('layouts.app')

@section('content')
  @include('erp.schedulers.partials.navbar')

  <div class="card border my-4">
    <div class="card-header p-4 border-bottom bg-body">
      <h2 class="h4 text-body mb-0">{{ $schedule->jobId }} - Преглед</h2>
    </div>
    <div class="card-body">
      <dl>
        <dt>Задача</dt>
        <dd>{{ $schedule->jobId }}</dd>

        <dt>Интервал</dt>
        <dd>{{ $schedule->interval }}</dd>

        <dt>Последно стартиране</dt>
        <dd>{{ $schedule->lastSync }}</dd>

        <dt>Приключила на</dt>
        <dd>{{ $schedule->endAt }}</dd>
      </dl>

      <hr/>

      <a class="btn btn-phoenix-primary mt-3" href="{{ url('/erp/schedulers/run/' . $schedule->jobId) }}" target="_blank">
        <i class="fa-regular fa-rocket"></i>
        Стартирай ръчно
      </a>
    </div>
  </div>
@endsection
