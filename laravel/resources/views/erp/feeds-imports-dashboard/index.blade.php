@extends('layouts.app')

@section('content')
  <div class="row gy-6 mb-3">
    <div class="col-12 col-xl-6 mb-3">
      @include('erp/feeds-imports-dashboard/partials/unlinked-count')
    </div>
    <div class="col-12 col-xl-6 mb-3">
      @include('erp/feeds-imports-dashboard/partials/conflicts-count')
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-12 mb-3">
      @include('erp.feeds-imports-dashboard.partials.feeds')
    </div>
  </div>

  <div class="row gy-6 mb-3">
    <div class="col-12 col-xl-12 mb-3">
      @include('erp.feeds-imports-dashboard.partials.promo-items')
    </div>
  </div>
@endsection
