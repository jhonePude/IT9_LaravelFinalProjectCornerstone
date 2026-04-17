<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding: 20px; }
        .email-card { 
            background: white; 
            max-width: 400px; 
            margin: auto; 
            border-radius: 15px; 
            padding: 30px; 
            text-align: center; 
            border-top: 5px solid #d4af37;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .logo { width: 80px; margin-bottom: 20px; }
        h2 { color: #0f172a; margin-bottom: 10px; }
        .code { 
            font-size: 35px; 
            font-weight: 800; 
            color: #2563eb; 
            letter-spacing: 8px; 
            margin: 25px 0;
            background: #f1f5f9;
            padding: 15px;
            border-radius: 10px;
        }
        p { color: #64748b; line-height: 1.6; }
        .footer { font-size: 12px; color: #94a3b8; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="email-card">
        <h2>Cornerstone</h2>
        <p>Peace be with you. Use the code below to recover your account:</p>
        <div class="code">{{ $otp }}</div>
        <p>If you did not request this, please ignore this message. This code will expire soon.</p>
        <div class="footer">
            Cornerstone Community Church<br>
            "Go into all the world"
        </div>
    </div>
</body>
</html>