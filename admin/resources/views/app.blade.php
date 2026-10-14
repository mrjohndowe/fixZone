<!DOCTYPE html>
<html lang="{{ config('app.locale', 'en') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Repair Box') }}</title>
    <link rel="shortcut icon" href="{{ $fav_icon }}">
    <link href="{{ url(mix('css/app.css')) }}" rel="stylesheet">
    <link href="{{asset('/css/custom-style.css')}}" rel="stylesheet">

    @if($app_data['square_state'])
    @if($app_data['square_sandbox'])
    <script src="https://sandbox.web.squarecdn.com/v1/square.js"></script>
    @else
    <script src="https://web.squarecdn.com/v1/square.js"></script>
    @endif
    @endif
</head>
<body>
    


 @if(config('app.demo_mode'))
    <div class="bg-pink-600 text-white px-5 py-1 md:flex justify-between items-center shadow-lg space-x-4 text-sm">
        <span>
            <a href="https://codehas.gitbook.io/repair-box/" target="_blank" rel="noopener">
                View Documentation
            </a>
        </span>
        <span>Demo mode: data resets every 48 hours.</span>
        <span>
            Report issues: 
            <a href="mailto:info@codehas.net" target="_blank" rel="noopener">info@codehas.net</a> | 
            <a href="mailto:info.codehas@gmail.com" target="_blank" rel="noopener">info.codehas@gmail.com</a>
        </span>
        <span>
            <a href="https://wa.me/447394521218?text={{ urlencode('Hello, I need help with Repair Box version ' . config('app.version')) }}" 
               target="_blank" 
               rel="noopener">
                Contact us on WhatsApp
            </a>
        </span>
        <span>Version {{ config('app.version') }}</span>
    </div>
@endif


  <div id="app"></div>
  <script>
    window.app = {!! json_encode( $app_data, JSON_THROW_ON_ERROR) !!};
</script>
@routes
<script src="{{ url(mix('js/app.js')) }}"></script>
</body>
</html>
