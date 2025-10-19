@if(session('success'))
  <div class="toast-container position-fixed bottom-0 end-0 p-3 opacity-75">
    <div data-app-success-toast class="toast align-items-center bg-success-subtle border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          {{ session('success') }}
        </div>
        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>
@endif

@if($errors->any())
  <div class="toast-container position-fixed bottom-0 end-0 p-3 opacity-75">
    <div data-app-error-toast class="toast align-items-center bg-danger-subtle border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>
@endif
