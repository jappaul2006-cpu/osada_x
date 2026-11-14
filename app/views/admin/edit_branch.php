<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Branch - Admin Panel</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="theme-navy">

<div class="shell">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="side-brand">
      <div class="logo-circle sm">OX</div>
      <div>
        <strong>LavaLust Gym</strong>
        <small>Admin Panel</small>
      </div>
    </div>
    <nav class="side-nav">
      <a href="/admin">Dashboard</a>
      <a href="/admin/members">Member Management</a>
      <a href="/admin/daily-logs">Daily Log History</a>
      <a href="/admin/expenses">Expenses History</a>
      <a href="/admin/users">User Management</a>
      <a href="/admin/branches" class="active">Branch Management</a>
    </nav>
    <div class="side-foot">
      <small>Signed in as: <b><?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'Admin'); ?></b></small>
      <a href="/logout" class="btn ghost small">Logout</a>
    </div>
  </aside>

  <!-- CONTENT -->
  <main class="content">
    <div class="page-head">
      <div>
        <h2>Edit Branch</h2>
        <p class="muted">Update branch details.</p>
      </div>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="panel">
      <h3>Edit Branch #<?php echo (int) ($branch['id'] ?? 0); ?></h3>
      <form action="/admin/branches/update/<?php echo (int) ($branch['id'] ?? 0); ?>" method="post" class="form grid-form">
        <label>Branch Name / Location
          <input name="location" value="<?php echo htmlspecialchars($branch['location'] ?? ''); ?>" placeholder="e.g. Calapan, Pinamalayan, Victoria, Barcenaga" required />
        </label>
        <div class="form-actions">
          <button class="btn primary" type="submit">Update Branch</button>
          <a href="/admin/branches" class="btn ghost small">Cancel</a>
        </div>
      </form>
    </div>
  </main>

</body>
</html>
