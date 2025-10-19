@extends('layouts.app')

@section('content')
  @include('erp.products.partials.navbar')

  <h1 class="h4 mb-5">{{ $product->getOriginal('nameBg') }} - История</h1>

  <div class="card">
    <div class="card-body">
      <table class="table table-sm fs-9">
        <thead>
        <tr>
          <th>#</th>
          <th>Действие</th>
          <th>Оригинални данни</th>
          <th>Нови данни</th>
          <th>Локация</th>
          <th>Дата</th>
        </tr>
        </thead>
        <tbody>
        @foreach($logs as $log)
          <tr>
            <td>
              {{ $log->id }}
            </td>
            <td>
              {{ $log->action }}
            </td>
            <td>
              <pre class="mb-0" style="max-height: 350px; max-width: 250px; overflow-y: auto;">{{ json_encode($log->original, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
            </td>
            <td>
              <pre class="mb-0" style="max-height: 350px; max-width: 250px; overflow-y: auto;">{{ json_encode($log->new, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
            </td>
            <td>
              <pre class="mb-0" style="max-height: 350px; max-width: 250px; overflow-y: auto;">{{ $log->place }}</pre>
            </td>
            <td>
              {{ $log->createdAt }}
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection
