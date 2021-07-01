@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row panel">
                        <div class="col-md-6">
                            <p>This application features a spreadsheet for Carex taxonomists to record characters for their selected specimens. This experiemental application encourages the users to use standard vocabulary and add new terms to the vocabulary. It also lets the users see the character states recorded by other users for the same taxon.</p>
                        </div>
                        <div class="col-md-5 col-md-offset-1">
                            <p>We hope the application will help reduce the variation, and increase the clarity and the parallelism in taxonomic descriptions. Try this application, and let use know how we can make it better.</p>
                        </div>
                    </div>
                </div>
                <div class="panel-heading font-20">Login</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label font-20">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label font-20">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" onclick="javascript: form.action='/chrecorder/public/login';" class="btn btn-primary font-20">
                                    Login
                                </button>
                                <button type="submit" onclick="javascript: form.action='/chrecorder/public/register';" class="btn btn-primary font-20">
                                    Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
