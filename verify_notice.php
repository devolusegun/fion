<!DOCTYPE html>
<html>

<head>
    <title>Email Verification Required</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="auth-wrapper">
        <div class="auth-form">
            <h2 class="auth-title">Verify Your Email</h2>
            <p>
                Your account has been created, but you must verify your email address
                before accessing the platform.
            </p>
            <p>
                Please check your inbox (and spam folder).
            </p>

            <!-- SUCCESS MESSAGE (after resend) -->
            <?php if (isset($_GET['resent'])): ?>
                <p style="color:#47caff; margin-top:12px;">
                    Verification email resent successfully.
                </p>
            <?php endif; ?>

            <!-- ACTION BUTTON -->
            <a href="resendverification" class="btn btn-primary" style="margin-top:15px;">
                Resend Verification Email
            </a>

            <!-- Optional navigation -->
            <p class="auth-link" style="margin-top:15px;">
                <a href="login" class="btn btn-primary">Back to Login</a>
            </p>
            
        </div>
    </div>

</body>

</html>