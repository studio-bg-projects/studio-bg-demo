<div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-3 mb-5 mt-n5">
  <nav class="navbar p-0 m-0">
    <ul class="nav nav-underline fs-9">
      <li class="nav-item">
        <a href="{{ url('/erp/storage-report/products') }}" class="nav-link me-1 px-3 {{ Request::is('erp/storage-report/products') ? 'active' : '' }}">Продукти</a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/storage-report/nra') }}" class="nav-link me-1 px-3 {{ Request::is('erp/storage-report/nra') ? 'active' : '' }}">НАП</a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/storage-report/inventory') }}" class="nav-link me-1 px-3 {{ Request::is('erp/storage-report/inventory') ? 'active' : '' }}">Инвентар</a>
      </li>
    </ul>
  </nav>
</div>
