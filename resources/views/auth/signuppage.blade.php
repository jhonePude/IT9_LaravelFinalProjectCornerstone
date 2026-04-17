<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Cornerstone Community Church</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=3"> 
    <link rel="stylesheet" href="/css/signuppage.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/signuppage.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

    <div class="signup-card">
        <div class="image-side">
              <div class="side-brand-content">
                <span class="mission-tag">The Great Commission</span>
                <h1 class="side-title">CORNERSTONE</h1>
                <p class="bible-verse">"Go into all the world and proclaim the gospel to the whole creation."<span class="verse-ref">Mark 16:15</span></p>
                <div class="gold-line"> </div>
            </div>
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="main-logo">
        </div>

        <div class="form-side">
            <div class="header-text">
                <span class="church-badge">Join Us</span>
                <h2>Create <span class="accent-text">Account</span></h2>
                <p class="subtitle">Let us grow together in faith.</p>
            </div>
            
            <form id= "inputsform" action="/register" method="POST" onsubmit="return showLoading()">
                @csrf
                <div class="input-group">
                    <label>Fullname</label>
                    <input type="text" name="fullname" placeholder="Enter fullname" required>
                </div>
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Enter username" required>
                </div>
                <div class="input-group">
                    <label>Email</label>
                    <input type="text" name="email" placeholder="example@gmail.com" pattern="[a-zA-Z0-9.]+@gmail\.com" required>
                </div>
                <div class="input-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="" disabled selected>Select your gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="others">Others</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Contact Number</label>
                    <input type="text" name="contact" placeholder="Enter contact number" required pattern="[0-9]{11}">
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="Create a password" required minlength="7">
                        <i class="fa-solid fa-eye" id="togglePassword"></i>
                    </div>
                </div>
                <button type="submit" name="register_btn" class="btn-signin">Sign Up</button>
            </form>
            <p class="signup-text">Already have an account? <a href="/login">Log in here</a></p>
        </div>
    </div>

    <!-- LOADING SCREEN -->
    <div id="loader-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.5); backdrop-filter: blur(5px); z-index: 10000; justify-content: center; align-items: center;">
    <div id="loader-card" style="background: white; padding: 30px; border-radius: 20px; text-align: center; width: 270px; border-bottom: 4px solid #00a45d;">
        <div id="visual-box" style="height: 80px; display: flex; justify-content: center; align-items: center;">
            <div id="pulse-dot" style="width: 20px; height: 20px; background: radial-gradient(circle, #ffcc33, #f7f9fc); border-radius: 50%; animation: pulse-blue 1.5s infinite;"></div>
        </div>
        <div id="success-icon" style="display: none; width: 50px; height: 50px; background: #28a745; color: white; border-radius: 50%; margin: 0 auto 15px; line-height: 50px; font-size: 25px;">✓</div>
        <h3 id="loader-text" style="margin: 0; color: #d55a5a; font-size: 1.1rem; font-weight: 800;">Creating Account...</h3>
        <p id="loader-sub" style="margin: 5px 0 0; color: #cd4444; font-size: 0.8rem;">Cornerstone Church</p>
    </div>
    </div>

<script>
function showLoading() {
    const overlay = document.getElementById('loader-overlay');
    const visual = document.getElementById('visual-box');
    const success = document.getElementById('success-icon');
    const text = document.getElementById('loader-text');
    const form = document.getElementById('inputsform');
    overlay.style.display = 'flex';
    setTimeout(() => {
        visual.style.display = 'none';
        success.style.display = 'block';
        text.innerText = "Registration Complete!";
        text.style.color = "#28a745";
        const fakeBtn = document.createElement('input'); 
        fakeBtn.type = 'hidden'; 
        fakeBtn.name = 'register_btn'; 
        fakeBtn.value = 'clicked';
        form.appendChild(fakeBtn); 
        setTimeout(() => { form.submit(); }, 1000); 
    }, 2000);
    return false;
}
</script>
</body>
</html>