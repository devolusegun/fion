<div class="auth-wrapper">
  <form class="auth-form" method="POST" action="register.php">
    <h2 class="auth-title">Create Your Account</h2>
    <div class="form-group">
      <input type="text" class="input-field" name="fullname" required placeholder="Full Name">
    </div>
    <div class="form-group">
      <input type="email" class="input-field" name="email" required placeholder="Email">
    </div>
    <div class="form-group">
      <input type="password" class="input-field" name="password" required placeholder="Password">
    </div>
    <button type="submit" class="btn btn-primary">Register</button>
    <p class="auth-link">Already have an account? <a href="login.php">Login</a></p>
  </form>
</div>
