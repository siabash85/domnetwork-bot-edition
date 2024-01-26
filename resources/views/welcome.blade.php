<!DOCTYPE html>
<html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/js/app.css', 'resources/js/app.js'])
    <title>DOM NETWORK PANEL</title>
</head>

<body class="antialiased">
    <div id="app"></div>
</body>

</html>
