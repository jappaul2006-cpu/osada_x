<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Expense - LavaLust Gym</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="shell">
  <!-- SIDEBAR -->
  <aside class="sidebar staff">
    <div class="side-brand">
      <div class="logo-circle sm">OX</div>
      <div>
        <strong>LavaLust Gym</strong>
        <small>Staff Panel</small>
      </div>
    </div>
    <nav class="side-nav">
      <a href="/staff">Staff Dashboard</a>
      <a href="/staff/todays-logs">Today's Log</a>
      <a href="/staff/members">Member Management</a>
      <a href="/staff/expenses" class="active">Expenses Management</a>
    </nav>
    <div class="side-foot">
      <small>Signed in as: <b><?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'staff'); ?></b></small>
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
      <a href="/staff/expenses" class="btn ghost small">Back to List</a>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="panel">
      <h3>Edit Expense</h3>
      <form action="/staff/expenses/update/<?php echo (int) ($expense['id'] ?? 0); ?>" method="post" class="form grid-form">
        <label>Expense Name
          <input name="name" placeholder="e.g. Electricity, Water, Equipment" required value="<?php echo htmlspecialchars($expense['name'] ?? ''); ?>" />
        </label>
        <label>Price (₱)
          <input type="number" name="price" step="0.01" min="0" placeholder="0" required value="<?php echo htmlspecialchars($expense['price'] ?? 0); ?>" />
        </label>
        <label>Date
          <input type="date" name="date" value="<?php echo htmlspecialchars($expense['date'] ?? ''); ?>" />
        </label>
        <div class="form-actions">
          <button class="btn primary" type="submit">Update Expense</button>
          <a href="/staff/expenses" class="btn ghost">Cancel</a>
        </div>
      </form>
    </div>

  </main>
</div>

</body>
</html>
