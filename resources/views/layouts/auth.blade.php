<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel="icon" type="image/x-icon" href="{{getFavicon()}}">
    <link href="//fonts.googleapis.com/css?family=Quicksand:300,400,500,700" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/authentication/css/style.css')}}">
    @yield('css')
</head>
<body>
    
    <header>
        <a href="{{route('home')}}"><img src="{{getLogo()}}" alt="Logo"/></a>
    </header>
	
    @yield('alert')

    <main>
        @yield('content')
    </main>

	<footer>
		<p> &copy; {{date("Y")}} All Rights Reserved | Design by <a href="http://proweb.vn" target="_blank">proweb.vn</a></p>
	</footer>
    @yield('js')
</body>
</html>