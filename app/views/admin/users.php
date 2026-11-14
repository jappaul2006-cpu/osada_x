<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - LavaLust Gym</title>
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
      <a href="/admin/users" class="active">User Management</a>
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
        <h2>User Management</h2>
        <p class="muted">Add, edit, and manage staff users.</p>
      </div>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="panel">
      <h3>New User</h3>
      <form action="/admin/users/store" method="post" class="form grid-form">
        <label>Username
          <input name="username" placeholder="Enter username" required />
        </label>
        <label>Password
          <input type="password" name="password" placeholder="Enter password" required />
        </label>
        <label>Branch
          <select name="branch_id" required>
            <option value="">— Select branch —</option>
            <?php foreach ($branches ?? [] as $b): ?>
              <option value="<?php echo (int) ($b['id'] ?? 0); ?>"><?php echo htmlspecialchars($b['location'] ?? ''); ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <div class="form-actions">
          <button class="btn primary" type="submit">Add User</button>
        </div>
      </form>
    </div>

    <div class="panel">
      <div class="panel-head wrap">
        <h3>List (<?php echo count($users ?? []); ?>)</h3>
        <div class="toolbar">
          <form method="get" class="toolbar" style="display:flex;gap:8px;align-items:center;">
            <input class="search" name="q" placeholder="Search username / role..." value="<?php echo htmlspecialchars($q ?? ''); ?>" />
            <select name="filter" onchange="this.form.submit()">
              <option value="all" <?php echo (($filter ?? '') === 'all') ? 'selected' : ''; ?>>All</option>
              <option value="admin" <?php echo (($filter ?? '') === 'admin') ? 'selected' : ''; ?>>Admin</option>
              <option value="staff" <?php echo (($filter ?? '') === 'staff') ? 'selected' : ''; ?>>Staff</option>
            </select>
          </form>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Username</th><th>Role</th><th>Branch</th><th>Status</th><th>Created At</th><th>Action</th></tr></thead>
          <tbody>
            <?php if (!empty($users)): foreach ($users as $u): ?>
              <?php $uBid = (int) ($u['branch_id'] ?? 0); ?>
              <tr>
                <td><?php echo htmlspecialchars($u['username'] ?? ''); ?></td>
                <td><span class="badge"><?php echo htmlspecialchars(ucfirst($u['role'] ?? '')); ?></span></td>
                <td>
                  <?php if ($uBid > 0 && isset($branchMap[$uBid])): ?>
                    <span class="badge branch-badge b-<?php echo (int) $uBid; ?>"><?php echo htmlspecialchars($branchMap[$uBid]); ?></span>
                  <?php else: ?>
                    <span class="muted">—</span>
                  <?php endif; ?>
                </td>
                <td><span class="badge <?php echo ($u['is_active'] ?? 0) ? 'success' : 'danger'; ?>"><?php echo ($u['is_active'] ?? 0) ? 'Active' : 'Inactive'; ?></span></td>
                <td><?php echo htmlspecialchars($u['created_at'] ?? ''); ?></td>
                <td class="actions">
                  <a href="/admin/users/edit/<?php echo (int) ($u['id'] ?? 0); ?>" class="btn small primary" style="margin-right:6px;">Update</a>
                  <?php if ((int) $u['id'] !== (int) ($_SESSION['user']['id'] ?? 0)): ?>
                    <form action="/admin/users/delete/<?php echo (int) ($u['id'] ?? 0); ?>" method="post" style="display:inline;" onsubmit="event.preventDefault(); showConfirm('Delete this user?', function(){ this.submit(); }.bind(this));">
                      <button class="btn small danger" type="submit">Delete</button>
                    </form>
                  <?php else: ?>
                    <span class="btn small danger" style="cursor:not-allowed; opacity:0.6;">Self</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colSpan="6" class="muted">No users found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<style>
  .actions .btn.primary {
    background: #16233A;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
  }
  .actions .btn.primary:hover {
    background: #0B0E14;
  }
  .badge.success { background: #22c55e; color: #fff; }
  .badge.danger { background: #ef4444; color: #fff; }
</style>

<!-- CUSTOM CONFIRM DIALOG -->
<style>
  #confirmDialog {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 2000;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
  }
  #confirmDialog .confirm-box {
    background: #fff;
    border-radius: 16px;
    padding: 28px 36px;
    max-width: 440px;
    width: 90%;
    text-align: center;
    box-shadow: 0 24px 80px rgba(0, 0, 0, 0.35);
    border: 1px solid var(--line, #e5e7eb);
    animation: confirmSlide 0.2s ease-out;
  }
  @keyframes confirmSlide {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
  }
  #confirmDialog #confirmMessage {
    margin-bottom: 24px;
    font-size: 16px;
    color: #111;
    font-weight: 600;
  }
  #confirmDialog .confirm-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
  }
  #confirmDialog #confirmOk {
    background: #dc2626;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 24px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }
  #confirmDialog #confirmOk:hover {
    background: #b91c1c;
  }
  #confirmDialog #confirmCancel {
    background: #f3f4f6;
    color: #374151;
    border: none;
    border-radius: 8px;
    padding: 10px 24px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
  }
  #confirmDialog #confirmCancel:hover {
    background: #e5e7eb;
  }
</style>
<div id="confirmDialog">
  <div class="confirm-box">
    <div id="confirmMessage" style="margin-bottom:24px; font-size:16px; color:#111; font-weight:600;">Delete this user?</div>
    <div class="confirm-actions">
      <button id="confirmOk" style="background:#dc2626; color:#fff; border:none; border-radius:8px; padding:10px 24px; font-size:14px; font-weight:600; cursor:pointer;">Yes, Delete</button>
      <button id="confirmCancel" style="background:#f3f4f6; color:#374151; border:none; border-radius:8px; padding:10px 24px; font-size:14px; font-weight:500; cursor:pointer;">Cancel</button>
    </div>
  </div>
</div>

<script>
let confirmCallback = null;
function showConfirm(msg, cb) {
  document.getElementById('confirmMessage').textContent = msg;
  confirmCallback = cb;
  document.getElementById('confirmDialog').style.display = 'flex';
}
document.getElementById('confirmOk').addEventListener('click', function(){
  document.getElementById('confirmDialog').style.display = 'none';
  if (confirmCallback) confirmCallback();
  confirmCallback = null;
});
document.getElementById('confirmCancel').addEventListener('click', function(){
  document.getElementById('confirmDialog').style.display = 'none';
  confirmCallback = null;
});
</script>

</body>
</html>