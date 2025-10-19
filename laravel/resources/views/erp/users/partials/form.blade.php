<div class="row">
  <div class="col-12 col-xl-4 pb-3 pb-lg-0">
    <h2 class="h4 card-title mt-4 mb-2">Основна информация</h2>
    <p class="text-body-secondary mb-0 fs-8">Въведете името и имейл адреса на потребителя. Тази информация ще се използва за идентифициране и комуникация с потребителя в системата.</p>
  </div>
  <div class="col-12 col-xl-8">
    <div class="card">
      <div class="card-body pb-1">
        <h2 class="h4 card-title mb-4">Основна информация за потребителя</h2>

        <div class="mb-4">
          <label class="app-form-label required" for="f-email">Имейл</label>
          <input type="email" class="form-control @if($errors->has('email')) is-invalid @endif" id="f-email" name="email" value="{{ $user->email }}" placeholder="email@insidetrading.bg" required/>
          @if($errors->has('email'))
            <div class="invalid-feedback">
              {{ $errors->first('email') }}
            </div>
          @endif
        </div>

        <div class="mb-4">
          <label class="app-form-label required" for="f-fullName">Име и Фамилия</label>
          <input type="text" class="form-control @if($errors->has('fullName')) is-invalid @endif" id="f-fullName" name="fullName" value="{{ $user->fullName }}" placeholder="Иван Иванов..." required/>
          @if($errors->has('fullName'))
            <div class="invalid-feedback">
              {{ $errors->first('fullName') }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<hr class="my-3"/>

<div class="row">
  <div class="col-12 col-xl-4 pb-3 pb-lg-0">
    <h2 class="h4 card-title mt-4 mb-2">Парола на потребителя</h2>
    <p class="text-body-secondary mb-0 fs-8">
      @if (Request::is('erp/users/update/*'))
        Въведете нова парола, ако искате да смените текущата. Ако полето остане празно, паролата на потребителя няма да бъде променена.
      @else
        Въведете паролата за новия потребител.
      @endif
    </p>
  </div>
  <div class="col-12 col-xl-8">
    <div class="card">
      <div class="card-body pb-1">
        <h2 class="h4 card-title mb-4">
          @if (Request::is('erp/users/update/*'))
            Промяна на паролата на потребителя
          @else
            Парола на потребителя
          @endif
        </h2>

        <div class="mb-4 text-start">
          <label class="app-form-label required" for="f-password">Парола</label>
          <div class="position-relative" data-app-password>
            <input class="form-control form-icon-input pe-6 @if($errors->has('password')) is-invalid @endif" id="f-password" type="password" name="password" value="{{ $user->password }}" placeholder="Парола" data-app-password-input required/>
            @if($errors->has('password'))
              <div class="invalid-feedback">
                {{ $errors->first('password') }}
              </div>
            @endif
            <div class="btn px-3 py-0 position-absolute top-0 end-0 fs-7 text-body-tertiary" data-app-password-toggle>
              <i class="fa-regular fa-eye show"></i>
              <i class="fa-regular fa-eye-slash hide"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<hr class="my-3"/>

<div class="row">
  <div class="col-12 col-xl-4 pb-3 pb-lg-0">
    <h2 class="h4 card-title mt-4 mb-2">Права и достъп</h2>
    <p class="text-body-secondary mb-0 fs-8">Изберете какви права ще има потребителя. Ако маркирате, че е администратор, той ще има права до всичко в системата.</p>
  </div>
  <div class="col-12 col-xl-8">
    <div class="card">
      <div class="card-body pb-1">
        <h2 class="h4 card-title mb-4">Права и достъп на потребителя </h2>

        <div class="mb-4">
          <h5 class="app-form-label">Супер администратор</h5>

          <div class="form-check form-switch">
            <input type="hidden" name="isAdmin" value="0"/>
            <input class="form-check-input @if($errors->has('isAdmin')) is-invalid @endif" id="f-isAdmin" name="isAdmin" value="1" type="checkbox" @if ($user->isAdmin) checked @endif />
            <label class="form-check-label" for="f-isAdmin">Да</label>
            @if($errors->has('isAdmin'))
              <div class="invalid-feedback">
                {{ $errors->first('isAdmin') }}
              </div>
            @endif
          </div>
        </div>

        <div class="mb-4">
          <h5 class="app-form-label">Списък с права</h5>

          @foreach(\App\Services\PermissionsService::getAllPermission() as $id => $title)
            <div class="form-check form-switch">
              <input class="form-check-input @if($errors->has('permissions.' . $id)) is-invalid @endif" id="f-permissions-{{ $id }}" name="permissions[{{ $id }}]" value="{{ $id }}" type="checkbox" @if (in_array($id, $user->permissions)) checked @endif />
              <label class="form-check-label" for="f-permissions-{{ $id }}">{{ $title }}</label>
              @if($errors->has('permissions.' . $id))
                <div class="invalid-feedback">
                  {{ $errors->first('permissions.' . $id) }}
                </div>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
