@extends('layouts.mail')

@section('content')
  <h2 style="color: #000; text-align: center;">
    {{ [
      'bg' => 'Здравейте, ' . $customer->firstName . ' '. $customer->lastName . '!',
      'en' => 'Hello, ' . $customer->firstName . ' '. $customer->lastName . '!'
    ][$lang] }}
  </h2>

  <p>
    {!! [
      'bg' => 'Поздравления! Вашият профил беше одобрен.',
      'en' => 'Congratulations! Your account has been approved.'
    ][$lang] !!}
  </p>

  <p>
    {!! [
      'bg' => 'Вече можете да влезете в акаунта си с имейла и паролата, които сте посочили при регистрацията, на адрес:',
      'en' => 'You can now log in using the email and password you provided during registration at:'
    ][$lang] !!}

    {!! [
      'bg' => '<a href="https://shop.insidetrading.bg/">shop.insidetrading.bg</a>',
      'en' => '<a href="https://shop.insidetrading.bg/">shop.insidetrading.bg</a>'
    ][$lang] !!}
  </p>
@endsection
