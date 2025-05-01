<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name') }} - @yield('title', 'Destek Talepleri')</title>
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    @stack('styles')

    <!-- Support Ticket CSS -->
    @if(file_exists(public_path('vendor/supportticket/css/styles.css')))
        <link rel="stylesheet" href="{{ asset('vendor/supportticket/css/styles.css') }}">
    @endif
</head>
<body>
    <div id="app">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="container py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h4 mb-0">
                        <a href="{{ route('supportticket.tickets.index') }}">
                            @yield('header', 'Destek Talepleri')
                        </a>
                    </h1>
                    <div>
                        @yield('header-actions')
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="py-4">
            <div class="container">
                @include('supportticket::partials.alerts')
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-light py-3 mt-5">
            <div class="container text-center text-muted">
                <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. Tüm hakları saklıdır.</p>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <!-- Support Ticket JS -->
    @if(file_exists(public_path('vendor/supportticket/js/app.js')))
        <script src="{{ asset('vendor/supportticket/js/app.js') }}"></script>
    @endif
</body>
</html> 