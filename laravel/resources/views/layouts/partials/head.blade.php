<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">


<title>
  @if (!empty($pageTitle))
    {{ $pageTitle }} &mdash;
  @endif
  {{ env('APP_NAME') }}
</title>

<script>
  (() => {
    const storedTheme = localStorage.getItem('theme');
    const theme = storedTheme ? storedTheme : 'light';
    document.documentElement.setAttribute('data-bs-theme', theme);
  })();
</script>

@vite([
  'resources/scss/app.scss',
  'resources/js/app.js'
])

@yield('headers')

<meta name="csrf-token" content="{{ csrf_token() }}">
<meta http-equiv="Cache-Control" content="no-store"/>

<!-- Favicon -->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/img/favicons/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/img/favicons/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/img/favicons/favicon-16x16.png') }}">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('/img/favicons/favicon.ico') }}">
<link rel="manifest" href="{{ asset('/img/favicons/manifest.json') }}">
<meta name="msapplication-TileImage" content="{{ asset('/img/favicons/mstile-150x150.png') }}">
<meta name="theme-color" content="#ffffff">
