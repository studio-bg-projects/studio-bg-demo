<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

@include('layouts.partials.head')

<body>

@include('shared.alerts')

@yield('content')

</body>
</html>
