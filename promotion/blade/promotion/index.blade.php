@extends('layouts.app')
@section('title')
Promo Code
@endsection
@section('content')
<style>
    /* Bootstrap 5 overrides */
    .table-responsive {
        overflow-x: auto;
    }
</style>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <h3>Promotion</h3>
                </div>
                <div>
                    <a href="{{ route('promotion.create') }}" type="button" class="btn btn-primary">+ New Create</a>
                </div>
            </div>

            @include('flash::message')

            @if(session()->has('message'))
            <p class="alert alert-success text-center mt-4">{{ session()->get('message') }}</p>
            @elseif(session()->has('error'))
            <p class="alert alert-danger text-center mt-4">{{ session()->get('error') }}</p>
            @endif

            <div class="table-responsive">
                <div class="row mb-3">
                    <div class="col-md-4">
                    <input type="text" class="form-control" id="searchInput"  placeholder="Search">

                    </div>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Title</th>
                            <th scope="col">Category</th>
                            <th scope="col">Brand</th>
                            <th scope="col">Banner</th>
                            <th scope="col">Discount</th>
                            <th scope="col">Slug</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1 @endphp
                        @foreach ($promotionValus as $promotion)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $promotion->title }}</td>
                            <td>{{ $promotion->promotionCatagory->catagory }}</td>
                            <td>{{ $promotion->promotionBrand->brand }}</td>
                            <td><img src="{{ asset('images/banner/' . $promotion->banner) }}" width="105px" height="55px"></td>
                            <td>{{ $promotion->discount }}</td>
                            <td>{{ $promotion->slug }}</td>
                            <td>
                                @if($promotion->status==1)
                                <a href="{{ route('change.promotionStatus', ['id' => $promotion->id]) }}" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-success" style="font-size: 11px;">Active</a>
                                @else
                                <a href="{{ route('change.promotionStatus', ['id' => $promotion->id]) }}" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger" style="font-size: 11px;">Inactive</a>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('promotion.edit', ['id' => $promotion->id]) }}"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('promotion.delete') }}" method="post" style="display: inline-block;">
                                    @csrf
                                    <input type="hidden" name="promotion_id" value="{{ $promotion->id }}">
                                    <button type="submit" style="   font-size:13px;border: none;background: none;padding: 0;color: inherit;font: inherit;cursor: pointer;" role="button" onclick="return confirm('Are You Sure !!')"><i class="fa fa-trash" aria-hidden="true" style="color: red;"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

@include('layouts.templates.actions')
@include('vcards.templates.templates')
@include('vcards.templates.analytics')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        // Search functionality
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('table.table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>
@endsection
