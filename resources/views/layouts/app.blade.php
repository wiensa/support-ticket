<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - {{ __('supportticket::common.support_tickets') }}</title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- Custom Styles -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 600;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
            margin-bottom: 1.5rem;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
        }
        .ticket-status {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .status-open {
            background-color: #28a745;
        }
        .status-pending {
            background-color: #ffc107;
        }
        .status-resolved {
            background-color: #17a2b8;
        }
        .status-closed {
            background-color: #6c757d;
        }
        .ticket-reply {
            border-left: 3px solid #f8f9fa;
            padding-left: 15px;
            margin-bottom: 20px;
        }
        .ticket-reply.admin {
            border-left-color: #17a2b8;
        }
        .ticket-reply.user {
            border-left-color: #28a745;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('supportticket.tickets.*') ? 'active' : '' }}" href="{{ route('supportticket.tickets.index') }}">
                                {{ __('supportticket::common.support_tickets') }}
                            </a>
                        </li>
                        
                        @if(auth()->user() && auth()->user()->can('viewAny', \Wiensa\SupportTicket\Models\Category::class))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('supportticket.admin.*') ? 'active' : '' }}" href="{{ route('supportticket.admin.tickets.index') }}">
                                    {{ __('supportticket::admin.admin_panel') }}
                                </a>
                            </li>
                        @endif
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @auth
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
        
        <footer class="py-3 text-center text-muted">
            <div class="container">
                <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }} - {{ __('supportticket::common.support_ticket_system') }}</p>
            </div>
        </footer>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html> 