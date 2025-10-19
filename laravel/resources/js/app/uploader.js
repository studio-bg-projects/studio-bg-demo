import GLightbox from 'glightbox';

export class Uploader {
  constructor(settings) {
    this.$wrapper = $(settings.wrapper);
    this.$previewsContainer = $(settings.previewsContainer);
    this.serverUrl = settings.serverUrl;
    this.maxFiles = settings.maxFiles;
    this.acceptedFiles = settings.acceptedFiles;
    this.labelCallback = settings.labelCallback;

    this.leftFiles = this.maxFiles || 9999;
    this.template = this.$previewsContainer.html();
    this.$form = this.$wrapper.closest('form');
    this.dropzone = null;
    this.gLightbox = null;

    this.$previewsContainer.html('').show();
  }

  createDropZone() {
    const that = this;
    const $buttons = that.$form.find('button, [type="submit"], [type="button"]');

    this.gLightbox = GLightbox({
      selector: '#' + this.$previewsContainer.attr('id') + ' [data-gallery]'
    });

    that.dropzone = new Dropzone(that.$wrapper.get(0), {
      url: `${this.serverUrl}/upload`,
      method: 'post',
      paramName: 'up',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      previewsContainer: this.$previewsContainer.get(0),
      previewTemplate: this.template,
      maxFiles: this.maxFiles,
      acceptedFiles: this.acceptedFiles,
      thumbnailWidth: 800,
      thumbnailHeight: 800,
      thumbnailMethod: 'contain',

      init: function () {
        const errorHandler = (file, response) => {
          console.error('Error', response);
          const msg = response?.message && response.message !== '' ? response.message : (response.exception || typeof response === 'string' ? response : '');

          const $errorMsg = $(file.previewElement).find('[data-dz-errormessage]');
          $errorMsg.html(`An upload error occurred${msg ? `: ${msg}` : ''}!`);

          const $errorWrapper = $errorMsg.parent();
          $errorWrapper.fadeIn();
        };

        // Start uploading
        this.on('addedfile', () => {
          that.leftFiles--;
          that.lockZoneCheck();

          $buttons.attr('disabled', true);
        });

        // Uploading complete
        this.on('queuecomplete', () => {
          $buttons.attr('disabled', false);
        });

        // Upload error
        this.on('error', errorHandler);

        // Successful uploading
        this.on('success', (file, response) => {
          if (!response?.file?.id) {
            errorHandler(file, response);
            return;
          }

          file.response = response;

          const $container = $(file.previewElement);
          $container.attr('data-upload-id', response.file.id);

          const $link = $container.find('[data-upload-link]');
          $link.attr('target', '_blank');
          $link.attr('href', response.file.urls.main);

          if (response.file.mimeType.startsWith('image/')) {
            $container.find('[data-dz-thumbnail]').attr('src', response.file.urls.preview);
          }

          that.sortSerialize();
          that.runLabelCallback();
        });

        // Remove file
        this.on('removedfile', (file) => {
          if (file?.response?.file?.id) {
            that.removeFile(file?.response?.file?.id);
          }
        });
      }
    });
  }

  createSortable() {
    new Sortable(this.$previewsContainer.get(0), {
      animation: 150,
      handle: '.sortable-handle',
      ghostClass: 'sortable-handle-ghost',
      delayOnTouchOnly: true, // Useful for mobile touch
      forceFallback: true, // Ignore the HTML5 DnD behaviour
      onEnd: () => {
        this.sortSerialize();
        this.runLabelCallback();
      }
    });
  }

  async removeFile(fileId) {
    await $.ajax({
      url: `${this.serverUrl}/remove/${fileId}`,
      type: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    this.leftFiles++;
    this.lockZoneCheck();
    this.sortSerialize();
    this.runLabelCallback();
  }

  async loadFiles() {
    const files = await $.ajax({
      url: `${this.serverUrl}`
    });

    this.leftFiles -= files.length;
    this.lockZoneCheck();

    files.forEach(data => {
      const $container = $(this.template);

      $container.attr('data-upload-id', data.id);

      $container.addClass('dz-processing');
      $container.addClass('dz-success');
      $container.addClass('dz-complete');

      $container.find('[data-dz-name]').html(data.originalName);
      $container.find('[data-dz-size]').html((data.size / (1024 * 1024)).toFixed(2) + ' MB');

      const $link = $container.find('[data-upload-link]');
      $link.attr('target', '_blank');
      $link.attr('href', data.urls.main);

      $container.find('[data-dz-remove]').on('click', () => {
        this.removeFile(data.id);
        $container.remove();
      });

      this.$previewsContainer.append($container);

      if (data.mimeType.startsWith('image/')) {
        $container.find('[data-dz-thumbnail]').attr('src', data.urls.preview);

        $link.attr('data-gallery', this.$wrapper.attr('id'));

        if (this.gLightbox) {
          this.gLightbox.init();
        }
      }
    });

    this.runLabelCallback();
  }

  lockZoneCheck() {
    if (this.leftFiles <= 0) {
      this.$wrapper.slideUp();
    } else {
      this.$wrapper.slideDown();
    }
  }

  async runLabelCallback() {
    if (this.labelCallback) {
      const that = this;
      let i = 0;
      this.$previewsContainer.find('[data-upload-id]').map(function () {
        const html = that.labelCallback(i++, this);
        const $wrapper = $('<div class="seq">').append(html);
        $(this).find('.seq').remove();
        $(this).append($wrapper);
      }).toArray();
    }
  }

  async sortSerialize() {
    const ids = this.$previewsContainer.find('[data-upload-id]').map(function () {
      return $(this).data('upload-id');
    }).toArray();

    if (!ids.length) {
      return;
    }

    await $.ajax({
      url: `${this.serverUrl}/sort/`,
      type: 'POST',
      data: {
        ids
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
  }
}
