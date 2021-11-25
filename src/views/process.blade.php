<!DOCTYPE html>
<html lang="en">
<head>
    <title>Importify DWS</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        .btn a{
            text-decoration:none;
            color:white;
        }
        </style>
</head>
<body>

    <div class="container" style="border: 2px solid black; margin-top:10%;padding:20px;">
        <h2 class="text-center">Upload excel file </h2>
        @if ($message = Session::get('error_message'))
        <div class="alert alert-danger">
            <strong>Danger!</strong> {{ $message ?? "" }}
        </div>
        @endif
        @if ($message = Session::get('success_message'))
        <div class="alert alert-success">
            <strong>Success!</strong> {{ $message ?? "" }}
        </div>
        @endif
        <form action="{{ url('process-files/') }}" method="POST" enctype="multipart/form-data">
            <div class="card-body">
                <input type="hidden" name="fileName" value="{{ $fileName }}">
                <input type="hidden" name="originalFileName" value="{{ $originalFileName }}">
                <input type="hidden" name="fileExtension" value="{{ $fileExtension }}">
                <div class="form-group">
                    <label for="contact_number">Tables</label>
                    <select name="table" id="tables_to_insert" class="form-control">
                        <option value="*" selected>Select table to insert </option>
                        @foreach($tables as $table)
                        <option value="{{ $table }}" selected>{{ $table }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <label for="contact_number" style="width:220px;padding-left:20px;">Table Field</label>
                    <label for="contact_number" style="width:420px;padding-left:50px;">Row Field</label>
                </div>
                <div id="replaceWithNew" class="form-group" style="margin-left:1%;">
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer mt-3">
                <button type="submit" class="btn btn-primary"> <i class="fa fa-save"></i> Save</button>
                <button type="reset" class="btn btn-danger"> <i class="fa fa-redo"></i><a href="{{ url('importify/') }}"> Cancel</a></button>
            </div>
        </form>
    </div>
</body>
<script>
    $(document).ready(function(){
        $("#tables_to_insert").change(function() {
        var table = $(this).val();
        var rowInFile=@json($rowInFile);
        $.ajax({
            type: 'get',
            url: "{{route('getColumns')}}/"+table,
            data: {  _token: "{{ csrf_token() }}", table: table ,rowInFile:rowInFile},
            success: function(resp) {
                $('#replaceWithNew').html(resp.html);
            },
            error: function(error) {
                alert('error');
                console.log(error);
            }
        });
    });
});
</script>
</html>


