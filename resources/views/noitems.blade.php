@php
  use Carbon\Carbon;
  $users = \Auth::user();
  $currantLang = $users->currentLanguage();
@endphp
@extends('layouts.main')
@section('title', __('No Items'))
@push('css')
  <link rel="stylesheet" href="{{ asset('vendor/jqvmap/dist/jqvmap.min.css') }}">
@endpush
@section('content')

  <div class="card">
    <div class=" sm:px-6 lg:px-8 card-body">
      <div class="overflow-hidden text-center ">
        <div class="p-2">

          <div
            class="w-10  font-medium text-danger text-base text-center rounded border
                             bg-danger-transparent my-10 d-flex row">
            <div>
              <svg style="width: 80px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
              </svg>

            </div>
            <p class="font-bold text-lg">We could not find any quickbooks data for this account, or your connection
              for quickbooks or efris.</p>
          </div>

          <ul class="list-group my-1">
            <li class="list-group-item">Check your connection with Efris middleware</li>
            <li class="list-group-item">Check your connection with Quickbooks api</li>
          </ul>


        </div>

      </div>
    </div>
  </div>

@endsection
