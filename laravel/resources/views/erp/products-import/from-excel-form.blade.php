@extends('layouts.app')

@section('content')
  @include('erp.products-import.partials.navbar')

  <h1 class="h4 mb-5">Импортиране на продукти от Excel</h1>

  <form method="post" action="?" class="mb-5" enctype="multipart/form-data" data-disable-on-submit>
    @csrf
    <div class="mb-3">
      <label for="f-importFile" class="form-label required">Качване на excel файл</label>
      <input class="form-control" type="file" name="importFile" id="f-importFile" accept=".csv, .xls, .xlsx" required/>
    </div>

    <div class="text-end">
      <button class="btn btn-primary btn-lg" type="submit">
        <i class="fa-regular fa-cloud-arrow-up"></i>
        Качи
      </button>
    </div>

    <div class="alert alert-subtle-info my-5" role="alert">Моля, не редактирайте и не маркирайте клетки след последния продукт в Excel файла. Това може да доведе до проблеми с импорта, тъй като ще се обработят и празните редове. Оставяйте само реалните данни!</div>
  </form>
@endsection
