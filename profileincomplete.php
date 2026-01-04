<!DOCTYPE html>
<html>

<head>
    <title>Profile Completion Required</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="auth-wrapper">
        <div class="auth-form">
            <h2>Complete Your Profile</h2>
            <p>
                Your profile is <strong><?= $percent ?>%</strong> complete.<br>
                You must complete at least <strong>75%</strong>
                to submit a project idea.
            </p>

            <a href="profile.php" class="btn btn-primary">
                Complete Profile
            </a>

            <p class="auth-link">
                <a href="dashboard.php">Back to Dashboard</a>
            </p>
        </div>
    </div>

</body>

</html>