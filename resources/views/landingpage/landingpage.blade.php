<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Cornerstone Community Church | Welcome</title>
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}?v=1.1">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v=1.1">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        html { scroll-behavior: smooth; }
        #dynamic-verse { transition: opacity 0.8s ease-in-out; min-height: 3.5em; display: block; }
    </style>
</head>
<body>

    <!-- Header: Only logo, no navigation links -->
    <nav>
        <div class="logo-area">
            <img src="{{ asset('images/logo.png') }}" class="logo-img" alt="Cornerstone Logo">
            <h2 class="logo-text">CORNERSTONE</h2>
        </div>
    </nav>

    <header class="hero" id="home">
        <div class="hero-content">
            <span class="mission-tag">The Great Commission</span>
            <h1>BUILD YOUR LIFE ON THE <span style="color: var(--gold)">ROCK</span></h1>
            <p id="dynamic-verse">"Go into all the world and proclaim the gospel to the whole creation." <br>— Mark 16:15</p>
            <div class="hero-btns">
                <a href="/register" class="btn-main">Join Our Community</a>
                <a href="/login" class="hero-signin-btn">Sign in</a>
            </div>
        </div>
    </header>

    <section class="features reveal">
        <div class="feature-card"><i class="fa-solid fa-cross"></i><h3>Worship</h3><p>Experience a vibrant atmosphere where we gather to honor God.</p></div>
        <div class="feature-card"><i class="fa-solid fa-hands-holding-child"></i><h3>Community</h3><p>Find your tribe. We believe faith grows best in authentic relationships.</p></div>
        <div class="feature-card"><i class="fa-solid fa-earth-asia"></i><h3>Missions</h3><p>Our call is global. We spread light and love to the ends of the earth.</p></div>
    </section>

    <section class="about-mission reveal" id="about">
        <div class="about-text">
            <span style="color: var(--gold); font-weight: 800; font-size: 0.9rem;">WHO WE ARE</span>
            <h2>Rooted in Faith, <br>Reaching the World.</h2>
            <div class="divider" style="margin: 0 0 20px 0; width: 50px; height: 3px; background: var(--gold);"></div>
            <p>At Cornerstone Community Church, we are a family based on the foundation of Jesus Christ.</p>
        </div>
        <div class="about-image"><img src="{{ asset('images/church1.jpg') }}" alt="Our Community" id="churchImage" style="cursor: zoom-in;"></div>
    </section>

    <!-- UPCOMING EVENTS -->
    <section class="landing-events reveal" id="events" style="background: #fff;">
        <div class="section-header"><h2>Upcoming Events</h2><div class="divider"></div></div>
        <div class="events-list-container">
            @forelse($events as $event)
                <div class="event-list-row">
                    <div class="event-date-col">
                        <span class="event-day">{{ \Carbon\Carbon::parse($event->Event_Date)->format('d') }}</span>
                        <span class="event-month">{{ \Carbon\Carbon::parse($event->Event_Date)->format('M') }}</span>
                    </div>
                    <div class="event-text-col">
                        <span class="event-tag-gold">{{ $event->category->Event_Category_Name ?? 'General' }}</span>
                        <h3>{{ $event->Title }}</h3>
                    </div>
                    <div class="event-btn-col">
                        <a href="{{ route('register') }}" class="btn-join-list">Join Event</a>
                    </div>
                </div>
            @empty
                <p style="text-align: center; width: 100%; color: #94a3b8;">No upcoming events.</p>
            @endforelse
        </div>
    </section>

    <!-- GIVING IMPACT -->
    <section class="giving-impact reveal" id="giving-impact" style="background: #fdfdfd;">
        <div class="section-header">
            <h2>Where Your Giving Goes</h2>
            <div class="divider"></div>
            <p style="color: #64748b; margin-top: 15px;">Transparency in our ministry finances and how we use God's resources.</p>
        </div>
        
        <div class="giving-loading-card">
            <div class="giving-loading-group">
                <div class="loading-label"><span>Total General Income</span> <strong>₱{{ number_format($totalIncome, 2) }}</strong></div>
                <div class="loading-bar-bg"><div class="loading-bar-fill income" style="width: 100%"></div></div>
            </div>
            
            <div class="giving-loading-group">
                <div class="loading-label"><span>Total Ministry Expenses</span> <strong>₱{{ number_format($totalExpenses, 2) }}</strong></div>
                <div class="loading-bar-bg"><div class="loading-bar-fill expense" style="width: {{ $expensePercentage }}%"></div></div>
            </div>

            <div class="expense-breakdown-area">
                <p>Current Expense Allocation</p>
                @foreach($impactData as $data)
                <div class="breakdown-mini">
                    <div class="breakdown-info"><span>{{ $data->Category_Name }}</span> <span>{{ $data->percentage }}%</span></div>
                    <div class="loading-bar-bg small"><div class="loading-bar-fill gold" style="width: {{ $data->percentage }}%"></div></div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Lightbox -->
    <div id="imageLightbox" class="lightbox-overlay"><span class="lightbox-close"></span><img class="lightbox-content" id="imgFull"></div>

    <!-- NEXT STEPS -->
    <section class="connect reveal" id="connect">
        <div class="section-header"><h2>Take Your Next Step</h2><div class="divider"></div></div>
        <div class="connect-grid">
            <div class="connect-card">
                <i class="fa-solid fa-users-viewfinder"></i>
                <h4>Small Groups</h4>
                <p>Build lasting friendships while growing deeper in God's word together.</p>
            </div>
            <div class="connect-card">
                <i class="fa-solid fa-heart-pulse"></i>
                <h4>Youth Ministry</h4>
                <p>Inspiring the next generation to lead lives rooted in faith and purpose.</p>
            </div>
            <div class="connect-card">
                <i class="fa-solid fa-hand-holding-heart"></i>
                <h4>Serve</h4>
                <p>Make a difference by using your God-given talents to serve our community.</p>
            </div>
            <div class="connect-card">
                <i class="fa-solid fa-gift"></i>
                <h4>Online Giving</h4>
                <p>Support our global mission safely and securely from wherever you are.</p>
            </div>
        </div>
    </section>

    <footer>
        <img src="{{ asset('images/logo.png') }}" class="footer-logo">
        <p>&copy; {{ date('Y') }} Cornerstone Community Church.</p>
    </footer>

    <script>
        // Dynamic Bible verses
        const verses = [
            { text: "Go into all the world and proclaim the gospel to the whole creation.", ref: "Mark 16:15" },
            { text: "For God so loved the world, that he gave his only Son.", ref: "John 3:16" },
            { text: "The Lord is my shepherd; I shall not want.", ref: "Psalm 23:1" }
        ];
        let verseIndex = 0;
        setInterval(() => {
            const el = document.getElementById('dynamic-verse');
            if (el) {
                el.style.opacity = 0;
                setTimeout(() => {
                    verseIndex = (verseIndex + 1) % verses.length;
                    el.innerHTML = `"${verses[verseIndex].text}" <br>— ${verses[verseIndex].ref}`;
                    el.style.opacity = 1;
                }, 800);
            }
        }, 7000);

        // Scroll reveal
        function reveal() {
            document.querySelectorAll(".reveal").forEach(r => {
                if (r.getBoundingClientRect().top < window.innerHeight - 150) {
                    r.classList.add("active");
                }
            });
        }
        window.addEventListener("scroll", reveal);
        reveal();

        // Lightbox functionality
        const lightbox = document.getElementById("imageLightbox");
        const churchImage = document.getElementById("churchImage");
        if (churchImage) {
            churchImage.onclick = function() { 
                lightbox.style.display = "block"; 
                document.getElementById("imgFull").src = this.src; 
            };
        }
        const closeBtn = document.querySelector(".lightbox-close");
        if (closeBtn) {
            closeBtn.onclick = () => lightbox.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == lightbox) {
                lightbox.style.display = "none";
            }
        };
    </script>
</body>
</html>
