<?php 
      require_once "config.php";

      function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
    $mess = [];
    $errors = [];

   if($_SERVER['REQUEST_METHOD']==="POST"){
    $uname = $_POST['uname'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if($password === $password2){
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");

        if ($stmt) {
            $stmt->bind_param('ss', $uname, $password);

            if ($stmt->execute()) {
                $mess[] = "Account created successfully. Please wait for admin approval.";
            } else {
                $errors[] = "Something went wrong. Please try again.";
            }

            $stmt->close();
        }else{
            $errors[] = "Something went wrong. Please try again.";
        }
    }else{
        $errors[] = "Passwords should match.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        :root { font-family: "Segoe UI", Arial, sans-serif; background: #f7f4e9; color: #263b31; }
        * { box-sizing: border-box; }
        body {
            min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 32px 18px;
            background: radial-gradient(ellipse at 12% 15%, rgba(191,220,190,.55), transparent 32%),
                        radial-gradient(ellipse at 88% 85%, rgba(224,235,205,.75), transparent 34%), #f7f4e9;
        }
        .register-card {
            width: min(100%, 460px); padding: clamp(28px, 7vw, 48px); border: 1px solid rgba(93,126,98,.17);
            border-radius: 26px; background: rgba(255,254,248,.94); box-shadow: 0 22px 60px rgba(54,79,57,.13);
            animation: arrive .5s ease-out both;
        }
        .brand-mark { display: grid; place-items: center; width: 52px; height: 52px; margin-bottom: 23px; border-radius: 17px; background: #e4efdf; color: #52765a; font-size: 25px; }
        h1 { margin: 0 0 8px; font-size: 1.55rem; letter-spacing: -.04em; }
        h2 { margin: 0 0 26px; color: #748276; font-size: 1rem; font-weight: 450; }
        .field { display: block; margin-bottom: 18px; }
        .field label { display: block; margin-bottom: 8px; color: #405548; font-size: .9rem; font-weight: 650; }
        .field input { width: 100%; padding: 14px 15px; border: 1px solid #dce4d8; border-radius: 12px; outline: none; background: #fffefb; color: #263b31; font: inherit; transition: border-color .2s, box-shadow .2s, background .2s; }
        .field input::placeholder { color: #a0aaa0; }
        .field input:hover { border-color: #a8c2a6; }
        .field input:focus { border-color: #78a47b; background: #fff; box-shadow: 0 0 0 4px rgba(120,164,123,.16); }
        .notice { margin: 0 0 16px; padding: 12px 14px; border-radius: 11px; font-size: .9rem; }
        .error-message { border: 1px solid #efd0c8; background: #fff2ee; color: #a04e42; }
        .success-message { border: 1px solid #cfe2ce; background: #eff7ed; color: #47704b; }
        .submit-button { width: 100%; margin-top: 4px; padding: 14px 18px; border: 0; border-radius: 12px; background: #789d79; color: #fffef8; font: inherit; font-weight: 700; cursor: pointer; box-shadow: 0 8px 18px rgba(91,131,94,.22); transition: transform .18s, background .18s, box-shadow .18s; }
        .submit-button:hover { transform: translateY(-2px); background: #668b69; box-shadow: 0 11px 22px rgba(91,131,94,.28); }
        .submit-button:active { transform: translateY(0); }
        .submit-button:focus-visible, a:focus-visible { outline: 3px solid #b6d0ad; outline-offset: 3px; }
        .signin-note { margin: 25px 0 0; color: #7a877b; text-align: center; font-size: .92rem; }
        a { color: #527c58; font-weight: 700; text-decoration-thickness: 1px; text-underline-offset: 3px; }
        a:hover { color: #345e3d; }
        @keyframes arrive { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; } }
    </style>
</head>
<body>
    <main class="register-card">
        <div class="brand-mark" aria-hidden="true">✿</div>
        <h1>Inventory System</h1>
        <h2>Create your account</h2>
        <?php foreach ($errors as $e): ?>
            <p class="notice error-message" role="alert"><?= h($e) ?></p>
        <?php endforeach; ?>
        <?php foreach ($mess as $m): ?>
            <p class="notice success-message" role="status"><?= h($m) ?></p>
        <?php endforeach; ?>
        <form action="" method="post">
            <div class="field">
                <label for="uname">Username</label>
                <input type="text" name="uname" id="uname" placeholder="Choose a username" required autofocus autocomplete="username">
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Create a password" required autocomplete="new-password">
            </div>
            <div class="field">
                <label for="password2">Confirm password</label>
                <input type="password" name="password2" id="password2" placeholder="Enter your password again" required autocomplete="new-password">
            </div>
            <button class="submit-button" type="submit">Create Account</button>
        </form>
        <p class="signin-note">Already have an account? <a href="index.php">Sign in</a></p>
    </main>
</body>
</html>
