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
<div class="row justify-content-between align-items-start mb-5">
    <div class="col-sm-12 col-md-auto mb-3">
        <form action="{{ route('all.search') }}" method="GET" class="d-flex">
            <input type="text" name="query" placeholder="Search & more..." class="form-control me-2" style="height: 39px">
            <button class="btn btn-primary" type="submit" style="  height: 41px;">Search</button>
        </form>
    </div>

    <div class="col-sm-12 col-md d-flex justify-content-md-between align-items-center">
        <form name="_token" action="{{ route('promotion.filter.category') }}" id="tour-dropdown" method="get">
            {!! csrf_field() !!}
            <select id="tour" class="btn btn-primary" name="category_id" style="width: 138px;">
                <option value="">Category</option>
                @foreach ($promotionCatagory as $category)
                <option value="{!! $category->id !!}">{!! $category->catagory !!}</option>
                @endforeach
            </select>
        </form>

        
        <a type="button" class="btn btn-primary ms-auto" href="{{route('admin.dashboard')}}" data-turbo="false">
            <span><i class="fa fa-arrow-left" aria-hidden="true"></i></span> Back
        </a>
    </div>
</div>

    <div class="row">
        @foreach ($explore as $image)
        <div class="col-12 col-md-6 col-lg-4 mb-5" style="height: 374px;">
            <div class="card shadow p-3 mb-5 bg-white rounded" style="height: 100%;">
                <img class="card-img-top" src="{{ asset('images/banner/' . $image->banner) }}" style="height: 200px;" alt="Bologna">
                <div class="card-body">
                    <h4 style="text-align: center;">{!! Str::limit($image->title, 40, '...') !!}</h4>
                    <h6 style="text-align: center;color: dimgrey;">{{ $image->promotionBrand->brand }}</h6>


                    <div style="text-align: center;">
                        <a href="{{ route('promotion.details', $image->slug) }}" class="btn btn-primary stretched-link">Details</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>



@include('dashboard.templates.templates')
@include('dashboard.templates.userTemplate')

<script>
    function dataFilter(id) {
        $(id).on('change', function(e) {

            var variable = e.target.value;

            $(id + '-dropdown').submit();

        });
    };

    dataFilter('#tour');
</script>

@endsection
