@if (count($vehicleInspections))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th style="width: 5rem;" class="nosort border-top border-translucent"></th>

          <th class="nosort border-top border-translucent">
            Инспекция
          </th>

          <th class="nosort border-top border-translucent">
            Номер
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($vehicleInspections as $row)
          <tr>
            <td>
              <div class="d-block border border-translucent rounded-2 table-preview" style="width: 80px; height: 80px;">
                <a href="{{ url("/vehicle-inspections/view/$row->id") }}">
                  @if ($row->files)
                    <img src="{{ Storage::url('uploads/vehicle-inspections/' . $row->id . '/' . $row->files[0]) }}" alt=""/>
                  @else
                    <img src="{{ asset('img/icons/file-placeholder.svg') }}" alt=""/>
                  @endif
                </a>
              </div>
            </td>
            <td>
              <a href="{{ url("/vehicle-inspections/view/$row->id") }}">
                {{ !empty($row->responseContent->summary) ? $row->responseContent->summary : '#' . $row->id }}
              </a>
            </td>
            <td>
              {{ $row->responseContent->plate ?? '-' }}
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
@else
  @include('/shared/no-rs')
@endif
