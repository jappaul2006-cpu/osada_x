<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Expense - <?php echo $branch_name ?? 'Victoria'; ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        :root { --brand-red: #DC2626; --brand-dark: #0A0F0D; }
        .sidebar.staff { background: var(--brand-dark); border-right: 3px solid var(--brand-red); }
        .sidebar.staff .side-nav a.active { background: var(--brand-red); color: #fff; }
        .btn.primary { background: var(--brand-red); color: #fff; border-color: var(--brand-red); }
        .btn.primary:hover { background: #B91C1C; border-color: #B91C1C; }
        th { background: var(--brand-dark); color: #fff; }
        .logo-circle.sm { background: linear-gradient(135deg, #0A0F0D, #DC2626); }
    </style>
</head>
<body>

<div class="shell">
  <aside class="sidebar staff">
    <div class="side-brand">
      <div class="logo-circle sm">VX</div>
      <div>
        <strong><?php echo $branch_name ?? 'Victoria'; ?> Gym</strong>
        <small>Staff Panel</small>
      </div>
    </div>
    <nav class="side-nav">
      <a href="/staff-vic">Staff Dashboard</a>
      <a href="/staff-vic/todays-logs">Today's Log</a>
      <a href="/staff-vic/members">Member Management</a>
      <a href="/staff-vic/expenses" class="active">Expenses Management</a>
    </nav>
    <div class="side-foot">
      <small>Signed in as: <b><?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'staff'); ?></b></small>
      <small>Branch: <b><?php echo $branch_name ?? 'Victoria'; ?></b></small>
      <a href="/logout" class="btn ghost small">Logout</a>
    </div>
  </aside>

  <main class="content">
    <div class="page-head">
      <div>
        <h2>Edit Expense</h2>
        <p class="muted">Update expense details. Branch: <strong><?php echo $branch_name ?? 'Victoria'; ?></strong></p>
      </div>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="panel">
      <h3>Edit Expense #<?php echo (int) ($expense['id'] ?? 0); ?></h3>
      <form action="/staff-vic/expenses/update/<?php echo (int) ($expense['id'] ?? 0); ?>" method="post" class="form grid-form">
        <label>Expense Name
          <input name="name" value="<?php echo htmlspecialchars($expense['name'] ?? ''); ?>" placeholder="e.g. Electricity, Water, Equipment" required />
        </label>
        <label>Price (₱)
          <input type="number" name="price" step="0.01" min="0" value="<?php echo number_format($expense['price'] ?? 0, 2); ?>" required />
        </label>
        <label>Date
          <input type="date" name="date" value="<?php echo htmlspecialchars($expense['date'] ?? ''); ?>" />
        </label>
        <div class="form-actions">
          <button class="btn primary" type="submit">Update Expense</button>
          <a href="/staff-vic/expenses" class="btn ghost small">Cancel</a>
        </div>
      </form>
    </div>
  </main>

<style>
  .actions .btn.primary {
    background: #DC2626;
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
    background: #B91C1C;
  }
</style>

</body>
</html>
