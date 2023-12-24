@extends('layouts.app')
@section('title')
Promo Code
@endsection
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <h1>Add Promotion</h1>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('promotion.catagoryCreate') }}">
                        <button class="btn btn-primary me-md-2" type="button">+ Catagory</button>
                    </a>
                    <a href="{{ route('promotion.brandCreate') }}">
                        <button class="btn btn-primary" type="button">+ Brand</button>
                    </a>

                </div>
                <a class="btn btn-outline-primary float-end" href="{{ route('promotion.index') }}">{{ __('messages.common.back') }}</a>

            </div>




            <div class="col-12">
                @include('layouts.errors')
            </div>
            <div class="card">
                <div class="card-body">


                    <form action="{{ route('promotion.store') }}" method="post" enctype="multipart/form-data">

                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-5">
                                    <label class="">Title : </label>
                                    <input type="text" class="form-control" placeholder="Title" name="title" nullable>
                                </div>
                            </div>

                            <div class="col-lg-6 mb-5">
                                <label class="">Status : </label>
                                <select class="form-control" id="status" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-5">
                                    <label class="">Category : </label>
                                    <select class="form-control" name="category_id" required id="" class="@error('title') is-invalid @enderror">
                                        <option value="">Select A Category</option>
                                        @foreach($proCatagory as $categorys)
                                        <option value="{{$categorys->id}}">{{$categorys->catagory}}</option>
                                        @endforeach
                                    </select>
                                    @error('title')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6 mb-5">
                                <label class="">Brand : </label>
                                <select class="form-control" name="brand_id" required id="" class="@error('title') is-invalid @enderror">
                                    <option value="">Select A Brand</option>
                                    @foreach($proBrand as $brands)
                                    <option value="{{$brands->id}}">{{$brands->brand}}</option>
                                    @endforeach
                                </select>
                                @error('title')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-5">
                                    <label class="">Discount : </label>
                                    <input type="text" class="form-control" placeholder="40% Discount" name="discount" required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-5">
                                    <label class="">Slug : </label>
                                    <input type="text" class="form-control" placeholder="Slug" name="slug" nullable>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">Desceription :</label>
                                <textarea type="text" name="desceription" id="description" class="form-control" id="exampleFormControlTextarea1" rows="3" nullable></textarea>
                            </div>
                            <div class="col-lg-6 mb-5">
                                <label class="" style="margin-top: 23px;">Banner:<span class="required"></span><p style="font-size: 10px;">max_height 340px, max_width 1450px</p></label>
                                <input type="file" id="image" name="banner" required>

                            </div>
                            <br><br> <br>


                            <div style="margin-top: 12px;">
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
