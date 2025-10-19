<div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
  <div class="table-responsive">
    <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
      <thead>
      <tr class="bg-body-highlight">
        <th class="nosort border-top border-translucent">
          Задача
        </th>
        <th class="nosort border-top border-translucent">
          Последно стартиране
        </th>
        <th class="nosort border-top border-translucent">
          Стартиране след
        </th>
        <th class="nosort border-top border-translucent" style="width: 10px;"></th>
      </tr>
      </thead>
      <tbody>
      @foreach ($schedulers as $row)
        <tr>
          <td>
            <a href="{{ url('/erp/schedulers/view/' . $row->jobId) }}">
              {{ $row->jobId }}
            </a>
          </td>
          <td>
            {{ $row->lastSync }}
          </td>
          <td class="white-space-nowrap" data-timer-interval="{{ $row->interval }}" data-timer-count="{{ time() - $row->lastSync->timestamp }}">
            <p class="text-body-secondary fs-10 mb-0" data-timer-label>- / -</p>
            <div class="progress" style="height: 3px;">
              <div class="progress-bar bg-primary" style="width: 0" data-timer-progress></div>
            </div>
          </td>
          <td>
            <a href="{{ url('/erp/schedulers/run/' . $row->jobId) }}">
              <i class="fa-regular fa-rocket"></i>
            </a>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>

<script type="module">
  $('[data-timer-interval]').each(function () {
    const interval = $(this).data('timer-interval');
    let count = $(this).data('timer-count');

    const $label = $(this).find('[data-timer-label]');
    const $progress = $(this).find('[data-timer-progress]');

    setInterval(() => {
      const percent = (100 / interval) * count++;
      const overreach = percent >= 100;

      $label.html(`<span class="${overreach && 'text-danger'}">${count}</span> / ${interval}`);
      $progress.css({width: `${overreach ? 100 : percent}%`});
    }, 1000)
  });
</script>
