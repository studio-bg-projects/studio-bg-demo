<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>
  @if (!empty($pageTitle))
    {{ $pageTitle }} &mdash;
  @endif
  {{ env('APP_NAME') }}
</title>

<style>
  {!! Vite::content('resources/scss/app.scss') !!}
</style>

@yield('headers')

<body>

@include('shared.alerts')

@yield('content')

</body>
</html>
