@extends('layouts.mail')

@section('content')
  <h2 style="color: #000; text-align: center;">
    {{ [
      'bg' => 'Информация за вашата кредитна линия.',
      'en' => 'Information about your credit line'
    ][$lang] }}
  </h2>

  <p>
    {{ [
      'bg' => 'Здравейте, ' . $customer->firstName . ' '. $customer->lastName . '!',
      'en' => 'Hello, ' . $customer->firstName . ' '. $customer->lastName . '!',
    ][$lang] }}
  </p>

  <p>
    {{ [
      'bg' => 'Изпращаме ви параметрите на вашата кредитна линия.',
      'en' => 'We are sending you the details of your credit line'
    ][$lang] }}
  </p>

  <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
    <tr>
      <th style="width: 33%; border: 1px solid #ddd; padding: 20px; background: #393185; color: #ffffff;">
        {{ ['bg' => 'Кредитна линия', 'en' => 'Credit line'][$lang] }}
      </th>
      <th style="width: 33%; border: 1px solid #ddd; padding: 20px; background: #393185; color: #ffffff;">
        {{ ['bg' => 'Остатъчна сума', 'en' => 'Remaining amount'][$lang] }}
      </th>
      <th style="width: 33%; border: 1px solid #ddd; padding: 20px; background: #393185; color: #ffffff;">
        {{ ['bg' => 'Използвана сума', 'en' => 'Used amount'][$lang] }}
      </th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <tr>
      <td style="text-align: center; font-weight: bold; border: 1px solid #ddd; padding: 20px;">
        {{ price($customer->creditLineValue) }}
      </td>
      <td style="text-align: center; font-weight: bold; border: 1px solid #ddd; padding: 20px;">
        {{ price($customer->creditLineUsed) }}
      </td>
      <td style="text-align: center; font-weight: bold; border: 1px solid #ddd; padding: 20px;">
        {{ price($customer->creditLineLeft) }}
      </td>
    </tr>
    </tbody>
  </table>

  @if ($customer->salesRepresentative)
    @include('mails.partials.sales-representative', [
      'salesRepresentative' => $customer->salesRepresentative,
    ])
  @endif
@endsection
