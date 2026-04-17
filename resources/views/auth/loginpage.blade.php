<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Cornerstone Community Church</title>
     <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=3">   
     <link rel="stylesheet" href="{{ asset('css/loginpage.css') }}"> 

     <link rel="stylesheet" href="/images/logo.png?v=3">
     <link rel="stylesheet" href="/css/loginpage.css">

     <link rel="icon" href="images/logo.png?v=3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <div class="login-card">
      
        <div class="image-side">
            <div class="side-brand-content">
                
                <span class="mission-tag">The Great Commission</span>
                <h1 class="side-title">CORNERSTONE</h1>
                <p class="bible-verse">
                    "Go into all the world and proclaim the gospel to the whole creation."
                    <span class="verse-ref">Mark 16:15</span>
                </p>
                <div class="gold-line"></div>
            </div>

            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="main-logo">
        </div>
        
        <div class="form-side" >
            <div class="header-text">
                <h2>Cornerstone <br> <span class="accent-text">Community Church</span></h2>
                <p class="subtitle">Welcome to our Faith Community!</p>
                <hr class="divider-line">
            </div>

            <form id ="inputsform" action="/login" method="POST" onsubmit="return showLoading()">
                @csrf 

                @if($errors->any())
                    <div class="error-msg" style="display: block; color: #d93025; margin-top: -40px; padding-bottom: 20px; font-weight: 600">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
                    </div>
                @endif

                <div class="input-group">
                    <label>Email</label>
                    <input type="text" id="email" name="email" placeholder="Enter email address" required
                    oninvalid= "if(this.validity.valueMissing)
                                {
                                    this.setCustomValidity('Please fill up all fields.')
                                } 
                            " oninput="this.setCustomValidity('')">
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="Create a password" autocomplete="off" required>
                        <i class="fa-solid fa-eye" id="togglePassword"></i>
                    </div>
                </div>
                
                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>

                <button type="submit" name = "submit_btn" class="btn-signin">Sign in</button>
                
                <hr class="divider-line">

                <button type="button" class="btn-google" id="googleBtn">
                    <img src="https://thumbs.dreamstime.com/b/google-logo-white-background-vector-format-available-google-logo-124289805.jpg" alt="Google">
                    Sign in with Google
                </button>
            </form>

            <p class="signup-text">
                New to our community? <a href="/register">Register now</a>
            </p>
        </div>
    </div>

    <!-- LOADING SCREEN EFFECTS -->
    <div id="loader-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); z-index: 10000; justify-content: center; align-items: center; transition: opacity 0.5s ease;">
    <div id="loader-card" style="text-align: center; max-width: 300px;">
        <div id="visual-box" style="margin-bottom: 20px;">
            <i class="fa-solid fa-cross" style="font-size: 40px; color: #d4af37; animation: breathe 2s ease-in-out infinite;"></i>
        </div>
        <div id="success-icon" style="display: none; margin-bottom: 20px; animation: fadeIn 0.8s ease;">
            <i class="fa-solid fa-check" style="font-size: 40px; color: #d4af37;"></i>
        </div>
        <h3 id="loader-text" style="margin: 0; color: #1e293b; font-family: 'Poppins', sans-serif; font-size: 1.2rem; font-weight: 500; letter-spacing: 1px;">Preparing the way...</h3>
        <p id="loader-sub" style="margin: 10px 0 0; color: #94a3b8; font-family: 'Poppins', sans-serif; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 2px;">Cornerstone Community</p>
    </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function () {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    });

    function showLoading() {
        const overlay = document.getElementById('loader-overlay');
        const visual = document.getElementById('visual-box');
        const success = document.getElementById('success-icon');
        const text = document.getElementById('loader-text');
        const subtext = document.getElementById('loader-sub');
        const form = document.getElementById('inputsform');

        overlay.style.display = 'flex';
        overlay.style.opacity = '0';
        setTimeout(() => overlay.style.opacity = '1', 10); 

        setTimeout(() => {
            visual.style.display = 'none';
            success.style.display = 'block';
            text.innerText = "Peace be with you";
            subtext.innerText = "Entering the Sanctuary...";
            text.style.color = "#d4af37";
            setTimeout(() => { form.submit(); }, 1200);
        }, 2000);
        return false; 
    }

    const googleBtn = document.getElementById('googleBtn');
    if (googleBtn) {
        googleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const loaderOverlay = document.getElementById('loader-overlay');
            loaderOverlay.style.display = 'flex';
            loaderOverlay.style.opacity = '1';
            document.getElementById('loader-text').innerText = "Authenticating via Google...";
            setTimeout(() => { window.location.href = "{{ url('/auth/google') }}"; }, 1200);
        });
    }
</script>
</body>
</html>