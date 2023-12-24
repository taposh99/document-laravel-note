@extends('layouts.app')
@section('title')
Promo Code
@endsection
@section('content')
<style>
    nav>div>a {
        text-decoration: none;
    }

    nav>div>div p {
        margin-top: 20px;
    }

    nav>div>div span .relative {
        display: none;
    }
</style>

<div class="container-fluid">
    <div class="d-flex flex-column table-striped">
        @include('flash::message')
        <!-- message -->
        @if(session()->has('message'))
        <p class="alert alert-success text-center mt-4">{{ session()->get('message') }}</p>
        @elseif(session()->has('error'))
        <p class="alert alert-danger text-center mt-4">{{ session()->get('error') }}</p>
        @endif


        <div class="card">
            <div class="card-body">
                <div class="hadder mb-5 d-flex justify-content-between">
                    <div>
                        <h3>Brand</h3>
                    </div>

                    <div>
                        <a href="{{ route('promotion.brandCreate') }}" type="button" class="btn btn-primary"> + New Create</a>
                    </div>

                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-responsiv">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Brand Nam</th>
                                <th scope="col">created</th>
                                <th scope="col">Updated</th>
                            
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        </tr>
                        @php $i=1 @endphp
                        @foreach ($brands as $data)
                        <tr>
                            <td>{{ $i++ }}</td>

                            <td>{{ $data->brand }}</td>
                            <td>{{ $data->created_at }}</td>
                            <td>{{ $data->updated_at }}</td>


                            <td>

                                <a href="{{ route('promotion.brand.edit', ['id' => $data->id]) }}"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('promotion.brand.delete') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="brand_id" value="{{ $data->id }}">
                                    <button style="font-size:13px;border: none;background: none;padding: 0;color: inherit;font: inherit;cursor: pointer;" role="button" onclick="return confirm('Are You Sure !!')"><i class="fa fa-trash" aria-hidden="true" style="color: red;"></i></button>

                                </form>
                            </td>

                        </tr>
                        @endforeach


                    </table>

                </div>





            </div>
        </div>
    </div>
</div>

@include('layouts.templates.actions')
@include('vcards.templates.templates')
@include('vcards.templates.analytics')




<script>
    $(document).on('click', '.checkStatus', function() {
        let id = $(this).data('id');
        let callUrl = route('orders.index', id);
        location.replace(callUrl);
        console.log(callUrl);
        // $.ajax({
        //     type: 'get',
        //     url: callUrl,
        //     success: function(response) {
        //         displaySuccessMessage(response.message)
        //         Livewire.emit('refresh');
        //         location.reload();
        //     },
        // })
    })
</script>

@endsection