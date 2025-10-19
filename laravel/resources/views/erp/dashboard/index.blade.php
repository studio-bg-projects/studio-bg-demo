@extends('layouts.app')

@section('content')
  <div class="row gy-6">
    <div class="col-12 col-xl-6 mb-3">
      @include('erp/dashboard/partials/pending-orders-list')
    </div>
    <div class="col-12 col-xl-6 mb-3">
      @include('erp/dashboard/partials/chart-orders')
    </div>
  </div>

  <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-y mb-5">
    <div class="row gy-6">
      <div class="col-12 col-xl-6 mb-3">
        @include('erp/dashboard/partials/pending-customers-list')
      </div>
      <div class="col-12 col-xl-6 mb-3">
        @include('erp/dashboard/partials/chart-customers')
      </div>
    </div>
  </div>

  <div class="row mb-5">
    <div class="col-12 mb-3">
      @include('erp/dashboard/partials/unpaid-documents')
    </div>
  </div>

  <div class="row mb-5">
    <div class="col-12 col-xl-6 mb-3">
      @include('erp/dashboard/partials/chart-categories-products')
    </div>
    <div class="col-12 col-xl-6 mb-3">
      @include('erp/dashboard/partials/chart-customers-groups')
    </div>
  </div>
@endsection
