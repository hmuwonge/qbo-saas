@extends('layouts.main')
@section('title', __('Show Announcement List'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Show Announcement List') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                @foreach ($announcementLists as $announcementList)
                    <div class="col-md-3">
                        <div class="card bg-primary text-white text-center announcement-card">
                            <div class="card-body card-body-inner">
                                <div class="image-container">
                                    <img src="{{ Storage::url($announcementList->image) }}" class="img-fluid">
                                </div>
                                <div class="card-content">
                                    <div class="card-top-content">
                                        <p class="my-4">{{ $announcementList->title }}</p>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-12">
                                            <div class="d-grid">
                                                <a href="{{ route('show.announcement', ['id' => $announcementList->id]) }}"
                                                    class="btn btn-sm btn-light">
                                                    {{ __('Show Announcement') }}
                                                    <i class="ti ti-chevron-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
