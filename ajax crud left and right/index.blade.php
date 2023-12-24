<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel 8 ajax Crud Application</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="{{ asset('js') }}/sweetalert2@8.js"></script>
    <script src="{{ asset('js') }}/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>

<body>
    <div style="padding: 30px;"></div>
    <div class="container">
        <h2 style="color: red;">
        </h2>
        <div class="row">
            <div class="col-sm-8">
                <div class="card">
                    <div class="card-header">
                        All Teacher
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">SL</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Institute</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="data-table-body" class="table-data">
                                @php $i=1 @endphp
                                @foreach ($data as $value)
                                <tr>
                                    <td>{{ $i++ }}</td> <!-- Increment $i for each row -->
                                    <td>{{ $value->name }}</td>
                                    <td>{{ $value->title }}</td>
                                    <td>{{ $value->institute }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a>
                                                <button class="btn btn-md btn-success me-1 p-1" onclick='editData({{ $value->id }})'><i class="fas fa-edit"></i></button>
                                            </a>
                                            <form method="POST" onsubmit="return confirm('Are you sure?')">
                                                @method('DELETE')
                                                @csrf
                                                <button class="btn btn-md btn-danger p-1"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <button id="next-button" class="btn btn-primary">Next</button>


                    </div>

                </div>
            </div>

            <div class="col-sm-4">
                <div class="card">
                    <div class="card-header">
                        <span id="addT">Add New Teacher</span> <br>

                        <span id="updateT">Update Teacher</span>

                    </div>
                    <span id="output"></span>

                    <div class="card-body">
                        <form id="my-form">

                            <div class="form-group">
                                <label for="exampleInputEmail1">Name</label>
                                <input type="text" id="name" name="name" class="form-control" aria-describedby="emailHelp" placeholder="Enter name" required>

                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Title</label>
                                <input type="text" id="title" name="title" class="form-control " placeholder="Job Positon" required>

                            </div>

                            <div class="form-group">
                                <label for="exampleInputPassword1">Institute</label>
                                <input type="text" id="institute" class="form-control" name="institute" placeholder="Institute Name" required>

                            </div>
                            <!-- <input type="hidden" id="id">
                        <button type="submit" id="add" onclick="addData()" class="btn btn-primary">Add</button>
                        <button type="submit" id="update" onclick="updateData()" class="btn btn-primary">Update</button> -->
                            <input type="hidden" id="id">
                            <button type="submit" id="btnSubmit" class="btn btn-primary">Add</button>
                            <button type="submit" id="updateBtn" class="btn btn-primary" onclick='updateData()'>Update</button>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name = "csrf-token"]').attr('content')
            }
        })
    </script>
    <script>
        $('#addT').show();
        $('#btnSubmit').show();
        $('#updateT').hide();
        $('#updateBtn').hide();
    </script>

    <script>
        $(document).ready(function() {
            $("#my-form").on("submit", function(e) {
                e.preventDefault();
                var form = $("#my-form")[0];
                var data = new FormData(form);

                // Disable the submit button while the AJAX request is in progress
                $("#btnSubmit").prop("disabled", true);

                // Send the AJAX request
                $.ajax({
                    type: "POST",
                    url: "{{ route('store') }}", // Replace with your server-side endpoint
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function(data) {

                        // Handle the response from the server
                        $("#output").text(data.message);
                        setTimeout(function() {
                            $("#output").text("");
                        }, 2000);

                        $("#btnSubmit").prop("disabled", false); // Re-enable the submit button
                        form.reset();
                        $('table').load(location.href + ' .table');
                    },
                    error: function(e) {
                        // Handle the error
                        $("#output").text(data.responseText);
                        $("#btnSubmit").prop("disabled", false); // Re-enable the submit button
                    }
                });
            });
        });
    </script>
    <script>
        function editData(id) {
            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: "/teacher/edit/" + id, // Replace with your actual URL structure
                success: function(data) {
                    $('#addT').hide();
                    $('#btnSubmit').hide();
                    $('#updateT').show();
                    $('#updateBtn').show();

                    $('#id').val(data.id);

                    $('#name').val(data.name);
                    $('#title').val(data.title);
                    $('#institute').val(data.institute);
                    console.log(data);
                }
            });
        }
    </script>

    <script>
        function updateData(id) {
            var id = $('#id').val();
            var name = $('#name').val();
            var title = $('#title').val();
            var institute = $('#institute').val();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    name: name,
                    title: title,
                    institute: institute
                },
                url: "/teacher/update/" + id, // Replace with your actual URL structure

                success: function(data) {
                    $('#addT').show();
                    $('#btnSubmit').show();
                    $('#updateT').hide();
                    $('#updateBtn').hide();
                    console.log(data);
                }
            });
        }
    </script>