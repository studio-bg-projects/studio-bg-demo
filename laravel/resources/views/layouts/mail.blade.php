<!DOCTYPE html>
<html lang="{{ $lang || str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8"/>
  <title>
    @if (!empty($pageTitle))
      {{ $pageTitle }} &mdash;
    @endif
    {{ env('APP_NAME') }}
  </title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 20px;">

<div style="max-width: 800px; margin: auto; border: 1px solid #ddd; padding: 20px; background: #f9f9f9;">
  <div style="text-align: center">
    <a href="{{ env('SHOP_URL') }}" target="_blank">
      <img src="data:image/svg+xml;base64,{{base64_encode(file_get_contents(public_path('img/logo.svg')))}}" alt="Inside Trading" style="height: 3rem;"/>
    </a>
  </div>

  @if (!empty($content))
    {!! $content !!}
  @endif

  @yield('content')

  <hr style="border-top: 1px solid black; margin: 30px 0;"/>

  <p style="text-align: center; margin-bottom: 20px;">
    {{ [
      'bg' => 'При въпроси можете да се свържете с нас чрез контактната форма:',
      'en' => 'If you have any questions, feel free to reach out through our contact form:'
    ][$lang] }}

    {!! [
      'bg' => '<a href="https://insidetrading.bg/kontakti/" target="blank">Контакти</a>',
      'en' => '<a href="https://insidetrading.bg/en/contact/" target="blank">Contacts</a>'
    ][$lang] !!}
  </p>
  <table style="width: 100%; border-collapse: collapse;">
    <tr>
      <!-- Телефон -->
      <td style="width: 33%; text-align: center; padding: 10px;">
        <a href="tel:+359885915515" style="text-decoration: none; color: #393185;">
          <div style="width: 50px; height: 50px; margin: 0 auto; background-color: #ef6b03; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
            <svg aria-hidden="true" viewBox="0 0 512 512" width="24" height="24" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
              <path d="M497.39 361.8l-112-48a24 24 0 0 0-28 6.9l-49.6 60.6A370.66 370.66 0 0 1 130.6 204.11l60.6-49.6a23.94 23.94 0 0 0 6.9-28l-48-112A24.16 24.16 0 0 0 122.6.61l-104 24A24 24 0 0 0 0 48c0 256.5 207.9 464 464 464a24 24 0 0 0 23.4-18.6l24-104a24.29 24.29 0 0 0-14.01-27.6z"></path>
            </svg>
          </div>
          <div style="margin-top: 8px; font-weight: bold; color: #393185;">
            +359 885 915515
          </div>
        </a>
      </td>

      <!-- Email -->
      <td style="width: 33%; text-align: center; padding: 10px;">
        <a href="mailto:info@insidetrading.bg" style="text-decoration: none; color: #393185;">
          <div style="width: 50px; height: 50px; margin: 0 auto; background-color: #ef6b03; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
            <svg aria-hidden="true" viewBox="0 0 512 512" width="24" height="24" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
              <path d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 54.955 42.717.231 80.509-37.199 103.053-54.947 49.528-38.783 82.032-64.401 104.947-82.653V400H48z"></path>
            </svg>
          </div>
          <div style="margin-top: 8px; font-weight: bold; color: #393185;">
            info@insidetrading.bg
          </div>
        </a>
      </td>

      <!-- Адрес -->
      <td style="width: 33%; text-align: center; padding: 10px;">
        <a href="https://g.co/kgs/SvWpYS9" style="text-decoration: none; color: #393185;">
          <div style="width: 50px; height: 50px; margin: 0 auto; background-color: #ef6b03; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
            <svg aria-hidden="true" viewBox="0 0 576 512" width="24" height="24" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
              <path d="M560.02 32c-1.96 0-3.98.37-5.96 1.16L384.01 96H384L212 35.28A64.252 64.252 0 0 0 191.76 32c-6.69 0-13.37 1.05-19.81 3.14L20.12 87.95A32.006 32.006 0 0 0 0 117.66v346.32C0 473.17 7.53 480 15.99 480c1.96 0 3.97-.37 5.96-1.16L192 416l172 60.71a63.98 63.98 0 0 0 40.05.15l151.83-52.81A31.996 31.996 0 0 0 576 394.34V48.02c0-9.19-7.53-16.02-15.98-16.02zM224 90.42l128 45.19v285.97l-128-45.19V90.42zM48 418.05V129.07l128-44.53v286.2l-.64.23L48 418.05zm480-35.13l-128 44.53V141.26l.64-.24L528 93.95v288.97z"></path>
            </svg>
          </div>
          <div style="margin-top: 8px; font-weight: bold; color: #393185;">
            {{ [
              'bg' => 'ул. Източна тангента № 102, София, България',
              'en' => 'NPZ Iskar, ul. "Iztochna Tangenta" 102'
            ][$lang] }}
          </div>
        </a>
      </td>
    </tr>
  </table>

</div>

</body>
</html>
