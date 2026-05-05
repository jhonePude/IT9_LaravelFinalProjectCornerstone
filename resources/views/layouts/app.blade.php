<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title') - Cornerstone</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Link to the Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    
    @stack('styles')
</head>
<body class="">

    <!-- SIDENAV (Midnight Navy Theme) -->
    <nav class="sidenav" id="mySidenav">
        <!-- Sidebar Header (Logo & Title) -->
        <div class="sidenav-header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="side-logo">
            <h1 class="side-title">CORNERSTONE</h1>
            <hr class="gold-divider">
            
            <button class="collapse-toggle" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <!-- Sidebar Menu -->
        <div class="sidenav-menu">
            @if(!Request::is('member/portal*'))
                <a href="{{ route('dashboard') }}" class="{{ Request::is('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('users.index') }}" class="{{ Request::is('users*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Members</span>
                </a>
                <a href="{{ route('events.index') }}" class="{{ Request::is('events*') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Events</span>
                </a>
                <a href="{{ route('finance.index') }}" class="{{ Request::is('finance*') ? 'active' : '' }}">
                    <i class="fa-solid fa-wallet"></i>
                    <span>Finance</span>
                </a>
            @endif
        </div>

        <div class="floating-indicator" id="indicator"></div>

        <!-- Sidebar Footer (Profile & Dropdown) -->
        <div class="sidenav-footer">
            <div class="dropdown-trigger" onclick="toggleDropdown()">
                @auth
                    <div class="user-profile-info">
                        {{-- FIX: Production fallback for profile pictures --}}
                        @php
                            $userPhoto = Auth::user()->Profile_Picture;
                            $photoPath = public_path('images/' . $userPhoto);
                            $displayPhoto = ($userPhoto && file_exists($photoPath)) ? asset('images/' . $userPhoto) : asset('images/profile-male.png');
                        @endphp
                        <img src="{{ $displayPhoto }}" class="user-avatar-small">
                        <span class="user-name-small">{{ Auth::user()->Fullname }}</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                @endauth
            </div>
            
            <div id="myDropdown" class="dropdown-content">
                @auth
                    @if(Request::is('member/portal*'))
                        {{-- FIX: Dashboard link only for Admin (Role 1) --}}
                        @if(Auth::user()->Role_Id == 1)
                            <a href="{{ route('dashboard') }}">
                                <i class="fa-solid fa-chart-line"></i> Dashboard
                            </a>
                        @endif
                    @else
                        <a href="{{ route('member.portal') }}" class="{{ Request::is('member/portal') ? 'active' : '' }}">
                            <i class="fa-solid fa-id-card-clip"></i> Portal
                        </a>
                    @endif

                    <form method="GET" action="{{ route('logout') }}" id="logout-form" style="display: none;">
                        @csrf
                    </form>
                    <a href="{{ route('logout') }}" class="logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT AREA -->
    <main class="main">
        @yield('content')
    </main>

    <script>
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-collapsed');
            const isCollapsed = document.body.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarState', isCollapsed ? 'collapsed' : 'expanded');
        }

        function toggleDropdown() {
            document.getElementById("myDropdown").classList.toggle("show");
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('sidebarState') === 'collapsed') {
                document.body.classList.add('sidebar-collapsed');
            }
        });

        window.onclick = function(event) {
            if (!event.target.closest('.dropdown-trigger')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    if (dropdowns[i].classList.contains('show')) dropdowns[i].classList.remove('show');
                }
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>