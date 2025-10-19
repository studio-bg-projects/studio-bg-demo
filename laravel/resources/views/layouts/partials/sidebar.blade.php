<div class="navbar-vertical-content">
  <ul class="navbar-nav flex-column">
    <li class="nav-item">
      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/dashboard') || Request::is('erp/dashboard/*') ? 'active' : '' }}" href="{{ url('/erp/dashboard') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-chart-line"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Начало</span>
            </span>
          </div>
        </a>
      </div>
    </li>
    <li class="nav-item">
      <p class="navbar-vertical-label">Магазин</p>
      <hr class="navbar-vertical-line">

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/products') || Request::is('erp/products/*') ? 'active' : '' }}" href="{{ url('/erp/products') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-box"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Продукти</span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/categories') || Request::is('erp/categories/*') ? 'active' : '' }}" href="{{ url('/erp/categories') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-layer-group"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Категории</span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/manufacturers') || Request::is('erp/manufacturers/*') ? 'active' : '' }}" href="{{ url('/erp/manufacturers') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-industry"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Производители</span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/specifications') || Request::is('erp/specifications/*') ? 'active' : '' }}" href="{{ url('/erp/specifications') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-circle-nodes"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Спецификации</span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/products-import') || Request::is('erp/products-import/*') ? 'active' : '' }}" href="{{ url('/erp/products-import') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-file-import"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">
                Импорт на продукти
              </span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/data-sources/products') || Request::is('erp/data-sources/products/*') ? 'active' : '' }}" href="{{ url('/erp/data-sources/products') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-sparkles"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">
                Извличане на данни
              </span>
            </span>
          </div>
        </a>
      </div>
    </li>
    <li class="nav-item">
      <p class="navbar-vertical-label">Склад</p>
      <hr class="navbar-vertical-line">

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/storage-entries') || Request::is('erp/storage-entries/*') ? 'active' : '' }}" href="{{ url('/erp/storage-entries') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-inbox"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Заприхождаване</span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/storage-items') || Request::is('erp/storage-items/*') ? 'active' : '' }}" href="{{ url('/erp/storage-items') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-boxes-stacked"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Артикули</span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/storage-report') || Request::is('erp/storage-report/*') ? 'active' : '' }}" href="{{ url('/erp/storage-report') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-chart-bar"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">
                Складови справки
              </span>
            </span>
          </div>
        </a>
      </div>
    </li>
    <li class="nav-item">
      <p class="navbar-vertical-label">Поръчки</p>
      <hr class="navbar-vertical-line">

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/orders') || Request::is('erp/orders/*') ? 'active' : '' }}" href="{{ url('/erp/orders') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-cart-shopping"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">
                Поръчки
                @if ($waitingOrders)
                  <span class="badge rounded-pill text-bg-danger">
                    {{ $waitingOrders }}
                  </span>
                @endif
              </span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/shipments/speedy') || Request::is('erp/shipments/speedy/*') ? 'active' : '' }}" href="{{ url('/erp/shipments/speedy') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-truck"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">
                Пратки (DPD/Speedy)
              </span>
            </span>
          </div>
        </a>
      </div>
    </li>
    <li class="nav-item">
      <p class="navbar-vertical-label">Документооборот</p>
      <hr class="navbar-vertical-line">

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/documents') || Request::is('erp/documents/*') ? 'active' : '' }}" href="{{ url('/erp/documents') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-file-lines"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">
                Фактури и документи
                @if ($unpaidDocuments)
                  <span class="badge rounded-pill text-bg-danger">
                    {{ $unpaidDocuments }}
                  </span>
                @endif
              </span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/incomes') || Request::is('erp/incomes/*') ? 'active' : '' }}" href="{{ url('/erp/incomes') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-money-bill-transfer"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Плащания</span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/offers') || Request::is('erp/offers/*') ? 'active' : '' }}" href="{{ url('/erp/offers') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-envelope-open-dollar"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Оферти</span>
            </span>
          </div>
        </a>
      </div>
    </li>
    <li class="nav-item">
      <p class="navbar-vertical-label">Клиенти и доставчици</p>
      <hr class="navbar-vertical-line">

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/customers') || Request::is('erp/customers/*') ? 'active' : '' }}" href="{{ url('/erp/customers') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-person"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">
                Клиенти и доставчици
                @if ($waitingCustomers || $waitingCustomersCreditLine)
                  <span class="badge rounded-pill text-bg-danger">
                    {{ implode('/', [$waitingCustomers, $waitingCustomersCreditLine]) }}
                  </span>
                @endif
              </span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/customers-groups') || Request::is('erp/customers-groups/*') ? 'active' : '' }}" href="{{ url('/erp/customers-groups') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-poll-people"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">
                Клиентски групи
              </span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/sales-representatives') || Request::is('erp/sales-representatives/*') ? 'active' : '' }}" href="{{ url('/erp/sales-representatives') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-user-headset"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Търговски представители</span>
            </span>
          </div>
        </a>
      </div>
    </li>
    <li class="nav-item">
      <p class="navbar-vertical-label">Feeds Import</p>
      <hr class="navbar-vertical-line">

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/feeds-imports-dashboard') || Request::is('erp/feeds-imports-dashboard/*') ? 'active' : '' }}" href="{{ url('/erp/feeds-imports-dashboard') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-chart-simple"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Дашборд</span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/feeds-imports') || Request::is('erp/feeds-imports/*') ? 'active' : '' }}" href="{{ url('/erp/feeds-imports') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-rss"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Feeds</span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/feeds-imports-items/related') || Request::is('erp/feeds-imports-items/related/*') ? 'active' : '' }}" href="{{ url('/erp/feeds-imports-items/related') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-link"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">
                Свързване
                @if ($unlinkedItemsCount)
                  <span class="badge rounded-pill text-bg-danger">
                    {{ $unlinkedItemsCount }}
                  </span>
                @endif
              </span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/feeds-imports-items/conflicts') || Request::is('erp/feeds-imports-items/conflicts/*') ? 'active' : '' }}" href="{{ url('/erp/feeds-imports-items/conflicts') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-triangle-exclamation"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">
                Конфликти
                @if ($conflictsItemsCount)
                  <span class="badge rounded-pill text-bg-danger">
                    {{ $conflictsItemsCount }}
                  </span>
                @endif
              </span>
            </span>
          </div>
        </a>
      </div>
    </li>
    <li class="nav-item">
      <p class="navbar-vertical-label">Други</p>
      <hr class="navbar-vertical-line">

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/search-report') || Request::is('erp/search-report/*') ? 'active' : '' }}" href="{{ url('/erp/search-report') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-magnifying-glass-waveform"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">
                Търсения от клиенти
              </span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/banners') || Request::is('erp/banners/*') ? 'active' : '' }}" href="{{ url('/erp/banners') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-shapes"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">
                Банери
              </span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/demos') || Request::is('erp/demos/*') ? 'active' : '' }}" href="{{ url('/erp/demos') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-sparkle"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Демо</span>
            </span>
          </div>
        </a>
      </div>
    </li>
    <li class="nav-item">
      <p class="navbar-vertical-label">Административни</p>
      <hr class="navbar-vertical-line">

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/users') || Request::is('erp/users/*') ? 'active' : '' }}" href="{{ url('/erp/users') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-user-crown"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Потребители</span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/schedulers') || Request::is('erp/schedulers/*') ? 'active' : '' }}" href="{{ url('/erp/schedulers') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-clock"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Сървърни задачи</span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/mails') || Request::is('erp/mails/*') ? 'active' : '' }}" href="{{ url('/erp/mails') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-envelope"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Имейли</span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/config') || Request::is('erp/config/*') ? 'active' : '' }}" href="{{ url('/erp/config') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-gear"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Конфигурация</span>
            </span>
          </div>
        </a>
      </div>

      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('erp/api-keys') || Request::is('erp/api-keys/*') ? 'active' : '' }}" href="{{ url('/erp/api-keys') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-plug"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Външни API интеграции</span>
            </span>
          </div>
        </a>
      </div>
    </li>
  </ul>
</div>
