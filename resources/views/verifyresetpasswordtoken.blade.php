@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ url('api/password/reset') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{$passwordReset['random_token']}}">

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
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


<!--
<!DOCTYPE html>
<html>
<head>
  <title>Reset Password</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <center id="content-wrapper">

    <div class="container-fluid">

      <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> <h3>Reset Password</h3>
        </div>
        <div class="card-body">
          <form action="{{ url('create-category') }}" method="post">
        @csrf
            <div class="col-md-6">
              <input type="hidden" name="token" value="{{$passwordReset['random_token']}}">
              <div class="form-group">
                <div class="form-label-group">
                  <label for="">Enter Password</label>
                  <input type="text" id="" name="name" class="form-control" placeholder="Password" required="required">
                </div>
              </div>
              <div class="form-group">
                <div class="form-label-group">                  
                  <label for="description">Enter Confirm Password</label>
                  <input type="textarea" id="description" name="description" class="form-control" placeholder="Confirm Password" required="required">
                </div>
              </div>
              <div class="form-group">
                 <input type="submit" class="btn btn-primary btn-block" value="Reset Password">
              </div>
            </div>
          </form>
        </div>
      </div> 

    </div>
  </center>
</body>
</html>  -->
