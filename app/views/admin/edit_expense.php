<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Expense - LavaLust Gym</title>
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
      <a href="/admin/expenses" class="active">Expenses History</a>
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
        <h2>Edit Expense</h2>
        <p class="muted">Update expense details.</p>
      </div>
      <a href="/admin/expenses" class="btn ghost small">Back to List</a>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="panel">
      <h3>Edit Expense</h3>
      <form action="/admin/expenses/update/<?php echo (int) ($expense['id'] ?? 0); ?>" method="post" class="form grid-form">
        <label>Expense Name
          <input name="name" placeholder="e.g. Electricity, Water, Equipment" required value="<?php echo htmlspecialchars($expense['name'] ?? ''); ?>" />
        </label>
        <label>Price (₱)
          <input type="number" name="price" step="0.01" min="0" placeholder="0" required value="<?php echo htmlspecialchars($expense['price'] ?? 0); ?>" />
        </label>
        <label>Date
          <input type="date" name="date" value="<?php echo htmlspecialchars($expense['date'] ?? ''); ?>" />
        </label>
        <label>Branch Location
          <select name="branch_id" id="branchSelect" required>
            <option value="">— Select branch —</option>
            <?php foreach (($branches ?? []) as $b): ?>
              <option value="<?php echo (int) ($b['id'] ?? 0); ?>" <?php echo ((int) ($expense['branch_id'] ?? 0) === (int) ($b['id'] ?? 0)) ? 'selected' : ''; ?>><?php echo htmlspecialchars($b['location'] ?? ''); ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <div class="form-actions">
          <button class="btn primary" type="submit">Update Expense</button>
          <a href="/admin/expenses" class="btn ghost">Cancel</a>
        </div>
      </form>
    </div>

  </main>
</div>

</body>
</html>