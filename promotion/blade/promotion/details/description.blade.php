@extends('layouts.app')
@section('title')
{{__('messages.dashboard')}}
@endsection
@section('content')
<style>
    .card-img-container {
        position: relative;
        /* height: 200px; */
    }

    .card-img-container img {
        width: 100%;
        /* height: 100%; */
        object-fit: cover;
    }

    .img-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .card-img-container:hover .img-overlay {
        opacity: .1;
    }

    @media (max-width: 576px) {
        .card-img-container img {
            width: 100%;

        }
    }
</style>
<div class="container">

    <div class="card" style="text-align: justify;padding: 23px;">
        <div class="row">
            <div class="col"></div>
            <div class="col-auto text-end px-4 my-2">

                <!-- <a type="button" class="btn btn-primary" href="{{ url()->previous() }}"><span><i class="fa fa-arrow-left" aria-hidden="true"></i></span> Back</a> -->
                <a type="button" class="btn btn-primary" href="{{route('explore.more')}}"><span><i class="fa fa-arrow-left" aria-hidden="true"></i></span> Back</a>

            </div>
        </div>

        <div class="row">

            <div class="card-img-container">
                <img class="card-img-bottom image-fluid" src="{{ asset('images/banner/' . $promotionDetails->banner) }}" alt="Bologna" style="width:100%;" alt="Card image cap">
                <div class="img-overlay"></div>
            </div>

            <div class="col py-4">
                <h2>{{ $promotionDetails->title }}</h2>
                <p style="color: blue;">{{ $promotionDetails->discount }}</p>

                <h4 style="color: dimgrey;" class="card-text">{{ $promotionDetails->promotionCatagory->catagory }}</h4>

            </div>
        </div>
        <p>{!! $promotionDetails->desceription !!}</p>

    </div>
</div>

@include('dashboard.templates.templates')
@include('dashboard.templates.userTemplate')


@endsection