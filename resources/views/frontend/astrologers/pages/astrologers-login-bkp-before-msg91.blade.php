<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Astrologer Login</title>
    <link rel="icon" href="/{{ $logo['value'] }}" type="image/x-icon">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta
        content="Ask an online Astrologer and get instant consultation on top Astrology portal. Accurate astrology predictions and solutions by India's best Astrologers' team."
        name="description" />
    <meta property="Keywords" content="" />

    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="" />
    <meta name="twitter:description"
        content="Ask an online Astrologer and get instant consultation on top Astrology portal. Accurate astrology predictions and solutions by India's best Astrologers' team." />
    <meta name="twitter:title"
        content="Online Astrology Consultation, Ask an Astrologer - Astroway" />
    <meta name="twitter:image" content="/public/storage/images/AdminLogo1707194841.png" />

    <meta property="og:type" content="website" />
    <meta property="og:title"
        content="Online Astrology Consultation, Ask an Astrologer - Astroway" />
    <meta property="og:description"
        content="Ask an online Astrologer and get instant consultation on top Astrology portal. Accurate astrology predictions and solutions by India's best Astrologers' team." />
    <meta property="og:image" content="/public/storage/images/AdminLogo1707194841.png" />
    <meta property="og:url" content="index.html" />
    <meta property="og:site_name" content="Astroway" />

    <title>Online Astrology Consultation, Ask an Astrologer - Astroway</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link href="index.html" rel="canonical" />


    {{-- <link href="{{asset($logo->value)}}" rel="shortcut icon" type="image/x-icon" /> --}}



    <script src="{{ asset('public/build/assets/jquery.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="preload" href="https://translate.google.com/translate_a/element.js?cb=googleTranslateInit"
        as="script">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            text-align: center;
        }

        .login-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .logo {
            width: 50px;
            margin-right: 10px;
        }

        .site-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .login-title {
            font-size: 20px;
            color: #555;
            margin-bottom: 20px;
        }

        #otpless-login-page {
            margin-top: 20px;
        }

        /* Responsive styles */
        @media (max-width: 600px) {
            .logo {
                width: 40px;
                margin-right: 5px;
            }

            .site-name {
                font-size: 20px;
            }

            .login-title {
                font-size: 18px;
            }

            .login-container {
                padding: 15px;
            }
        }
    </style>
</head>

<body>


    <div class="login-container">
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('error') }}
        </div>
    @endif
        <div class="login-header">
            <img src="/{{ $logo['value'] }}" alt="Logo" class="logo">
            <!--<span class="site-name">{{$appname['value']}}</span>-->
        </div>
        <h1 class="login-title">Astrologer Login</h1>
        <div class="login-body">
            <div id="otpless-login-page"></div>
        </div>
        <form method="post" action="{{route('front.verifyOTLAstro')}}" id="OtpLesslogin">
            @csrf
            <input type="hidden" name="otl_token" id="otl_token">
            <input id="contactNo" name="contactNo" type="hidden" value="" />
            <input id="countryCode" name="countryCode" type="hidden" value="" />
        </form>
    </div>
</body>

</html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script src="{{ asset('public/build/assets/jquery.min.js') }}"></script>
<script id="otpless-sdk" type="text/javascript" src="https://otpless.com/v2/auth.js"
    data-appid="{{ $otplessAppId['value'] }}"></script>
<script>
    function otpless(otplessUser) {
        console.log(JSON.stringify(otplessUser));
        $("#otl_token").val(otplessUser.token);
         $("#OtpLesslogin").submit();
    }
</script>
@if (request('error'))
    <script>
        toastr.error("{{ request('error') }}");

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.pathname);
        }
    </script>
@endif
