<div class="card h-100 border-0 bg-transparent">
  <div class="card-header border-bottom-0 pb-0 d-flex flex-row">
    <h3 class="text-body-emphasis">Регистрации по дни</h3>

    <a href="{{ url('/erp/customers') }}" class="ms-auto btn btn-phoenix-secondary">
      <i class="fa-regular fa-person"></i>
      Всички клиенти
    </a>
  </div>
  <div class="card-body py-0">
    <div id="chart-customers" style="width: 100%; height: 400px;"></div>
  </div>
</div>

<script type="module">
  $(function () {
    const erpData = @json($chartCustomers);

    const dates = erpData.map(item => item.date);
    const counts = erpData.map(item => item.cnt);

    const $chartDom = $('#chart-customers');

    if (!$chartDom.length) {
      return;
    }

    const chart = echarts.init($chartDom.get(0));

    chart.setOption({
      responsive: true,
      maintainAspectRatio: false,

      xAxis: {
        type: 'category',
        data: dates,
        axisLabel: {
          rotate: 45
        }
      },

      yAxis: {
        type: 'value',
        // name: 'Брой клиенти',
      },

      series: [{
        data: counts,
        type: 'line',
        smooth: true,
        areaStyle: {}
      }],

      tooltip: {
        trigger: 'axis'
      },

      grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
      }
    });

    $(window).on('resize', function () {
      chart.resize();
    });
  });
</script>
