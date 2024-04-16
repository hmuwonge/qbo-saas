@extends('layouts.main')
@section('title', __('Show Announcement'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('show.announcement.list') }}">{{ __('Show Announcement List') }}</a></li>
    <li class="breadcrumb-item">{{ __('Show Announcement') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-8 m-auto">
            <div class="card text-center">
                <div class="card-header">
                    <h5>{{ __('Show Announcement') }}</h5>
                    <p class="card-subtitle text-muted">{{ __('Start Date') }} :
                        {{ Utility::date_time_format($showAnnouncement->start_date) }}</p>
                    <p class="card-subtitle text-muted">{{ __('End Date') }} :
                        {{ Utility::date_time_format($showAnnouncement->end_date) }}</p>
                </div>
                <div class="card-body">
                    <h5 class="card-title mb-4">{{ $showAnnouncement->title }}</h5>
                    <img class="img-fluid card-img-bottom mb-2" src="{{ Storage::url($showAnnouncement->image) }}">
                    <hr>
                    <p>{!! $showAnnouncement->description !!}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
