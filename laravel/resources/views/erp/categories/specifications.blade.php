@extends('layouts.app')

@section('content')
  @include('erp.categories.partials.navbar')

  <h1 class="h4 mb-5">{{ $category->getOriginal('nameBg') }} - Спецификации</h1>

  <p class="text-body-tertiary">За да добавите спецификации към категорията, преместете ги от лявата колона ("Налични спецификации") към дясната колона ("Зададени спецификации"). Ако искате да премахнете спецификации от категорията, просто ги върнете обратно.</p>

  <hr/>

  <div class="row g-3">
    <div class="col-6">
      <h5 class="card-title mb-2">Налични спецификации</h5>
      <input type="text" class="form-control form-control-sm" placeholder="Търси..." data-simple-search="#js-specifications-from li"/>
    </div>
    <div class="col-6">
      <h5 class="card-title mb-2">Зададени спецификации</h5>
      <input type="text" class="form-control form-control-sm" placeholder="Търси..." data-simple-search="#js-specifications-to li"/>
    </div>

    <div class="col-6">
      <ul class="list-unstyled sortable-place p-2" id="js-specifications-from">
        @foreach($specifications as $specification)
          <li class="sortable-item-wrapper mb-2 fs-9" data-id="{{ $specification->id }}">
            <p class="mb-0 sortable-item bg-body-secondary px-3 py-1 rounded-2">
              {{ $specification->nameBg }}
            </p>
          </li>
        @endforeach
      </ul>
    </div>

    <div class="col-6">
      <div class="position-sticky" style="top: 8rem;">
        <ul class="list-unstyled sortable-place p-2" id="js-specifications-to">
          @foreach($assignedSpecification as $specification)
            <li class="sortable-item-wrapper mb-2 fs-9" data-id="{{ $specification->id }}">
              <p class="mb-0 sortable-item bg-body-secondary px-3 py-1 rounded-2">
                {{ $specification->nameBg }}
              </p>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>

  <script type="module">
    $(function () {
      // Sync
      const sortSerialize = async () => {
        const ids = $('#js-specifications-to li').map(function () {
          return $(this).data('id');
        }).toArray();

        await $.ajax({
          url: `?`,
          type: 'POST',
          data: {
            ids
          },
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        window.appToast({
          body: 'Промените са записани.'
        });
      };

      // Sortable
      const $containers = $('#js-specifications-from, #js-specifications-to');
      $containers.each(function () {
        new Sortable($(this).get(0), {
          animation: 150,
          group: {
            name: 'shared'
          },
          delayOnTouchOnly: true, // Useful for mobile touch
          forceFallback: true, // Ignore the HTML5 DnD behaviour
          onStart() {
            $containers.addClass('sortable-hover');
          },
          onEnd() {
            $containers.removeClass('sortable-hover');

            sortSerialize();
          },
        });
      });

      // DB Click
      $('#js-specifications-from li, #js-specifications-to li').on('dblclick', function () {
        const $li = $(this);
        const $ul = $li.closest('ul');
        const isItFrom = $ul.attr('id') === 'js-specifications-from';

        if (isItFrom) {
          $li.prependTo('#js-specifications-to');
        } else {
          $li.prependTo('#js-specifications-from');
        }

        $li.hide().fadeIn();
        sortSerialize();
      })
    });
  </script>
@endsection
