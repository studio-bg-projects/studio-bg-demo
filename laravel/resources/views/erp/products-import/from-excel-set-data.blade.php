@extends('layouts.app')

@section('content')
  @include('erp.products-import.partials.navbar')

  <h1 class="h4 mb-5">Импортиране на продукти от Excel</h1>

  <div class="alert alert-subtle-info" role="alert">
    <strong>Не забравяйте да натиснете бутона "Запази" в долната част на страницата.</strong>
  </div>

  <form method="post" action="?" class="mb-5" data-disable-on-submit>
    <input type="hidden" name="base64" value="{{ $base64 }}"/>

    @csrf
    <div class="table-responsive">
      <table class="table table-sm table-bordered fs-9">
        <thead>
        <tr>
          <th class="fw-normal" style="min-width: 100px;">Action</th>
          @foreach($headers as $column)
            <th class="fw-normal" style="min-width: 100px;">{{ $column }}</th>
          @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($rs as $item)
          <tr data-action="{{ $item['action'] }}">
            <td class="align-middle text-center">
              @if ($item['action'] === 'update')
                <div class="badge badge-phoenix fs-10 badge-phoenix-success">
                  <span class="fw-bold">{{ $item['action'] }}</span>
                  <i class="ms-1 fa-regular fa-pen-to-square"></i>
                </div>
              @elseif ($item['action'] === 'create')
                <div class="badge badge-phoenix fs-10 badge-phoenix-primary">
                  <span class="fw-bold">{{ $item['action'] }}</span>
                  <i class="ms-1 fa-regular fa-circle-plus"></i>
                </div>
              @elseif ($item['action'] === 'error')
                <div class="badge badge-phoenix fs-10 badge-phoenix-danger">
                  <span class="fw-bold">{{ $item['action'] }}</span>
                  <i class="ms-1 fa-regular fa-warning"></i>
                </div>
              @else
                <div class="badge badge-phoenix fs-10 badge-phoenix-secondary">
                  <span class="fw-bold">{{ $item['action'] }}</span>
                  <i class="ms-1 fa-regular fa-circle-question"></i>
                </div>
              @endif

              @if (!empty($item['error']))
                <p class="text-danger text-start">{{ $item['error'] }}</p>
              @endif
            </td>
            @foreach($headers as $column)
              <td class="@if (array_key_exists($column, $item['changes']) || $item['action'] === 'create') table-info @else opacity-25 @endif">
                @if (array_key_exists($column, $item['changes']))
                  <del>{!! !empty($item['originals'][$column]) ? $item['originals'][$column] : '<i>- празно -</i>' !!}</del>
                  <br/>
                @endif

                @if (array_key_exists($column, $item['data']))
                  {{ $item['data'][$column] }}
                @endif
              </td>
            @endforeach
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>

    <div class="text-center">
      <button class="btn btn-lg btn-primary btn-lg mt-5" type="submit">
        <i class="fa-regular fa-floppy-disk"></i>
        Запази промените
      </button>
    </div>
  </form>
@endsection
