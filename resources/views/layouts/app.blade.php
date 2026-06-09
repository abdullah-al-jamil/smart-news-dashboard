<!DOCTYPE html>
<html>
<head>
    <title>Smart News Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow p-4">...</nav>
    <main class="container mx-auto p-4">
        @yield('content')
    </main>
</body>
</html>