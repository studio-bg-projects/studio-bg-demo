@extends('layouts.mail')

@section('content')
  @if ($isNewOrder)
    <h2 style="color: #000; text-align: center;">
      {{ [
        'bg' => 'Благодарим Ви за Вашата поръчка!',
        'en' => 'Thank you for your order!'
      ][$lang] }}
    </h2>

    <p>
      {{ [
        'bg' => 'Вашата поръчка е получена и ще бъде обработена. Детайлите за нея са представени по-долу:',
        'en' => 'Your order has been received and will be processed. The details are presented below:'
      ][$lang] }}
    </p>
  @else
    <h2 style="color: #000; text-align: center;">
      {{ [
        'bg' => 'Информация за Вашата поръчка',
        'en' => 'Information about your order'
      ][$lang] }}
    </h2>

    <p>
      <strong>
        {{ [
          'bg' => 'Вашата поръчка е със статус:',
          'en' => 'Your order has status:'
        ][$lang] }}

        <span style="color: {{ \App\Services\MapService::orderStatus($order->status)->color }}">
          {{ [
          'bg' => \App\Services\MapService::orderStatus($order->status)->labelBg,
          'en' => \App\Services\MapService::orderStatus($order->status)->labelEn,
        ][$lang] }}
        </span>
      </strong>
    </p>
  @endif

  <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
    <tr>
      <th style="border: 1px solid #ddd; padding: 10px; background: #393185; color: #ffffff; text-align: left;">{{ ['bg' => 'Продукт', 'en' => 'Product'][$lang] }}</th>
      <th style="border: 1px solid #ddd; padding: 10px; background: #393185; color: #ffffff; text-align: right;">{{ ['bg' => 'Цена', 'en' => 'Price'][$lang] }}</th>
      <th style="border: 1px solid #ddd; padding: 10px; background: #393185; color: #ffffff; text-align: center;">{{ ['bg' => 'Брой', 'en' => 'Quantity'][$lang] }}</th>
      <th style="border: 1px solid #ddd; padding: 10px; background: #393185; color: #ffffff; text-align: right;">{{ ['bg' => 'Общо', 'en' => 'Total'][$lang] }}</th>
    </tr>
    </thead>
    <tbody>
    <tr>

    @foreach ($products as $product)
      <tr>
        <td style="border: 1px solid #ddd; padding: 10px; line-height: 25px;">
          {{ ['bg' => 'Продукт', 'en' => 'Product'][$lang] }}: {{ $product->name }}
          <br/>
          {{ ['bg' => 'MPN', 'en' => 'MPN'][$lang] }}: {{ $product->mpn }}
          <br/>
          {{ ['bg' => 'EAN', 'en' => 'EAN'][$lang] }}: {{ $product->ean }}
        </td>
        <td style="border: 1px solid #ddd; padding: 10px; text-align: right;">
          {{ price($product->price) }}
        </td>
        <td style="border: 1px solid #ddd; padding: 10px; text-align: right;">
          X {{ (float)$product->quantity }}
        </td>
        <td style="border: 1px solid #ddd; padding: 10px; text-align: right;">
          {{ price($product->total) }}
        </td>
      </tr>
    @endforeach
    </tbody>
    <tfoot>

    @foreach ($order->shopData->order_total as $row)
      <tr>
        <td colspan="3" style="border: 1px solid #ddd; padding: 10px; text-align: right;">{{ $row->title }}:</td>
        <td style="border: 1px solid #ddd; padding: 10px; text-align: right;">{{ price($row->value) }}</td>
      </tr>
    @endforeach
    </tfoot>
  </table>

  <p style="margin-top: 20px;">
    {{ [
      'bg' => 'За да видите Вашата поръчка, посетете този адрес:',
      'en' => 'To view your order, please visit this address:'
    ][$lang] }}

    <a href="{{ $shopLink }}" style="color: #007BFF; text-decoration: none;" target="_blank">{{ $shopLink }}</a>
  </p>

  @if ($order->customer && $order->customer->salesRepresentative)
    @include('mails.partials.sales-representative', [
      'salesRepresentative' => $order->customer->salesRepresentative,
    ])
  @endif
@endsection
