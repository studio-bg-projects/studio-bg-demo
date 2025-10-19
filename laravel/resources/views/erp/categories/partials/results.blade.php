@if (count($categories))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th style="width: 5rem;" class="nosort border-top border-translucent @if (request('sort') == 'nameBg') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif"></th>
          <th class="nosort border-top border-translucent">
            Име
          </th>
          <th class="nosort border-top border-translucent">
            Позиция
          </th>
          <th class="nosort border-top border-translucent">
            Статус
          </th>
          <th class="nosort border-top border-translucent">
            Скрит
          </th>
          <th class="nosort border-top border-translucent">
            Слайдър
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($categories as $row)
          <tr>
            <td>
              <div class="d-block border border-translucent rounded-2 table-preview">
                @if ($row->uploads->isNotEmpty())
                  <img src="{{ $row->uploads->first()->urls->tiny }}" alt=""/>
                @else
                  <img src="{{ asset('img/icons/image-placeholder.svg') }}" alt=""/>
                @endif
              </div>
            </td>
            <td>
              @if ($row->parent)
                <span class="opacity-75 mx-2">{{ $row->parent->nameBg }} &raquo;</span>
              @endif

              <a href="{{ url('/erp/categories/update/' . $row->id) }}">
                {{ $row->nameBg }}
              </a>
            </td>
            <td>
              {{ $row->sortOrder }}
            </td>
            <td>
              @if ($row->isActive)
                <span class="badge badge-phoenix badge-phoenix-success">Активен</span>
              @else
                <span class="badge badge-phoenix badge-phoenix-secondary">Неактивен</span>
              @endif
            </td>
            <td>
              @if ($row->isHidden)
                Да
              @else
                Не
              @endif
            </td>
            <td>
              @if ($row->isHomeSlider)
                Да
              @else
                Не
              @endif
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
@else
  @include('shared.no-rs')
@endif
