@extends('admin.layout.base')

@section('title', 'Sodai | Login')

@section('content')
    <div class="col-lg-6 col-md-10">
        <div class="card">
            <div class="card-header bg-primary">
                <div class="ec-brand">
                    <a href="index.html" title="Ekka">
                        <img class="ec-brand-icon" src="{{ asset('admin/img/logo/logo-login.png') }}" alt="" />
                    </a>
                </div>
            </div>
            <div class="card-body p-5">
                <h4 class="text-dark mb-5">Sign In</h4>

                <form action="{{ route('admin.login.post') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-12 mb-4">
                            <input type="email" class="form-control" id="email" placeholder="Email">
                        </div>

                        <div class="form-group col-md-12 ">
                            <input type="password" class="form-control" id="password" placeholder="Password">
                        </div>

                        <div class="col-md-12">
                            <div class="d-flex my-2 justify-content-between">
                                <div class="d-inline-block mr-3">
                                    <div class="control control-checkbox">Remember me
                                        <input type="checkbox" />
                                        <div class="control-indicator"></div>
                                    </div>
                                </div>

                                <p><a class="text-blue" href="#">Forgot Password?</a></p>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block mb-4">Sign In</button>

                            <p class="sign-upp">Don't have an account yet ?
                                <a class="text-blue" href="sign-up.html">Sign Up</a>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
