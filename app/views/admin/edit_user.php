<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - LavaLust Gym</title>
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
      <a href="/admin/branches">Branch Management</a>
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
        <h2>Edit User</h2>
        <p class="muted">Update user details.</p>
      </div>
      <a href="/admin/users" class="btn ghost small">Back to List</a>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="panel">
      <h3>Edit User</h3>
      <form action="/admin/users/update/<?php echo (int) ($user['id'] ?? 0); ?>" method="post" class="form grid-form">
        <label>Username
          <input name="username" placeholder="Enter username" required value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" />
        </label>
        <label>Password (leave blank to keep current)
          <input type="password" name="password" placeholder="Enter new password" />
        </label>
        <label>Branch
          <select name="branch_id" required>
            <option value="">— Select branch —</option>
            <?php foreach ($branches ?? [] as $b): ?>
              <option value="<?php echo (int) ($b['id'] ?? 0); ?>" <?php echo ((int) ($user['branch_id'] ?? 0) === (int) ($b['id'] ?? 0)) ? 'selected' : ''; ?>><?php echo htmlspecialchars($b['location'] ?? ''); ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label style="display:flex; align-items:center; gap:8px;">
          <input type="checkbox" name="is_active" value="1" <?php echo ($user['is_active'] ?? 0) ? 'checked' : ''; ?> />
          <span>Active</span>
        </label>
        <div class="form-actions">
          <button class="btn primary" type="submit">Update User</button>
          <a href="/admin/users" class="btn ghost">Cancel</a>
        </div>
      </form>
    </div>

  </main>
</div>

</body>
</html>