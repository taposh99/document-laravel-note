@extends('layouts.app')
@section('title')
Promo Code
@endsection
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <h1>Update Catagory</h1>
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

                    <form action="{{ route('catagory.update') }}"  method="post" enctype="multipart/form-data">

                        @csrf
                        <input type="hidden" value="{{ $catagoryEdit->id }}" name="catagoryId">
                        <div class="row">

                            <div class="col-lg-6">
                                <div class="mb-5">
                                    <label class="">Catagory : </label>
                                    <input type="text" class="form-control" value="{{ $catagoryEdit->catagory }}"placeholder="Title" name="catagory" nullable>
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