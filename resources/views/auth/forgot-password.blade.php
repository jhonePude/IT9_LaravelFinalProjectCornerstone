<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Cornerstone</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/loginpage.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <div class="login-card">
        <div class="image-side">
            <div class="side-brand-content">
                <span class="mission-tag">Faithful Security</span>
                <h1 class="side-title">RECOVER</h1>
                <p class="bible-verse">"Restore unto me the joy of thy salvation."<span class="verse-ref">Psalm 51:12</span></p>
                <div class="gold-line"></div>
            </div>
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="main-logo">
        </div>

        <div class="form-side">
            <div class="header-text">
                <h2 id="main-title">Account <span class="accent-text">Recovery</span></h2>
                <p id="instruction" class="subtitle">Enter your details to verify your account.</p>
                <hr class="divider-line">
            </div>

            <form id="recoveryForm">
                <div id="error-msg" class="error-msg"></div>

                <!-- PHASE 1: IDENTIFY -->
                <div id="phase-identify">
                    <div class="input-group">
                        <label>Email or Contact Number</label>
                        <input type="text" id="identifier" placeholder="e.g. name@email.com" required>
                    </div>
                </div>

                <!-- PHASE 2: VERIFY -->
                <div id="phase-verify" style="display: none;">
                    <label class="otp-label">Enter 6-Digit Code</label>
                    <div class="otp-container">
                        <input type="text" class="otp-input" maxlength="1">
                        <input type="text" class="otp-input" maxlength="1">
                        <input type="text" class="otp-input" maxlength="1">
                        <input type="text" class="otp-input" maxlength="1">
                        <input type="text" class="otp-input" maxlength="1">
                        <input type="text" class="otp-input" maxlength="1">
                    </div>
                </div>

                <!-- PHASE 3: RESET -->
                <div id="phase-reset" style="display: none;">
                    <div class="input-group">
                        <label>New Password</label>
                        <input type="password" id="new_password" placeholder="Enter new password" required >
                    </div>
                    <div class="input-group">
                        <label>Confirm Password</label>
                        <input type="password" id="confirm_password" placeholder="Repeat new password" required >
                    </div>
                </div>

                <button type="button" id="action-btn" class="btn-signin">Proceed</button>
            </form>

            <p class="signup-text"><a href="{{ route('login') }}">Back to Login</a></p>
        </div>
    </div>

    <!-- LOADING SCREEN -->
    <div id="loader-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); z-index: 10000; justify-content: center; align-items: center; transition: opacity 0.5s ease;">
        <div id="loader-card" style="text-align: center; max-width: 300px;">
            <div id="visual-box" style="margin-bottom: 20px;">
                <i class="fa-solid fa-cross" style="font-size: 40px; color: #d4af37; animation: breathe 2s ease-in-out infinite;"></i>
            </div>
            <div id="success-icon" style="display: none; margin-bottom: 20px;">
                <i class="fa-solid fa-check" style="font-size: 40px; color: #d4af37;"></i>
            </div>
            <h3 id="loader-text" style="margin: 0; color: #1e293b; font-family: 'Poppins', sans-serif; font-size: 1.2rem; font-weight: 500; letter-spacing: 1px;">Preparing the way...</h3>
            <p id="loader-sub" style="margin: 10px 0 0; color: #94a3b8; font-family: 'Poppins', sans-serif; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 2px;">Cornerstone Community</p>
        </div>
    </div>

<style>
    @keyframes breathe {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.7; }
    }
    .error-msg { display: none; color: #d93025; text-align: center; margin-bottom: 15px; font-size: 14px; }
    .otp-container { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 25px; }
    .otp-input { width: 40px; height: 50px; text-align: center; font-size: 20px; font-weight: bold; border: 2px solid #ddd; border-radius: 10px; background: #f8fafc; }
</style>

<script>
    const actionBtn = document.getElementById('action-btn');
    const errorDiv = document.getElementById('error-msg');
    const instruction = document.getElementById('instruction');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let currentPhase = 1;

    actionBtn.addEventListener('click', () => {
        if (currentPhase === 1) handleIdentify();
        else if (currentPhase === 2) handleVerify();
        else if (currentPhase === 3) handleReset();
    });

    function handleIdentify() {
        const id = document.getElementById('identifier').value;
        fetch('/forgot-password/process', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ step: 'send_code', identifier: id })
        })
        .then(res => res.json()).then(data => {
            if (data.status === 'success') {
                document.getElementById('phase-identify').style.display = 'none';
                document.getElementById('phase-verify').style.display = 'block';
                instruction.innerText = "Enter the 6-digit code we sent you.";
                currentPhase = 2;
            } else { showError(data.message); }
        });
    }

    function handleVerify() {
        let code = "";
        document.querySelectorAll('.otp-input').forEach(i => code += i.value);
        fetch('/forgot-password/process', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ step: 'verify_code', full_code: code })
        })
        .then(r => r.json()).then(data => {
            if (data.status === 'success') {
                document.getElementById('phase-verify').style.display = 'none';
                document.getElementById('phase-reset').style.display = 'block';
                currentPhase = 3;
            } else { showError(data.message); }
        });
    }

    function handleReset() {
        const p1 = document.getElementById('new_password').value;
        fetch('/forgot-password/process', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ step: 'update_password', password: p1 })
        })
        .then(r => r.json()).then(data => {
            if (data.status === 'success') { window.location.href = "{{ route('login') }}"; }
        });
    }

    function showError(m) {
        errorDiv.style.display = 'block';
        errorDiv.innerHTML = `<i class="fa-solid fa-circle-exclamation"></i> ${m}`;
    }

    document.querySelectorAll('.otp-input').forEach((input, index, inputs) => {
        input.addEventListener('input', () => {
            if (input.value.length === 1 && index < inputs.length - 1) inputs[index + 1].focus();
        });
    });
</script>
</body>
</html>