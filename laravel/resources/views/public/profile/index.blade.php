@extends('public.profile.layout')

@section('profile-content')
  <h2 class="h4 card-title">Профил</h2>

  <h5 class="text-body-secondary">Email</h5>
  <p class="text-body-secondary">{{ auth()->user()->email }}</p>

  <h5 class="text-body-secondary">Име</h5>
  <p class="text-body-secondary">{{ auth()->user()->fullName }}</p>

  <h5 class="text-body-secondary">Дата на създаване</h5>
  <p class="text-body-secondary">{{ auth()->user()->createdAt }}</p>
@endsection
