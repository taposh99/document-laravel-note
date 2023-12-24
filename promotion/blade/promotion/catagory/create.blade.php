@extends('layouts.app')
@section('title')
Promo Code
@endsection
@section('content')
<div class="container-fluid">
    <!-- message -->
    @if(session()->has('message'))
    <p class="alert alert-success text-center mt-4">{{ session()->get('message') }}</p>
    @elseif(session()->has('error'))
    <p class="alert alert-danger text-center mt-4">{{ session()->get('error') }}</p>
    @endif

    <div class="d-flex flex-column">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <h1>Add Category</h1>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">


                </div>
                <a class="btn btn-outline-primary float-end" href="{{ route('promotion.create') }}">{{ __('messages.common.back') }}</a>

            </div>


            <div class="col-12">
                @include('layouts.errors')
            </div>
            <div class="card">
                <div class="card-body">


                    <form action="{{ route('promotionCatagory.store') }}" method="post" enctype="multipart/form-data">

                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-5">
                                    <label class="">Category : </label><br><br>

                                    <input type="text" class="form-control" placeholder="Category" name="catagory" required>
                                </div>
                                @if ($errors->has('catagory'))
                                <span class="text-danger">{{ $errors->first('catagory') }}</span>
                                @endif
                            </div>


                            <div>
                                {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-3']) }}
                                <a href="{{ route('promotion.catagory.index') }}" class="btn btn-secondary">{{ __('messages.common.discard') }}</a>
                            </div>

                    </form>




                </div>


            </div>
        </div>
    </div>
</div>
</div>
@endsection