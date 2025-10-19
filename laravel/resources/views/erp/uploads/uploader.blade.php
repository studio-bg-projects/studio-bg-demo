@php($groupType = $groupType ?? 'default')

@php($groupId = $groupId ?? 'default')

@php($fieldName = $fieldName ?? 'uploadField')

@php($maxFiles = $maxFiles ?? null)

@php($acceptedFiles = $acceptedFiles ?? null)

@php($lockUpload = $lockUpload ?? false)

@php($labelCallback = $labelCallback ?? null)

@php($upId = Illuminate\Support\Str::random(10))

@if (!$lockUpload)
  <input type="hidden" name="{{ $fieldName }}" value="{{ $groupId }}">

  <div id="up-{{ $upId }}" class="dropzone">
    <div class="dz-message p-5 mb-3" data-dz-message>
      <i class="fa-regular fa-cloud-arrow-up fa-xl"></i>
      Качете вашите файлове тук
    </div>
  </div>
@endif

<ul id="up-preview-{{ $upId }}" class="m-n2 list-unstyled text-center pb-3" style="display: none;">
  <li class="dz-preview m-2">
    <a data-upload-link href="#!" class="dz-image">
      <img data-dz-thumbnail src="{{ asset('img/icons/file-placeholder.svg') }}" alt=""/>

      <div class="dz-details">
        <div class="dz-size">
          <span data-dz-size></span>
        </div>
        <div class="dz-filename">
          <span data-dz-name></span>
        </div>
      </div>
      <div class="dz-progress">
        <span class="dz-upload" data-dz-uploadprogress></span>
      </div>
      <div class="dz-error-message">
        <span data-dz-errormessage></span>
      </div>
    </a>

    @if (!$lockUpload)
      <div data-dz-remove class="dz-action dz-action-remove">
        <i class="fa-regular fa-trash-can"></i>
      </div>
      <div class="sortable-handle dz-action dz-action-sortable">
        <i class="fa-regular fa-arrows-maximize"></i>
      </div>
    @endif
  </li>
</ul>

<script type="module">
  const uploader = new Uploader({
    wrapper: '#up-{{ $upId }}',
    previewsContainer: '#up-preview-{{ $upId }}',
    serverUrl: @json(url('/erp/uploads/' . $groupType . '/' . $groupId)),
    maxFiles: @json($maxFiles ?? null),
    acceptedFiles: @json($acceptedFiles),
    labelCallback: @if ($labelCallback) {{ $labelCallback }} @else null @endif,
  });

  uploader.loadFiles();

  @if (!$lockUpload)
  uploader.createDropZone();
  uploader.createSortable();
  @endif
</script>
