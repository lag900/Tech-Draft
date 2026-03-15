<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TechDraft') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        @yield('content')
    </div>
    <script>
        window.onerror = function(message, source, lineno, colno, error) {
            var div = document.createElement('div');
            div.style.cssText = 'color:red; background:#fee; padding:20px; position:fixed; top:50px; left:0; right:0; z-index:99998; border-bottom:3px solid red; font-family:monospace; direction:ltr; text-align:left; max-height: 100vh; overflow: auto;';
            div.innerHTML = '<strong>Global Initial Error:</strong> ' + message + '<br><br><pre style="white-space:pre-wrap;">' + (error ? error.stack : source + ':' + lineno + ':' + colno) + '</pre>';
            document.body.appendChild(div);
        };
        window.addEventListener('unhandledrejection', function(event) {
            var div = document.createElement('div');
            div.style.cssText = 'color:red; background:#fee; padding:20px; position:fixed; top:100px; left:0; right:0; z-index:99997; border-bottom:3px solid red; font-family:monospace; direction:ltr; text-align:left; max-height: 100vh; overflow: auto;';
            div.innerHTML = '<strong>Unhandled Promise:</strong> ' + (event.reason ? event.reason.toString() : 'Unknown');
            document.body.appendChild(div);
        });
    </script>
</body>
</html>
