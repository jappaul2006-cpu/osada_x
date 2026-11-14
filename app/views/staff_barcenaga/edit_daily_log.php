<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Daily Log - <?php echo $branch_name ?? 'Barcenaga'; ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        :root { --brand-white: #FFFFFF; --brand-dark: #0A0F0D; }
        .sidebar.staff { background: var(--brand-dark); border-right: 3px solid var(--brand-white); }
        .sidebar.staff .side-nav a.active { background: var(--brand-white); color: var(--brand-dark); }
        .btn.primary { background: var(--brand-white); color: var(--brand-dark); border-color: var(--brand-white); }
        .btn.primary:hover { background: #f0f0f0; border-color: #f0f0f0; }
        th { background: var(--brand-dark); color: var(--brand-white); }
        .logo-circle.sm { background: linear-gradient(135deg, #0A0F0D, #FFFFFF); color: var(--brand-dark); }
        .sidebar.staff .side-brand strong { color: var(--brand-white); }
        .sidebar.staff .side-brand small { color: #e5e7eb; }
        .sidebar.staff .side-nav a { color: #e5e7eb; }
        .sidebar.staff .side-foot { color: #e5e7eb; }
        .sidebar.staff .side-foot .btn { color: var(--brand-white); border-color: var(--brand-white); }
        .sidebar.staff .side-foot .btn:hover { background: var(--brand-white); border-color: var(--brand-white); color: var(--brand-dark); }
        body { background: #f3f4f6; }
    </style>
</head>
<body>

<div class="shell">
  <aside class="sidebar staff">
    <div class="side-brand">
      <div class="logo-circle sm">BX</div>
      <div>
        <strong><?php echo $branch_name ?? 'Barcenaga'; ?> Gym</strong>
        <small>Staff Panel</small>
      </div>
    </div>
    <nav class="side-nav">
      <a href="/staff-barc">Staff Dashboard</a>
      <a href="/staff-barc/todays-logs">Today's Log</a>
      <a href="/staff-barc/members">Member Management</a>
      <a href="/staff-barc/expenses">Expenses Management</a>
    </nav>
    <div class="side-foot">
      <small>Signed in as: <b><?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'staff'); ?></b></small>
      <small>Branch: <b><?php echo $branch_name ?? 'Barcenaga'; ?></b></small>
      <a href="/logout" class="btn ghost small">Logout</a>
    </div>
  </aside>

  <main class="content">
    <div class="page-head">
      <div>
        <h2>Edit Daily Log</h2>
        <p class="muted">Update check-in details. Branch: <strong><?php echo $branch_name ?? 'Barcenaga'; ?></strong></p>
      </div>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="panel">
      <h3>Edit Log #<?php echo (int) ($log['id'] ?? 0); ?></h3>
      <form action="/staff-barc/daily-logs/update/<?php echo (int) ($log['id'] ?? 0); ?>" method="post" class="form grid-form">
        <label>Name
          <input name="name" value="<?php echo htmlspecialchars($log['name'] ?? ''); ?>" required />
        </label>
        <label>Customer Type
          <input type="text" name="customer_type" list="customerTypeOptions" value="<?php echo htmlspecialchars($log['type'] ?? 'walk-in'); ?>" autocomplete="off" />
          <datalist id="customerTypeOptions">
            <option value="student"></option>
            <option value="member"></option>
            <option value="non-member"></option>
            <option value="walk-in"></option>
          </datalist>
        </label>
        <label>Membership Type
          <select name="membership_type">
            <option value="daily" <?php echo (($log['membership_type'] ?? '') === 'daily') ? 'selected' : ''; ?>>Daily</option>
            <option value="2weeks" <?php echo (($log['membership_type'] ?? '') === '2weeks') ? 'selected' : ''; ?>>2 Weeks</option>
            <option value="monthly" <?php echo (($log['membership_type'] ?? '') === 'monthly') ? 'selected' : ''; ?>>Monthly</option>
          </select>
        </label>
        <label>Membership Amount (₱)
          <input type="number" name="membership_amount" step="0.01" value="<?php echo number_format($log['membership_amount'] ?? 0, 2); ?>" />
        </label>
        <label>Weekly Renewal (₱)
          <input type="number" name="weekly_renewal" step="0.01" value="<?php echo number_format($log['weekly_renewal'] ?? 0, 2); ?>" />
        </label>
        <label>Monthly Renewal (₱)
          <input type="number" name="monthly_renewal" step="0.01" value="<?php echo number_format($log['monthly_renewal'] ?? 0, 2); ?>" />
        </label>
        <label>Membership Renewal (₱)
          <input type="number" name="membership_renewal" step="0.01" value="<?php echo number_format($log['membership_renewal'] ?? 0, 2); ?>" />
        </label>
        <label>Date
          <input type="date" name="date" value="<?php echo htmlspecialchars($log['date'] ?? ''); ?>" />
        </label>
        <label>Time
          <input type="time" name="time" value="<?php echo htmlspecialchars($log['time'] ?? ''); ?>" />
        </label>
        <div class="form-actions">
          <button class="btn primary" type="submit">Update Log</button>
          <a href="/staff-barc" class="btn ghost small">Cancel</a>
        </div>
      </form>
    </div>
  </main>

<style>
  .actions .btn.primary {
    background: #FFFFFF;
    color: #0A0F0D;
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
    background: #f0f0f0;
  }
</style>

</body>
</html>
