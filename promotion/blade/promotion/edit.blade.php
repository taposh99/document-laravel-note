@extends('layouts.app')
@section('title')
Promo Code
@endsection
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <h1>Update</h1>
                <a class="btn btn-outline-primary float-end" href="{{ route('promotion.index') }}">{{ __('messages.common.back') }}</a>
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

                    <form action="{{ route('promotion.update') }}" method="post" enctype="multipart/form-data">

                        @csrf
                        <input type="hidden" value="{{ $promotion->id }}" name="promotion_id">
                        <div class="row">

                            <div class="col-lg-6">
                                <div class="mb-5">
                                    <label class="">Title : </label>
                                    <input type="text" class="form-control" value="{{ $promotion->title }}" placeholder="Title" name="title" nullable>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-5">
                                    <label class="">Discount : </label>
                                    <input type="text" class="form-control" value="{{ $promotion->discount }}" name="discount" required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-5">
                                    <label class="">Slug : </label>
                                    <input type="text" class="form-control" value="{{ $promotion->slug }}" name="slug">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">Desceription :</label>
                                <textarea type="text" name="desceription" id="description" class="form-control" id="exampleFormControlTextarea1" rows="3" nullable>{!! $promotion->desceription !!}</textarea>
                            </div>

                            <div class="col-lg-6 mb-5">
                                <label class="" style="margin-top: 23px;">Banner :<span class="required"></span>
                                    <p style="font-size: 10px;">max_height 340px, max_width 1450px</p>
                                </label>
                                <input type="file" id="banner" class="form-control" value="{{ $promotion->banner }}" name="banner" required>
                                <img width="105px" height="55px" id="showImage" src="{{asset('images/banner')}}/{{ $promotion->banner }}" alt="Card image cap">

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

<script>
    ClassicEditor
        .create(document.querySelector('#description'))
        .catch(error => {
            console.error(error);
        });
</script>
<script type="text/javascript">
    CKEDITOR.replace('description', {
        filebrowserUploadUrl: "{{route('ckeditor.upload', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form'
    });
</script>



@endsection