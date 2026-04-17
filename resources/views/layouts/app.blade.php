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
<body class=""> <!-- JavaScript will add 'sidebar-collapsed' here -->

    <!-- SIDENAV (Midnight Navy Theme) -->
    <nav class="sidenav" id="mySidenav">
        <!-- Sidebar Header (Logo & Title) -->
        <div class="sidenav-header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="side-logo">
            <h1 class="side-title">CORNERSTONE</h1>
            <hr class="gold-divider">
            
            <!-- Hamburger Button for Desktop Collapse -->
            <button class="collapse-toggle" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <!-- Sidebar Menu -->
        <div class="sidenav-menu">
            {{-- ISSUE 1 FIX: Subsystems only visible when NOT in Portal mode --}}
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

        <!-- Floating Magic Indicator (Mobile Only) -->
        <div class="floating-indicator" id="indicator"></div>

        <!-- Sidebar Footer (Profile & Dropdown) -->
        <div class="sidenav-footer">
            <div class="dropdown-trigger" onclick="toggleDropdown()">
                <div class="user-profile-info">
                    <img src="/images/{{ Auth::user()->Profile_Picture }}" class="user-avatar-small">
                    <span class="user-name-small">{{ Auth::user()->Fullname }}</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>
            
            <div id="myDropdown" class="dropdown-content">
                {{-- ISSUE 2 FIX: Dynamic Link Switching between Portal and Dashboard --}}
                @if(Request::is('member/portal*'))
                    <a href="{{ route('dashboard') }}">
                        <i class="fa-solid fa-chart-line"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('member.portal') }}" class="{{ Request::is('member/portal') ? 'active' : '' }}">
                        <i class="fa-solid fa-id-card-clip"></i> Portal
                    </a>
                @endif

                {{-- Breeze Logout Fix: Requires Form POST --}}
                <form method="GET" action="{{ route('logout') }}" id="logout-form" style="display: none;">
                    @csrf
                </form>
                <a href="{{ route('logout') }}" class="logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT AREA -->
    <main class="main">
        @yield('content')
    </main>

    <!-- Scripts for theme logic -->
    <script>
        // 1. Sidebar Collapse Logic (Desktop)
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-collapsed');
            
            // Save state to local storage so it stays collapsed on refresh
            const isCollapsed = document.body.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarState', isCollapsed ? 'collapsed' : 'expanded');
        }

        // 2. Profile Dropdown Logic
        function toggleDropdown() {
            document.getElementById("myDropdown").classList.toggle("show");
        }

        // 3. Mobile Magic Indicator Positioning
        document.addEventListener('DOMContentLoaded', function() {
            // Restore sidebar state
            if (localStorage.getItem('sidebarState') === 'collapsed') {
                document.body.classList.add('sidebar-collapsed');
            }

            const activeLink = document.querySelector('.sidenav-menu a.active');
            const indicator = document.getElementById('indicator');
            
            if (activeLink && indicator && window.innerWidth <= 768) {
                // Initial indicator position
                const moveIndicator = () => {
                    const rect = activeLink.getBoundingClientRect();
                    indicator.style.transform = `translateX(${rect.left}px)`;
                    indicator.style.width = `${rect.width}px`;
                };
                moveIndicator();
                window.addEventListener('resize', moveIndicator);
            }
        });

        // Close dropdown if clicked outside
        window.onclick = function(event) {
            if (!event.target.closest('.dropdown-trigger')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>