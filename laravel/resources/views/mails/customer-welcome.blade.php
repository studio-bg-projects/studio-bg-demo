@extends('layouts.mail')

@section('content')
  <h2 style="color: #000; text-align: center;">
    {{ [
      'bg' => 'Здравейте, ' . $customer->firstName . ' '. $customer->lastName . '! Благодарим Ви, че се регистрирахте в нашата платформа.',
      'en' => 'Hello, ' . $customer->firstName . ' '. $customer->lastName . '! Thank you for registering on our platform.'
    ][$lang] }}
  </h2>

  <p>
    {{ [
      'bg' => 'Това е бизнес портал, предназначен за B2B клиенти.',
      'en' => 'This is a business portal intended for B2B clients.'
    ][$lang] }}
  </p>

  <p>
    {!! [
      'bg' => 'След <strong>преглед от наш оператор</strong>, профилът Ви ще бъде активиран. След това ще можете да виждате цените и да поръчвате директно през платформата.',
      'en' => 'Once your <strong>profile is reviewed by our operator</strong>, it will be activated. After that, you will be able to see prices and place orders directly through the platform.'
    ][$lang] !!}
  </p>

  <p>
    {{ [
      'bg' => 'При въпроси можете да се свържете с нас чрез контактната форма:',
      'en' => 'If you have any questions, feel free to reach out through our contact form:'
    ][$lang] }}

    {!! [
      'bg' => '<a href="https://insidetrading.bg/kontakti/">Контакти</a>',
      'en' => '<a href="https://insidetrading.bg/en/contact/">Contacts</a>'
    ][$lang] !!}
  </p>
@endsection
