<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch Management - Admin Panel</title>
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
        <h2>Branch Management</h2>
        <p class="muted">Manage gym branches. Staff users are assigned to a branch and only see that branch's data.</p>
      </div>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="panel">
      <h3>New Branch</h3>
      <form action="/admin/branches/store" method="post" class="form grid-form">
        <label>Branch Name / Location
          <input name="location" placeholder="e.g. Calapan, Pinamalayan, Victoria, Barcenaga" required />
        </label>
        <div class="form-actions">
          <button class="btn primary" type="submit">Add Branch</button>
        </div>
      </form>
    </div>

    <div class="panel">
      <div class="panel-head wrap">
        <h3>Branch List (<?php echo count($branches ?? []); ?>)</h3>
        <div class="toolbar">
          <form method="get" class="toolbar" style="display:flex;gap:8px;align-items:center;">
            <input class="search" name="q" placeholder="Search branch name..." value="<?php echo htmlspecialchars($q ?? ''); ?>" />
          </form>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>ID</th><th>Branch Name / Location</th><th>Created At</th><th>Action</th></tr></thead>
          <tbody>
            <?php if (!empty($branches)): foreach ($branches as $b): ?>
              <tr>
                <td><?php echo (int) ($b['id'] ?? 0); ?></td>
                <td><?php echo htmlspecialchars($b['location'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($b['created_at'] ?? ''); ?></td>
                <td class="actions">
                  <a href="/admin/branches/edit/<?php echo (int) ($b['id'] ?? 0); ?>" class="btn small primary">Update</a>
                  <form action="/admin/branches/delete/<?php echo (int) ($b['id'] ?? 0); ?>" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this branch? This cannot be undone.');">
                    <button class="btn small danger" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colSpan="4" class="muted">No branches found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

<style>
  .actions .btn.danger {
    background: var(--white);
    border-color: var(--dark-green);
    color: var(--dark-green);
  }
  .actions .btn.danger:hover {
    background: var(--black);
    color: #fff;
  }
</style>

</body>
</html>
