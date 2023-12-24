@extends('layouts.app')
@section('title')
Promo Code
@endsection
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <h1>Update Brand</h1>
                <a class="btn btn-outline-primary float-end" href="{{ route('promotion.index')}}">{{ __('messages.common.back') }}</a>
            </div>

            <div class="col-12">
                @include('layouts.errors')
                <!-- message -->
                @if(session()->has('message'))
                <p class="alert alert-success text-center mt-4">{{ session()->get('message') }}</p>
                @elseif(session()->has('error'))
                <p class="alert alert-danger text-center mt-4">{{ session()->get('error') }}</p>
                @endif

            </div>

            <div class="card">
                <div class="card-body">

                    <form action="{{ route('brand.update') }}"  method="post" enctype="multipart/form-data">

                        @csrf
                        <input type="hidden" value="{{ $brandEdit->id }}" name="brandId">
                        <div class="row">

                            <div class="col-lg-6">
                                <div class="mb-5">
                                    <label class="">Brand : </label>
                                    <input type="text" class="form-control" value="{{ $brandEdit->brand }}"placeholder="Brand" name="brand" nullable>
                                </div>
                            </div>

                            <div>
                                {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-3']) }}
                                <a href="{{ route('promotion.index') }}" class="btn btn-secondary">{{ __('messages.common.discard') }}</a>
                            </div>

                    </form>




                </div>


            </div>
        </div>
    </div>
</div>
</div>
@endsection