<!DOCTYPE html>
<html lang="en">
<head>
    <title>Importify DWS</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    @if(Session::has('download.in.the.next.request'))
    <meta http-equiv="refresh" content="2;url={{Session::get('download.in.the.next.request') }}">
 @endif
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
        <form action="{{ url('fetch-files/') }}" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="email">File</label>
                <input type="file" class="form-control" id="file" placeholder="Choose File" name="file">
                {{-- @if($errors->has('file'))
                <p class="text text-danger -mb-2">{{$errors->first('file')}}</p>
                @endif --}}
            </div>
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
    </div>
</body>
</html>




{{-- <!DOCTYPE html>
<html>
<head>
	<title>Importify</title>
</head>
<body>
	<h1 style="text-align:center">
		The time is :
		<span style="font-weight:normal">{{ $time }}</span>
</h1>
</body>
</html> --}}
