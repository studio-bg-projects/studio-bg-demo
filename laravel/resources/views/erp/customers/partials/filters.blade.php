<form method="get" action="{{ url('/erp/customers') }}">
  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-email" name="filter[email]" value="{{ request()->filter['email'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-email">Имейл</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-email" name="op[email]">
          @foreach(App\Http\Controllers\Erp\UsersController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['email']) && request()->op['email'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-email">Оператор</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-companyId" name="filter[companyId]" value="{{ request()->filter['companyId'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-companyId">ЕИК</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-companyId" name="op[companyId]">
          @foreach(App\Http\Controllers\Erp\UsersController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['companyId']) && request()->op['companyId'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-companyId">Оператор</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-companyName" name="filter[companyName]" value="{{ request()->filter['companyName'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-companyName">Фирма</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-companyName" name="op[companyName]">
          @foreach(App\Http\Controllers\Erp\UsersController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['companyName']) && request()->op['companyName'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-companyName">Оператор</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="hidden" name="op[statusType]" value="eq"/>
        <select class="form-select" id="f-filter-statusType" name="filter[statusType]">
          <option value="" {{ (request()->filter['statusType'] ?? '') === '' ? 'selected' : '' }}>Всички</option>
          @foreach (App\Enums\CustomerStatusType::cases() as $statusType)
            <option value="{{ $statusType->value }}" {{ (request()->filter['statusType'] ?? '') === $statusType->value ? 'selected' : '' }}>
              {{ \App\Services\MapService::customerStatusType($statusType)->label }}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-type">Статус</label>
      </div>
    </div>

    <div class="col">
      <div class="form-floating">
        <input type="hidden" name="op[groupId]" value="eq"/>
        <select class="form-select" id="f-filter-groupId" name="filter[groupId]">
          <option value="-1::all">Всички</option>
          @foreach($customersGroups as $row)
            <option value="{{ $row->id }}" {{ isset(request()->filter['groupId']) && request()->filter['groupId'] == $row->id ? 'selected' : '' }}>
              {{ $row->nameBg }}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-groupId">Група</label>
      </div>
    </div>
  </div>

  <div class="d-inline-flex gap-2 mb-5 w-100">
    <button class="btn btn-primary px-9" type="submit">Покажи</button>
    <a href="?" class="btn btn-phoenix-primary px-4 position-relative">
      <i class="fa-regular fa-arrows-rotate"></i>
      Изчисти
      @if (request()->filter)
        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
      @endif
    </a>
  </div>
</form>
