<div class="card h-100">
  <div class="card-header border-bottom-0 pb-0 d-flex flex-row">
    <h3 class="text-body-emphasis">Клиентски групи</h3>

    <a href="{{ url('/erp/customers-groups') }}" class="ms-auto btn btn-phoenix-secondary">
      <i class="fa-regular fa-poll-people"></i>
      Всички клиентски групи
    </a>
  </div>
  <div class="card-body py-0">
    <div id="chart-customers-groups" style="width: 100%; height: 400px;"></div>
  </div>
</div>

<script type="module">
  $(function () {
    const erpData = @json($chartCustomersGroups);
    const chartData = erpData.map(item => ({name: item.name, value: item.count}));

    const $chartDom = $('#chart-customers-groups');

    if (!$chartDom.length) {
      return;
    }

    const chart = echarts.init($chartDom.get(0));

    chart.setOption({
      responsive: true,
      maintainAspectRatio: false,

      series: [
        {
          // name: 'Брой продукти',
          type: 'pie',
          radius: ['60%', '90%'],
          startAngle: 30,
          avoidLabelOverlap: false,
          center: ['75%', '50%'], // Поставя диаграмата на 75% от ширината и 50% от височината

          label: {
            show: false,
            position: 'center',
            formatter: '{x|{d}%} \n {y|{b}}',
            rich: {
              x: {
                fontSize: 31.25,
                fontWeight: 800,
                padding: [0, 0, 5, 15]
              },
              y: {
                fontSize: 12.8,
                fontWeight: 600
              }
            }
          },
          emphasis: {
            label: {
              show: true
            }
          },
          labelLine: {
            show: false
          },
          data: chartData,
        }
      ],
      grid: {
        bottom: 0,
        top: 0,
        left: 0,
        right: 0,
        containLabel: false
      },
      // title: {
      //   text: 'Продукти по категории',
      //   left: 'center'
      // },
      tooltip: {
        trigger: 'item'
      },
      legend: {
        orient: 'vertical',
        left: 'left',
        top: 'center'
      }
    });

    $(window).on('resize', function () {
      chart.resize();
    });
  });
</script>
