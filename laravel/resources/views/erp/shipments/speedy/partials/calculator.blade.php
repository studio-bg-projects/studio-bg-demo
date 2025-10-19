<button type="button" class="btn btn-sm btn-subtle-primary text-primary" id="js-calcShippingButton">
  <i class="fa-regular fa-calculator me-1"></i>
  Изчисли цената за доставка
</button>

<div style="display: none;" id="js-calculate-error" class="alert alert-subtle-danger mt-3" role="alert">
  <ul class="mb-0"></ul>
</div>

<div style="display: none;" id="js-calculate-info" class="alert alert-subtle-info mt-3"></div>

<script type="module">
  $(function () {
    function calculateShipping() {
      $.ajax({
        url: @json(url('/erp/shipments/speedy/calculate')),
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          products: window.calculateShippingGetProducts(),
          address: window.calculateShippingGetAddress(),
        },
        dataType: 'json',
        success: (rs) => {
          window.calculateShippingHandleResponse(rs);
          const $error = $('#js-calculate-error');
          const $errorUl = $error.find('ul');
          const $info = $('#js-calculate-info');

          $error.hide();
          $info.hide();

          const translateMap = {
            netAmount: 'Нетна сума',
            addressPickupSurcharge: 'Надб. за вземане от адрес',
            addressDeliverySurcharge: 'Надб. за доставка до адрес',
            fixedDiscount: 'Фиксирана отстъпка',
            dropOffDiscount: 'Отстъпка за предаване в офис',
            pickUpDiscount: 'Отстъпка за вземане от офис',
            additionalDiscount: 'Допълнителна отстъпка',
            fuelSurcharge: 'Такса гориво',
            nonStandardDeliveryDateSurcharge: 'Надб. "Събота/празник"',
            administrativeFee: 'ТРО (такса "разходи обработка")',
            loadUnload: 'Такса товарене/разтоварване',
            islandSurcharge: 'Надб. за островна дестинация',
            optionsBeforePaymentSurcharge: 'Надб. за опции ОПП/ТПП',
            codPremium: 'Такса "Наложен платеж"',
            heavyParcelSurcharge: 'Такса тежък пакет',
            addressNormalizationSurcharge: 'Такса за нормализиране на адрес',
            tollSurcharge: 'Тол такса',
            additionalPayZoneSurcharge: 'Надб. за отдалечена зона',
            insurancePremium: 'Обявена стойност',
            voucherDiscount: 'Отстъпка с ваучер',
          };

          let hasError = false;
          $errorUl.html('');
          $info.html('');

          if (Object.values(rs?.errors).length > 0) {
            hasError = true;
            $error.fadeIn();

            Object.values(rs.errors).forEach(error => {
              $errorUl.append(`<li>${error[0]}</li>`);
            });
          }

          rs?.calculation?.calculations?.forEach(item => {
            if (item.error) {
              hasError = true;
              $error.fadeIn();

              $errorUl.append(`<li>${item?.error?.message}</li>`);
            }
          });

          if (!hasError && rs?.calculation?.calculations) {
            $info.fadeIn();

            let html = '';
            rs.calculation.calculations.forEach(item => {
              html += `<p class="mb-1">Услуга: ${item.serviceId}</p>`;
              html += `<p class="mb-1">Дата на взимане: ${item.pickupDate}</p>`;
              html += `<p class="mb-1">Най-късна доставка: ${item.deliveryDeadline}</p>`;
              html += `<p class="mb-1">Валута: ${item.price.currency}</p>`;

              html += '<table class="table table-sm">';
              html += '<tr><th>Услуга</th><th>Стойност</th></tr>';
              Object.entries(item.price.details).forEach(([key, value]) => {
                if (value.amount > 0) {
                  let title = translateMap[key] ?? key;
                  html += `<tr><th><span title="${key}">${title}</span></th><td>${value.amount}</td></tr>`;
                }
              });
              html += `<tr><th>Облагаема стойност</th><td>${item.price.amount}</td></tr>`;
              html += `<tr><th>ДДС</th><td>${item.price.vat}</td></tr>`;
              html += `<tr><th>Всичко</th><td>${item.price.total}</td></tr>`;
              html += '</table>';
            });

            $info.html(html);
          }
        }
      });
    }

    $('#js-calcShippingButton').click(calculateShipping);
  });
</script>
