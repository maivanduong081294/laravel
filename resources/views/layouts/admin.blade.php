<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title','Admin')</title>
    <link rel="icon" type="image/x-icon" href="{{getFavicon()}}">
    <link href="//fonts.googleapis.com/css?family=Quicksand:300,400,500,700" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('assets/admin/css/style.css')}}">
    @yield('css')
</head>
<body>
    @include('layouts.admin.header')
    <div id="wrapper">
        @section('sidebar')
            @include('layouts.admin.sidebar')
        @show
        <main id="main">
            <div id="container">
                @hasSection ('heading')
                <div class="page-breadcrumb">
                    <div class="heading">
                        <h1>
                            {{$heading}}
                        </h1>
                        @hasSection ('heading-after')
                            <div class="heading-after">
                                @yield('heading-after')
                            </div>
                        @endif
                    </div>
                    @yield('breadcrumb')
                </div>
                @endif
                <div id="content">
                    @yield('alert')
                    @yield('content')
                </div>
            </div>
            @include('layouts.admin.footer')
        </main>
    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{asset('assets/admin/js/main.js')}}"></script>
    <script>
        ajaxUrl = {!! json_encode(route('admin.ajax')) !!}
        ajaxToken = {!! json_encode(csrf_token()) !!}
    </script>
    @yield('js')
</body>
</html>