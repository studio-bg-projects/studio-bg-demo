@extends('layouts.app')

@section('content')
  @include('erp.orders.partials.navbar')

  <h1 class="h4 mb-5">
    Поръчка #{{ $order->getOriginal('id') }}&mdash;
    <span style="color: {{ \App\Services\MapService::orderStatus($order->status)->color }}">
      {{ \App\Services\MapService::orderStatus($order->status)->labelBg }}
    </span>
  </h1>

  <hr class="my-3"/>

  @if (empty((array)$order->shopData))
    <div class="alert alert-outline-warning" role="alert">
      Очакваните данни, подадени от магазина (
      <code>$order->shopData</code>
      ), са празни. Без тях поръчката не може да бъде обслужена.
      <br/>
      Изчакайте последваща синхронизация или обновяване от магазина. Ако данните не бъдат доставени, моля, обърнете се към разработчика на системата.
    </div>
  @else
    @include('erp.orders.partials.process-flow', [
      'order' => $order
    ])

    <hr class="my-3"/>

    <div class="card mb-5">
      <div class="card-body pb-1">
        <h1 class="h4 card-title mb-4">Основна инфоамция</h1>

        <div class="d-flex">
          <p class="text-body fw-semibold">Дата на създаване:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $order->shopData->order->date_added }}</p>
        </div>
        <div class="d-flex">
          <p class="text-body fw-semibold">Статус:</p>
          <p class="text-body-emphasis fw-semibold ms-1">
            <span style="color: {{ \App\Services\MapService::orderStatus($order->status)->color }}">
              {{ \App\Services\MapService::orderStatus($order->status)->labelBg }}
            </span>
          </p>
        </div>
        <div class="d-flex">
          <p class="text-body fw-semibold">Валута - обменна стойност:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ (float)$order->shopData->order->currency_value }}</p>
        </div>
      </div>
      @if ($order->shopData->order->comment)
        <div class="card-footer text-secondary">
          <strong>Коментар:</strong>
          <i>{{ $order->shopData->order->comment }}</i>
        </div>
      @endif
    </div>

    <div class="card mb-5">
      <div class="card-body pb-1">
        <h1 class="h4 card-title mb-4">Въведен адрес за доставка</h1>

        <div class="d-flex">
          <p class="text-body fw-semibold">Име:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $order->shopData->order->shipping_firstname }}</p>
        </div>

        <div class="d-flex">
          <p class="text-body fw-semibold">Фамилия:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $order->shopData->order->shipping_lastname }}</p>
        </div>

        <div class="d-flex">
          <p class="text-body fw-semibold">Държава:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $order->shopData->order->shipping_country }}</p>
        </div>

        <div class="d-flex">
          <p class="text-body fw-semibold">Област/Регион:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $order->shopData->order->shipping_zone }}</p>
        </div>

        <div class="d-flex">
          <p class="text-body fw-semibold">Град:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $order->shopData->order->shipping_city }}</p>
        </div>

        <div class="d-flex">
          <p class="text-body fw-semibold">Пощенски код:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $order->shopData->order->shipping_postcode }}</p>
        </div>

        <div class="d-flex">
          <p class="text-body fw-semibold">Ул. №:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $order->shopData->shipping_address->street_no }}</p>
        </div>

        <div class="d-flex">
          <p class="text-body fw-semibold">Бл.:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $order->shopData->shipping_address->block_no }}</p>
        </div>

        <div class="d-flex">
          <p class="text-body fw-semibold">Вх.:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $order->shopData->shipping_address->entrance_no }}</p>
        </div>

        <div class="d-flex">
          <p class="text-body fw-semibold">Ет.:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $order->shopData->shipping_address->floor }}</p>
        </div>

        <div class="d-flex">
          <p class="text-body fw-semibold">Ап.:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $order->shopData->shipping_address->apartment_no }}</p>
        </div>

        <div class="d-flex">
          <p class="text-body fw-semibold">Адрес за доставка:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $order->shopData->order->shipping_address_1 }}</p>
        </div>

        <div class="d-flex">
          <p class="text-body fw-semibold">Уточнения към адреса за доставка:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $order->shopData->order->shipping_address_2 }}</p>
        </div>
      </div>
    </div>

    <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
      <h1 class="h5 mb-4">Продукти</h1>

      <div class="table-responsive">
        <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
          <thead>
          <tr class="bg-body-highlight">
            <th style="width: 5rem;" class="nosort border-top border-translucent"></th>
            <th class="nosort border-top border-translucent">
              MPN
            </th>
            <th class="nosort border-top border-translucent">
              EAN
            </th>
            <th class="nosort border-top border-translucent">
              Име
            </th>
            <th class="nosort border-top border-translucent text-end">
              Цена
            </th>
            <th class="nosort border-top border-translucent text-end">
              Брой
            </th>
            <th class="nosort border-top border-translucent text-end">
              Обща цена
            </th>
          </tr>
          </thead>
          <tbody>
          @foreach ($products as $product)
            <tr>
              <td>
                <div class="d-block border border-translucent rounded-2 table-preview">
                  <a href="{{ url('/erp/products/update/' . $product->product_id) }}">
                    <img src="{{ $product->image }}" alt="">
                  </a>
                </div>
              </td>
              <td>
                <a href="{{ url('/erp/products/update/' . $product->product_id) }}">
                  {{ $product->mpn }}
                </a>
              </td>
              <td>
                {{ $product->ean }}
              </td>
              <td>
                {{ $product->name }}
              </td>
              <td class="text-end">
                {{ price($product->price) }}
              </td>
              <td class="text-end">
                X {{ (float)$product->quantity }}
              </td>
              <td class="text-end">
                {{ price($product->total) }}
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>

      <div class="pt-5 mb-n5">
        @foreach ($order->shopData->order_total as $row)
          <p class="mb-4 pe-3 text-end h5">
            <span class="fw-normal me-1">{{ $row->title }}:</span>
            {{ price($row->value) }}
          </p>
        @endforeach
      </div>
    </div>

    <div class="card mb-5">
      <div class="card-body pb-1">
        <h1 class="h4 card-title mb-4">Информация за клиента</h1>

        @if ($customer)
          <a href="{{ url('/erp/customers/update/' . $customer->id) }}" target="_blank" class="btn btn-sm btn-phoenix-primary mb-3">
            <i class="fa-regular fa-square-arrow-up-right"></i>
            Преглед на клиента в ERP
          </a>

          <div class="d-flex">
            <p class="text-body fw-semibold">Имейл:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->email }}</p>
          </div>

          <div class="d-flex">
            <p class="text-body fw-semibold">Име:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->firstName }}</p>
          </div>

          <div class="d-flex">
            <p class="text-body fw-semibold">Фамилия:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->lastName }}</p>
          </div>

          <div class="d-flex">
            <p class="text-body fw-semibold">Група:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->group ? $customer->group->nameBg : '-' }}</p>
          </div>

          <div class="d-flex">
            <p class="text-body fw-semibold">Статус:</p>
            <p class="text-body-emphasis fw-semibold ms-1">
              {{ \App\Services\MapService::customerStatusType($customer->statusType)->label }}
            </p>
          </div>

          <hr class="mt-n2"/>

          <h1 class="h5 card-title mb-4">Данни за фирмата на клиента</h1>

          <div class="d-flex">
            <p class="text-body fw-semibold">Име на фирмата:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->companyName }}</p>
          </div>

          <div class="d-flex">
            <p class="text-body fw-semibold">Адрес по регистрация:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->companyAddress }}</p>
          </div>

          <div class="d-flex">
            <p class="text-body fw-semibold">ПК/ZIP:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->companyZipCode }}</p>
          </div>

          <div class="d-flex">
            <p class="text-body fw-semibold">Град:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->companyCity }}</p>
          </div>

          <div class="d-flex">
            <p class="text-body fw-semibold">Държава:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->companyCountry ? $customer->companyCountry->name : '-' }}</p>
          </div>

          <div class="d-flex">
            <p class="text-body fw-semibold">ЕИК:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->companyId }}</p>
          </div>

          <div class="d-flex">
            <p class="text-body fw-semibold">ДДС номер:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->companyVatNumber }}</p>
          </div>

          <hr class="mt-n2"/>

          <h1 class="h5 card-title mb-4">Инфоирмация за контакт</h1>

          <div class="d-flex">
            <p class="text-body fw-semibold">Търговски контакт:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->contactSales }}</p>
          </div>

          <div class="d-flex">
            <p class="text-body fw-semibold">Телефон:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->contactPhone }}</p>
          </div>

          <div class="d-flex">
            <p class="text-body fw-semibold">Имейл:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->contactEmail }}</p>
          </div>

          <hr class="mt-n2"/>

          <h1 class="h5 card-title mb-4">Финансов контакт</h1>

          <div class="d-flex">
            <p class="text-body fw-semibold">Телефон:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->financialContactPhone }}</p>
          </div>

          <div class="d-flex">
            <p class="text-body fw-semibold">Имейл:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->financialContactEmail }}</p>
          </div>

          <hr class="mt-n2"/>

          <h1 class="h5 card-title mb-4">Търговски представител</h1>

          <div class="d-flex">
            <p class="text-body fw-semibold">Търговски представител:</p>
            <p class="text-body-emphasis fw-semibold ms-1">{{ $customer->salesRepresentative ? $customer->salesRepresentative->nameBg : '-' }}</p>
          </div>
        @else
          <p>В базата данни на системата не открихме клиент с ID: #{{ $order->shopData->order->customer_id }}, който трябва да е свързан с поръчката.</p>
        @endif
      </div>
    </div>

    <div class="card mb-5">
      <div class="card-body pb-1">
        <h1 class="h4 card-title mb-4">История</h1>

        <div class="table-responsive">
          <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
            <thead>
            <tr class="bg-body-highlight">
              <th class="nosort border-top border-translucent">
                Действие
              </th>
              <th class="nosort border-top border-translucent">
                Под-действие
              </th>
              <th class="nosort border-top border-translucent">
                Изпълнител
              </th>
              <th class="nosort border-top border-translucent">
                Дата
              </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($order->events as $event)
              <tr>
                <td>
                  {{ $event->action }}
                </td>
                <td>
                  {{ $event->actionNote }}
                </td>
                <td>
                  {{ $event->actorType }}
                </td>
                <td>
                  {{ $event->createdAt }}
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @endif
@endsection
