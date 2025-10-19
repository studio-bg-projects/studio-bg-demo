<div class="sticky-bottom bg-body border-top w-100 p-3" data-bulk-node="wrapper" style="display: none;">
  <form action="{{ url('/erp/feeds-imports-items/bulk') }}" method="post">
    @csrf

    <div class="d-flex">
      <input type="hidden" name="backto" value="{{ request()->getRequestUri() }}"/>
      <div data-bulk-node="ids"></div>

      <select name="action" class="form-select form-select-sm me-3" data-bulk-node="action" style="max-width: 20rem;">
        <option value="">Избери</option>
        <option value="ignore">
          Игнорирай
        </option>
        <option value="unignore">
          Махни от игнорирани
        </option>
      </select>

      <button type="submit" class="btn btn-primary btn-sm" data-bulk-node="button" disabled>Продължи</button>
    </div>
  </form>
</div>

<script type="module">
  const $checkAll = $('[data-bulk-node="check-all"]');
  const $checkboxes = $('[data-bulk-node="checkbox"]');
  const $wrapper = $('[data-bulk-node="wrapper"]');
  const $action = $('[data-bulk-node="action"]');
  const $button = $('[data-bulk-node="button"]');
  const $ids = $('[data-bulk-node="ids"]');

  function updateChecks() {
    const checked = $checkboxes
      .filter(function () {
        return $(this).is(':checked');
      })
      .map(function () {
        return $(this).val();
      })
      .toArray();

    $ids.html('');
    checked.forEach(id => {
      $ids.append(`<input type="hidden" name="ids[]" value="${id}"/>`);
    })

    if (checked.length) {
      $wrapper.fadeIn();
    } else {
      $wrapper.fadeOut();
    }
  }

  $checkAll.change(function () {
    $checkboxes.prop('checked', $(this).is(':checked'))
    updateChecks();
  });

  $action.change(function () {
    $button.prop('disabled', !$(this).val())
  });

  $checkboxes.change(updateChecks);
</script>
