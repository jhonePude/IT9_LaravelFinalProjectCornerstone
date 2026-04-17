@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <!-- ================= SKELETON STATE (Shown while loading) ================= -->
    <div id="skeleton-dashboard">
        <div class="user-title" style="background: transparent; border: none;">
            <div class="skeleton skeleton-title" style="margin-top: 30px; margin-left: 20px;"></div>
        </div>
        
        <div class="cards-container">
            <div class="skeleton skeleton-card"></div>
            <div class="skeleton skeleton-card"></div>
            <div class="skeleton skeleton-card"></div>
        </div>

        <div class="chart-section">
            <div class="skeleton skeleton-chart"></div>
        </div>
    </div>

    <!-- ================= ACTUAL DASHBOARD CONTENT ================= -->
    <div id="main-dashboard-content">
        <!-- Header Text -->
        <div class="user-title"><p>Admin Dashboard</p></div>
        <div class="user-saying"><p>Welcome back! Administrator</p></div>

        <!-- Stats Cards -->
        <div class="cards-container">
            <div class="total-members-card">
                <div class="card-header">
                    <p class="label">Total Members</p>
                    <i class="fa-solid fa-users icon" style="color: #70bc0c;"></i>
                </div>
                <h2 style="font-size: 28px; margin: 0;">{{ $stats['total_members'] }}</h2>
                <p class="trend positive"><i class="fa-solid fa-arrow-up"></i> Active Users</p>
            </div>


            <div class="upcoming-event-card">
                <div class="card-header">
                    <p class="label">Upcoming Events</p>
                    <i class="fa-solid fa-calendar-check icon" style="color: #ae4143;"></i>
                </div>
                <h2 style="font-size: 28px; margin: 0;">{{ $stats['upcoming_events'] }}</h2>
                <p class="trend" style="color: #64748b;">Scheduled Activities</p>
            </div>
            
            <div class="total-members-card" style="border-left: 5px solid #d4af37;">
                <div class="card-header">
                    <p class="label">Total Balance</p>
                    <i class="fa-solid fa-wallet icon" style="color: #d4af37;"></i>
                </div>
                <h2 style="font-size: 28px; margin: 0;">₱{{ $stats['total_balance'] }}</h2>
                <p class="trend" style="color: #64748b;">Net Funds</p>
            </div>
        </div>

        <!-- 1. Attendance Chart (Wave Style with Event Dots) -->
        <div class="chart-section">
            <div class="chart-card">
                <div class="chart-header-text">
                    <h2>Attendance Trends</h2>
                    <p>Registration counts for the last 8 events</p>
                </div>
                <div class="chart-wrapper">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 2. Income vs Expenses Chart -->
        <div class="income-expenses-section">
            <div class="chart-card">
                <div class="chart-header-text">
                    <h2>Financial Overview</h2>
                    <p>Income vs Expenses for the last 6 months</p>
                </div>
                <div class="chart-wrapper">
                    <canvas id="financeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            document.getElementById('skeleton-dashboard').style.display = 'none';
            document.getElementById('main-dashboard-content').style.display = 'block';
            renderCharts();
        }, 1500);

        function renderCharts() {
            // 1. ATTENDANCE CHART (LIGHT BLUE WAVE WITH DOTS)
            const attCtx = document.getElementById('attendanceChart').getContext('2d');
            
            const blueGradient = attCtx.createLinearGradient(0, 0, 0, 400);
            blueGradient.addColorStop(0, 'rgba(96, 165, 250, 0.4)'); 
            blueGradient.addColorStop(1, 'rgba(96, 165, 250, 0.0)'); 

            new Chart(attCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($eventLabels) !!},
                    datasets: [{
                        label: 'Attendees',
                        data: {!! json_encode($eventCounts) !!},
                        borderColor: '#60a5fa',
                        borderWidth: 3,
                        backgroundColor: blueGradient,
                        fill: true,
                        tension: 0.4, 
                        // DOTS FOR EVERY EVENT
                        pointRadius: 6, 
                        pointBackgroundColor: '#ffffff', // White center
                        pointBorderColor: '#60a5fa',     // Blue border for dot
                        pointBorderWidth: 2,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#60a5fa',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            grid: { display: false }, 
                            border: { display: false },
                            ticks: { color: '#94a3b8' }
                        },
                        y: {
                            grid: { display: false }, 
                            border: { display: false },
                            ticks: { display: false } 
                        }
                    }
                }
            });

            // 2. FINANCIAL CHART
            const finCtx = document.getElementById('financeChart').getContext('2d');
            new Chart(finCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($months) !!},
                    datasets: [
                        {
                            label: 'Income',
                            data: {!! json_encode($incomeData) !!},
                            backgroundColor: '#0c20bc',
                            borderRadius: 5
                        },
                        {
                            label: 'Expenses',
                            data: {!! json_encode($expenseData) !!},
                            backgroundColor: '#ae4143',
                            borderRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    });

    function toggleDropdown() {
        document.getElementById("myDropdown").classList.toggle("show");
    }
    window.onclick = function(event) {
        if (!event.target.closest('.dropdown-trigger')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                if (dropdowns[i].classList.contains('show')) dropdowns[i].classList.remove('show');
            }
        }
    }
</script>
@endpush