@once
  <div class="modal fade" id="storageItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="storageItemModalLabel">Преглед на артикул</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Затвори"></button>
        </div>
        <div class="modal-body" id="storageItemModalBody"></div>
      </div>
    </div>
  </div>

  <script type="module">
    $(function () {
      const $storageItemModalLabel = $('#storageItemModalLabel');
      const $storageItemModalBody = $('#storageItemModalBody');

      function setModalLoading() {
        if (!$storageItemModalBody.length) {
          return;
        }

        $storageItemModalBody.html('<div class="d-flex align-items-center text-body-secondary fs-9"><span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Зареждане...</div>');
      }

      $(document).on('click', '[data-item-quick-view]', function () {
        const id = $(this).data('itemQuickView');

        if (!id || !$storageItemModalBody.length || !$storageItemModalLabel.length) {
          return;
        }

        $storageItemModalLabel.text(`Артикул #${id}`);
        setModalLoading();

        $.ajax({
          url: `/erp/storage-items/view/${id}`,
          method: 'GET',
          data: {
            emptyLayout: true,
            returnHtml: true
          }
        })
          .done(html => {
            $storageItemModalBody.html(html);
            $storageItemModalBody.find('.widgets-scrollspy-nav').remove();
          })
          .fail(() => {
            $storageItemModalBody.html('<span class="text-danger fs-9">Възникна грешка при зареждането.</span>');
          });
      });
    });
  </script>
@endonce
