<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LavaLust Gym</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="theme-navy">

<div class="login-wrap">
  <div class="login-card">
    <div class="login-brand">
      <div class="logo-circle">OX</div>
      <div>
        <h1>LavaLust Gym</h1>
        <p>Admin &amp; Staff Portal</p>
      </div>
    </div>

    <h2>Login</h2>
    <p class="muted">Sign in to continue.</p>

    <?php if (!empty($error)): ?>
      <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="/login" method="post" class="form">
      <label>
        Username
        <input name="username" placeholder="Enter username" autocomplete="username" required />
      </label>
      <label>
        Password
        <div class="pass-row">
          <input type="password" name="password" placeholder="Enter password" autocomplete="current-password" required />
        </div>
      </label>
      <button class="btn primary big" type="submit">Login</button>
    </form>
  </div>
</div>

</body>
</html>
