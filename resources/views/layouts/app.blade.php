<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Character Recorder') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/tabs-component.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/ldbtn.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/loading.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Hotjar Tracking Code for shark.sbs.arizona.edu/chrecorder/public -->
{{--    <script>--}}
{{--        (function(h,o,t,j,a,r){--}}
{{--            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};--}}
{{--            h._hjSettings={hjid:1699692,hjsv:6};--}}
{{--            a=o.getElementsByTagName('head')[0];--}}
{{--            r=o.createElement('script');r.async=1;--}}
{{--            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;--}}
{{--            a.appendChild(r);--}}
{{--        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');--}}
{{--    </script>--}}
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        .explore {
            color: #216a94;
            font-family: Arial;
            display: inline;
            vertical-align: bottom;
        }

        .explore:hover{
            color: #23527c;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand font-30" href="{{ url('/') }}">
                        <b>{{ config('app.name', 'Character Recorder') }} : <b style="font-size: 20px;">{{ getRandomPhrase() }}</b></b>
                    </a>

                </div>


                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @guest
                            {{--<li><a href="{{ route('login') }}">Login</a></li>--}}
                            {{--<li><a href="{{ route('register') }}">Register</a></li>--}}
                        @else
{{--                            <li><a href="{{ route('admin.companies.index') }}">Companies</a></li>--}}
{{--                            <li><a href="{{ route('admin.measurements.index') }}">Measurements</a></li>--}}
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->email }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
                <div class="row">
                    <div class="col-md-4">
                       <!--  <a id="top-user" href="{{ url('/leader-board') }}" style="text-decoration: none">
                            <div style="height: 34px;padding-top: 10px;">
                                <img src="{{ asset('images/crown.png') }}" style="display: inline"/>
                                <div class="explore" id="top_user" style="font-family: Arial; display: inline; vertical-align: bottom">fetching data...</div>
                            </div>
                        </a> -->
                    </div>
                    <div class="col-md-1">
                    </div>
                    <div class="col-md-3">
                        <!-- <div style="float: right">
                            <a id="explore-character" href="{{ url('/explore-character') }}" style="text-decoration: none;">
                                <div style="height: 34px; padding-top: 8px">
                                    <span class="glyphicon glyphicon-list" style="font-size: 20px"></span>
                                    <div class="explore">Explore Character Data</div>
                                </div>
                            </a>
                        </div> -->
                    </div>
                    <div class="col-md-1">
                    </div>
                    <div class="col-md-3">
                        <!-- <div style="float: right">
                            <a id="ontology-update" href="{{ url('/ontology-update') }}" style="text-decoration: none;">
                                <div style="height: 34px; padding-top: 8px">
                                    <span class="glyphicon glyphicon-stats" style="font-size: 20px"></span>
                                    <div class="explore">Ontology Update</div>
                                </div>
                            </a>
                        </div> -->
                        <div class="collapse navbar-collapse" id="app-subnav-collapse">
                            <ul class="nav navbar-nav navbar-right">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        <span class="glyphicon glyphicon-option-horizontal" style="font-size: 20px;color: #216a94;"></span>
                                    </a>

                                    <ul class="dropdown-menu" role="menu">
                                        <li>
                                            <a id="explore-character" href="{{ url('/explore-character') }}" style="text-decoration: none;">
                                                <div style="height: 30px; padding-top: 8px">
                                                    <span class="glyphicon glyphicon-eye-open" style="font-size: 16px;margin-right:3px;"></span>
                                                    <div class="explore">Explore Character Data</div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a id="shared-character" href="{{ url('/shared-character') }}" style="text-decoration: none;">
                                                <div style="height: 30px; padding-top: 8px">
                                                    <img src="{{ asset('images/venn.png') }}" style="display:inline;width: 19px;"/>
                                                    <div class="explore">Shared Characters</div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a id="ontology-update" href="{{ url('/ontology-update') }}" style="text-decoration: none;">
                                                <div style="height: 30px; padding-top: 8px">
                                                    <span class="glyphicon glyphicon-stats" style="font-size: 16px;margin-right:3px;"></span>
                                                    <div class="explore">Vocabulary/Ontology Update</div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}?v=2019_04_25"></script>
</body>
<!-- <script> 
$(document).ready(function(){
    $.ajax({
        url: "get_top_user", 
        success: function(result){
        $('#top_user').text(result)
    }});
});
</script> -->
</html>
