<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cornerstone Community Church | Welcome</title>
    

    <!-- External CSS -->
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="stylesheet" href="/css/landingpage.css">
    <link rel="icon" href="/images/logo.png" type="image/png">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        /* Smooth Scroll & Transition Styles */
        html {
            scroll-behavior: smooth;
        }

        :root {
            --gold: #d4af37;
            --navy: #0f172a;
        }

        /* Initial state for scroll animation */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 1.2s ease-out;
        }

        /* Visible state for scroll animation */
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Hero Verse Transition */
        #dynamic-verse {
            transition: opacity 0.8s ease-in-out;
            min-height: 3.5em; /* Prevents layout jumping */
            display: block;
        }

        /* Ensure smooth navigation links */
        .nav-links a {
            cursor: pointer;
        }
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
            
            <!-- Target for the 7-second verse rotation -->
            <p id="dynamic-verse">"Go into all the world and proclaim the gospel to the whole creation." <br>— Mark 16:15</p>

            <div class="hero-btns">
                <a href="/register" class="btn-main">Join Our Community</a>
                <a href="/login" class="btn-portal hero-portal-mobile">Sign in</a>
            </div>
        </div>
    </header>

    <!-- Section 1: Features (Added .reveal) -->
    <section class="features reveal">
        <div class="feature-card">
            <i class="fa-solid fa-cross"></i>
            <h3>Worship</h3>
            <p>Experience a vibrant atmosphere where we gather to honor God through song and scripture.</p>
        </div>
        <div class="feature-card">
            <i class="fa-solid fa-hands-holding-child"></i>
            <h3>Community</h3>
            <p>Find your tribe. We believe faith grows best in the context of authentic relationships.</p>
        </div>
        <div class="feature-card">
            <i class="fa-solid fa-earth-asia"></i>
            <h3>Missions</h3>
            <p>Our call is global. We are dedicated to spreading light and love to the ends of the earth.</p>
        </div>
    </section>

    <!-- Section 2: About Mission (Added .reveal) -->
    <section class="about-mission reveal" id="about">
        <div class="about-text">
            <span style="color: var(--gold); font-weight: 800; font-size: 0.9rem;">WHO WE ARE</span>
            <h2>Rooted in Faith, <br>Reaching the World.</h2>
            <div class="divider" style="margin: 0 0 20px 0; width: 50px; height: 3px; background: var(--gold);"></div>
            <p>At Cornerstone Community Church, we aren't just a building; we are a family. Based on the foundation of Jesus Christ, we strive to create an environment where the broken find healing and the searching find purpose.</p>
            <p>Inspired by the Great Commission, we are committed to making disciples, baptizing them, and teaching them the ways of the Lord.</p>
        </div>
        <div class="about-image">
            <!-- Added ID and inline style for cursor -->
            <img src="{{ asset('images/church1.jpg') }}" 
                alt="Our Community" 
                id="churchImage" 
                style="cursor: zoom-in; width: 100%; display: block;">
        </div>
    </section>

    <!-- REDESIGNED SECTION: Agenda List -->
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
                    </div>
                    <div class="agenda-action">
                        <a href="{{ route('register') }}" class="agenda-btn">Join</a>
                    </div>
                </div>
            @empty
                <p style="text-align: center; color: #94a3b8; width: 100%;">No upcoming events scheduled.</p>
            @endforelse
        </div>
    </section>

    <!-- GIVING IMPACT SECTION (Real Percentages) -->
    <section class="giving-impact reveal" id="giving-impact" style="background: #f8fafc;">
        <div class="section-header">
            <h2>Where Your Giving Goes</h2>
            <div class="divider"></div>
            <p style="color: #64748b; margin-top: 15px;">Transparency is our priority. Here is how your offerings are used based on our recent financial records.</p>
        </div>
        <div class="transparency-grid">
            @forelse($impactData as $data)
                <div class="transparency-card">
                    <div class="transparency-icon">
                        @if(Str::contains($data->Category_Name, 'Mission')) <i class="fa-solid fa-earth-americas"></i>
                        @elseif(Str::contains($data->Category_Name, 'Util')) <i class="fa-solid fa-church"></i>
                        @elseif(Str::contains($data->Category_Name, 'Youth')) <i class="fa-solid fa-child-reaching"></i>
                        @else <i class="fa-solid fa-hand-holding-heart"></i> @endif
                    </div>
                    <h3>{{ $data->Category_Name }}</h3>
                    <div class="percentage-wrapper">
                        <div class="percentage-bar" style="width: {{ $data->percentage }}%;">{{ $data->percentage }}%</div>
                    </div>
                    <p style="margin-top: 10px; font-size: 12px; color: #64748b;">Allocation for church {{ strtolower($data->Category_Name) }}</p>
                </div>
            @empty
                <p style="text-align: center; width: 100%; color: #94a3b8;">Financial updates are being calculated.</p>
            @endforelse
        </div>
    </section>

    <!-- PLACE THIS AT THE VERY BOTTOM OF YOUR index.blade.php (Before <footer>) -->
    <div id="imageLightbox" class="lightbox-overlay">
        <span class="lightbox-close"></span>
        <img class="lightbox-content" id="imgFull">
        <div id="caption">Cornerstone Community Church</div>
    </div>

    <!-- Section 3: Connect (Added .reveal) -->
    <section class="connect reveal" id="connect">
        <div class="section-header">
            <h2>Take Your Next Step</h2>
            <div class="divider"></div>
            <p style="color: #64748b; margin-top: 15px;">There is a place for you here at Cornerstone.</p>
        </div>
        <div class="connect-grid">
            <div class="connect-card">
                <i class="fa-solid fa-users-viewfinder"></i>
                <h4>Small Groups</h4>
                <p>Join a circle of friends to study the Word and share life's journey.</p>
            </div>
            <div class="connect-card">
                <i class="fa-solid fa-heart-pulse"></i>
                <h4>Youth Ministry</h4>
                <p>Empowering the next generation to lead with faith and courage.</p>
            </div>
            <div class="connect-card">
                <i class="fa-solid fa-hand-holding-heart"></i>
                <h4>Serve</h4>
                <p>Use your unique talents to serve the church and our local community.</p>
            </div>
            <div class="connect-card">
                <i class="fa-solid fa-gift"></i>
                <h4>Online Giving</h4>
                <p>Support our mission conveniently through our secure portal.</p>
            </div>
        </div>
    </section>

    <footer>
        <img src="{{ asset('images/logo.png') }}" class="footer-logo" alt="Cornerstone">
        <p>&copy; {{ date('Y') }} Cornerstone Community Church. <br> Built for The Great Commission.</p>
    </footer>


</body>
</html>

<script>

        // 2. BIBLE VERSE ROTATION (7 Seconds)
        const verses = [
            { text: "Go into all the world and proclaim the gospel to the whole creation.", ref: "Mark 16:15" },
            { text: "For God so loved the world, that he gave his only Son.", ref: "John 3:16" },
            { text: "But seek first the kingdom of God and his righteousness.", ref: "Matthew 6:33" },
            { text: "The Lord is my shepherd; I shall not want.", ref: "Psalm 23:1" },
            { text: "I can do all things through Christ who strengthens me.", ref: "Philippians 4:13" },
            { text: "Let everything that has breath praise the Lord.", ref: "Psalm 150:6" },
            { text: "Trust in the Lord with all your heart and lean not on your own understanding.", ref: "Proverbs 3:5" }
        ];

        let verseIndex = 0;
        function rotateVerse() {
            const verseElement = document.getElementById('dynamic-verse');
            
            // Fade out
            verseElement.style.opacity = 0;

            setTimeout(() => {
                // Change Content
                verseIndex = (verseIndex + 1) % verses.length;
                const nextVerse = verses[verseIndex];
                verseElement.innerHTML = `"${nextVerse.text}" <br>— ${nextVerse.ref}`;
                
                // Fade in
                verseElement.style.opacity = 1;
            }, 800); 
        }

        setInterval(rotateVerse, 7000);

        // 3. SCROLL REVEAL ANIMATION
        function revealSections() {
            const reveals = document.querySelectorAll(".reveal");

            for (let i = 0; i < reveals.length; i++) {
                const windowHeight = window.innerHeight;
                const elementTop = reveals[i].getBoundingClientRect().top;
                const elementVisible = 150; // Trigger point

                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add("active");
                }
            }
        }

        // Run on scroll
        window.addEventListener("scroll", revealSections);

        // Run once on load
        document.addEventListener("DOMContentLoaded", revealSections);



        // --- 4. IMAGE POPUP LOGIC ---
        const lightbox = document.getElementById("imageLightbox");
        const churchImg = document.getElementById("churchImage");
        const fullImg = document.getElementById("imgFull");
        const closeBtn = document.querySelector(".lightbox-close");

        // Open Popup
        churchImg.onclick = function() {
            lightbox.style.display = "block";
            fullImg.src = this.src;
            document.body.style.overflow = "hidden"; // Stop scrolling when viewing image
            
        }
        
        // Close via X button
        closeBtn.onclick = function() {
            closeLightbox();
            
        }

        // Close via clicking background
        lightbox.onclick = function(e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        }

        // Close via Escape Key
        document.addEventListener('keydown', (e) => {
            if (e.key === "Escape") {
                closeLightbox();
            }
        });

        function closeLightbox() {
            lightbox.style.display = "none";
            document.body.style.overflow = "auto"; // Re-enable scrolling
        }
</script>