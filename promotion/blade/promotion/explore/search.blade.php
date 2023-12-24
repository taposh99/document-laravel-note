@extends('layouts.app')
@section('title')
{{__('messages.dashboard')}}
@endsection
@section('content')
<style>
    .card:hover {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
</style>




{{--All promotion--}}


<div class="container">
    <div class=" d-flex justify-content-end">
        <a href="{{ url()->previous() }}" target="blank" class="btn btn-primary" style="margin-bottom: 10px;">
            Back
        </a>
    </div>


    <div class="row">

        <div class="text-center">
        @if ($results->isEmpty())
        <p>No results found.</p>
        @else
    </div>
        @foreach ($results as $image)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow p-3 mb-5 bg-white rounded" style="height: 100%;">
                <img class="card-img-top" src="{{ asset('images/banner/' . $image->banner) }}" style="height: 200px;" alt="Bologna">
                <div class="card-body">
                    <!-- <h2 class="card-title" style="color: blue;text-align: center;font-size: 13px;"><i class="fa-solid fa-tag"></i> {{ $image->discount }}</h2> -->
                    <h3 style="text-align: center;">{!! Str::limit($image->title, 40, '...') !!}</h3>
                    <h2 style="text-align: center;">{{ $image->promotionBrand->brand }}</h2>



                    <div style="text-align: center;">
                        <a href="{{ route('promotion.details', $image->slug) }}" class="btn btn-primary stretched-link">Details</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>

@include('dashboard.templates.templates')
@include('dashboard.templates.userTemplate')


@endsection
