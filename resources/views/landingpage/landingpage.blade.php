<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cornerstone Community Church | Welcome</title>
    
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        html { scroll-behavior: smooth; }
        :root { --gold: #d4af37; --navy: #0f172a; }
        .reveal { opacity: 0; transform: translateY(40px); transition: all 1.2s ease-out; }
        .reveal.active { opacity: 1; transform: translateY(0); }
        #dynamic-verse { transition: opacity 0.8s ease-in-out; min-height: 3.5em; display: block; }
        .nav-links a { cursor: pointer; }
    </style>
</head>
<body>

    <nav>
        <div class="logo-area">
            <img src="{{ asset('images/logo.png') }}" class="logo-img" alt="Cornerstone Logo">
            <h2 class="logo-text">CORNERSTONE</h2>
        </div>

        <div class="nav-links">
            <a href="#home">Home</a>
            <a href="#about">Our Mission</a>
            <a href="#events">Events</a>
            <a href="#giving-impact">Giving Impact</a>
            <a href="#connect">Connect</a>
            <a href="/login" class="btn-portal">Sign in</a>
        </div>
    </nav>

    <header class="hero" id="home">
        <div class="hero-content">
            <span class="mission-tag">The Great Commission</span>
            <h1>BUILD YOUR LIFE ON THE <span style="color: var(--gold)">ROCK</span></h1>
            <p id="dynamic-verse">"Go into all the world and proclaim the gospel to the whole creation." <br>— Mark 16:15</p>

            <div class="hero-btns">
                <a href="/register" class="btn-main">Join Our Community</a>
                <a href="/login" class="btn-portal hero-portal-mobile">Sign in</a>
            </div>
        </div>
    </header>

    <section class="features reveal">
        <div class="feature-card"><i class="fa-solid fa-cross"></i><h3>Worship</h3><p>Gather to honor God through song and scripture.</p></div>
        <div class="feature-card"><i class="fa-solid fa-hands-holding-child"></i><h3>Community</h3><p>Faith grows best in authentic relationships.</p></div>
        <div class="feature-card"><i class="fa-solid fa-earth-asia"></i><h3>Missions</h3><p>Spreading light and love to the ends of the earth.</p></div>
    </section>

    <section class="about-mission reveal" id="about">
        <div class="about-text">
            <span style="color: var(--gold); font-weight: 800; font-size: 0.9rem;">WHO WE ARE</span>
            <h2>Rooted in Faith, <br>Reaching the World.</h2>
            <div class="divider" style="margin: 0 0 20px 0; width: 50px; height: 3px; background: var(--gold);"></div>
            <p>Cornerstone Community Church is a family built on Jesus Christ.</p>
        </div>
        <div class="about-image"><img src="{{ asset('images/church1.jpg') }}" alt="Our Community" id="churchImage" style="cursor: zoom-in;"></div>
    </section>

    <!-- AGENDA LIST: Matches Image 2 -->
    <section class="landing-events reveal" id="events" style="background: #fff;">
        <div class="section-header">
            <h2>Upcoming Events</h2>
            <div class="divider"></div>
            <p style="color: #64748b; margin-top: 15px;">Join us in our upcoming activities and services.</p>
        </div>
        <div class="agenda-container">
            @forelse($events as $event)
                <div class="agenda-item">
                    <div class="agenda-date">
                        <span class="day">{{ \Carbon\Carbon::parse($event->Event_Date)->format('d') }}</span>
                        <span class="month">{{ \Carbon\Carbon::parse($event->Event_Date)->format('M') }}</span>
                    </div>
                    <div class="agenda-info">
                        <span class="agenda-badge">{{ $event->category->Event_Category_Name ?? 'General' }}</span>
                        <h3>{{ $event->Title }}</h3>
                        <p><i class="fa-solid fa-location-dot"></i> {{ $event->Location }} • <i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($event->Event_Time)->format('h:i A') }}</p>
                        <a href="{{ route('register') }}" class="agenda-btn">Join</a>
                    </div>
                </div>
            @empty
                <p style="text-align: center; color: #94a3b8; width: 100%;">No upcoming events scheduled.</p>
            @endforelse
        </div>
    </section>

    <!-- GIVING IMPACT -->
    <section class="giving-impact reveal" id="giving-impact" style="background: #f8fafc;">
        <div class="section-header">
            <h2>Where Your Giving Goes</h2>
            <div class="divider"></div>
            <p style="color: #64748b; margin-top: 15px;">Transparency is our priority based on our financial records.</p>
        </div>
        <div class="transparency-grid">
            @forelse($impactData as $data)
                <div class="transparency-card">
                    <h3>{{ $data->Category_Name }}</h3>
                    <div class="percentage-wrapper"><div class="percentage-bar" style="width: {{ $data->percentage }}%;">{{ $data->percentage }}%</div></div>
                </div>
            @empty
                <p style="text-align: center; width: 100%; color: #94a3b8;">Data is being updated.</p>
            @endforelse
        </div>
    </section>

    <div id="imageLightbox" class="lightbox-overlay"><span class="lightbox-close"></span><img class="lightbox-content" id="imgFull"></div>

    <section class="connect reveal" id="connect">
        <div class="section-header"><h2>Take Your Next Step</h2><div class="divider"></div></div>
        <div class="connect-grid">
            <div class="connect-card"><i class="fa-solid fa-users-viewfinder"></i><h4>Small Groups</h4></div>
            <div class="connect-card"><i class="fa-solid fa-heart-pulse"></i><h4>Youth Ministry</h4></div>
            <div class="connect-card"><i class="fa-solid fa-hand-holding-heart"></i><h4>Serve</h4></div>
            <div class="connect-card"><i class="fa-solid fa-gift"></i><h4>Online Giving</h4></div>
        </div>
    </section>

    <footer>
        <img src="{{ asset('images/logo.png') }}" class="footer-logo" alt="Cornerstone">
        <p>&copy; {{ date('Y') }} Cornerstone Community Church.</p>
    </footer>

    <script>
        const verses = [
            { text: "Go into all the world and proclaim the gospel to the whole creation.", ref: "Mark 16:15" },
            { text: "For God so loved the world, that he gave his only Son.", ref: "John 3:16" },
            { text: "The Lord is my shepherd; I shall not want.", ref: "Psalm 23:1" }
        ];
        let verseIndex = 0;
        setInterval(() => {
            const el = document.getElementById('dynamic-verse');
            el.style.opacity = 0;
            setTimeout(() => {
                verseIndex = (verseIndex + 1) % verses.length;
                el.innerHTML = `"${verses[verseIndex].text}" <br>— ${verses[verseIndex].ref}`;
                el.style.opacity = 1;
            }, 800);
        }, 7000);

        function reveal() {
            document.querySelectorAll(".reveal").forEach(r => {
                if (r.getBoundingClientRect().top < window.innerHeight - 150) r.classList.add("active");
            });
        }
        window.onscroll = reveal; reveal();

        const lightbox = document.getElementById("imageLightbox");
        document.getElementById("churchImage").onclick = function() { lightbox.style.display = "block"; document.getElementById("imgFull").src = this.src; }
        document.querySelector(".lightbox-close").onclick = () => lightbox.style.display = "none";
    </script>
</body>
</html>