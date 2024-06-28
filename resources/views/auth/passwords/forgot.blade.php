@extends('layouts.app')

@section('content')
<div class="container">
    @if(Session::has('alert') && Session::has('msg'))
    <div class="alert alert-{{Session::get('alert')}} alert-dismissible fade show" role="alert">
        {{Session::get('msg')}}
        <button class="close" type="button" data-dismiss="alert" aria-label="Close"><span
                aria-hidden="true">×</span></button>
    </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="row justify-content-center">
                <h1 class="m-5">{{__('auth.password.forgot.title')}}</h1>
                <label class="m-3">
                    {{__("auth.password.forgot.msg")}}
                </label>
            </div>
            <div class="card border-light bg-light">
                <div class="card-body">
                    <form method="POST" action="{{ route('password.forgot') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary my-primary-btn">
                                    {{ __('Send') }}
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