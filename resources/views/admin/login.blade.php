<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login | MK TRADERS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesdesign" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">

    <!-- Layout Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="auth-maintenance d-flex align-items-center min-vh-100">
        <div class="bg-overlay bg-light"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="auth-full-page-content d-flex min-vh-100 py-sm-5 py-4">
                        <div class="w-100">
                            <div class="d-flex flex-column h-100 py-0 py-xl-3">
                                <div class="text-center mb-4">
                                    <a href="#" class="">
                                        <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="22"
                                            class="auth-logo logo-dark mx-auto">
                                        <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="22"
                                            class="auth-logo logo-light mx-auto">
                                    </a>
                                </div>

                                <div class="card my-auto overflow-hidden">
                                    <div class="row g-0">
                                        <div class="col-lg-6 d-flex align-items-center justify-content-center">
                                            <img src="{{ asset('assets/images/Traders.jpeg') }}"
                                                 alt="Auth Image"
                                                 class="img-fluid"
                                                 style="max-height: 360px;">
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="p-lg-5 p-4">
                                                <div>
                                                    <div class="text-center mt-1">
                                                        <h4 class="font-size-18">Welcome Back !</h4>
                                                        <p class="text-muted">Sign in to continue to Tocly.</p>
                                                    </div>

                                                    <!-- Laravel Login Form -->
                                                    <form method="POST" action="{{ route('login.post') }}" class="auth-input">
                                                        @csrf
                                                        <div class="mb-2">
                                                            <label for="email" class="form-label">Email</label>
                                                            <input type="email" class="form-control" id="email"
                                                                name="email" placeholder="Enter email" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label" for="password-input">Password</label>
                                                            <input type="password" class="form-control" id="password-input"
                                                                name="password" placeholder="Enter password" required>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="remember"
                                                                id="auth-remember-check">
                                                            <label class="form-check-label" for="auth-remember-check">Remember me</label>
                                                        </div>
                                                        <div class="mt-3">
                                                            <button class="btn btn-primary w-100" type="submit">Sign In</button>
                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card -->

                                <div class="mt-5 text-center">
                                    <p class="mb-0">©
                                        <script>document.write(new Date().getFullYear())</script> Synergy integrated solutions<i
                                            class="mdi mdi-heart text-danger"></i> 
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

    <!-- Icon -->
    <script src="https://unicons.iconscout.com/release/v2.0.1/script/monochrome/bundle.js"></script>

    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>
