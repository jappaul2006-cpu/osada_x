<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Log - <?php echo $branch_name ?? 'Barcenaga'; ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        :root { --brand-white: #FFFFFF; --brand-dark: #0A0F0D; }
        .sidebar.staff { background: var(--brand-dark); border-right: 3px solid var(--brand-white); }
        .sidebar.staff .side-nav a.active { background: var(--brand-white); color: var(--brand-dark); }
        .card.green { border-top: 4px solid var(--brand-white); border-left: 5px solid var(--brand-white); }
        .card.green small { color: var(--brand-white); }
        .btn.primary { background: var(--brand-white); color: var(--brand-dark); border-color: var(--brand-white); }
        .btn.primary:hover { background: #f0f0f0; border-color: #f0f0f0; }
        .badge { border: 1px solid var(--brand-white); }
        .badge.daily { background: var(--brand-dark); color: var(--brand-white); border-color: var(--brand-white); }
        .badge.weekly { background: var(--brand-white); color: var(--brand-dark); border-color: var(--brand-white); }
        .badge.monthly { background: var(--brand-dark); color: var(--brand-white); border-color: var(--brand-white); }
        th { background: var(--brand-dark); color: var(--brand-white); }
        .logo-circle.sm { background: linear-gradient(135deg, #0A0F0D, #FFFFFF); color: var(--brand-dark); }
        .sidebar.staff .side-brand strong { color: var(--brand-white); }
        .sidebar.staff .side-brand small { color: #e5e7eb; }
        .sidebar.staff .side-nav a { color: #e5e7eb; }
        .sidebar.staff .side-foot { color: #e5e7eb; }
        .sidebar.staff .side-foot .btn { color: var(--brand-white); border-color: var(--brand-white); }
        .sidebar.staff .side-foot .btn:hover { background: var(--brand-white); border-color: var(--brand-white); color: var(--brand-dark); }
        .page-head h2 { color: var(--brand-dark); }
        .card h3 { color: var(--brand-dark); }
        .panel h3 { color: var(--brand-dark); }
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
      <a href="/staff-barc/todays-logs" class="active">Today's Log</a>
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
        <h2>Today's Log</h2>
        <p class="muted">All check-in / daily logs for today (<?php echo htmlspecialchars($today_label ?? date('F d, Y')); ?>). <strong>Branch: <?php echo $branch_name ?? 'Barcenaga'; ?></strong></p>
      </div>
      <a href="/staff-barc" class="btn ghost small">Back to Dashboard</a>
    </div>

    <div class="cards">
      <div class="card">
        <small>Check-ins Today</small>
        <h3><?php echo $today_count ?? 0; ?></h3>
      </div>
      <div class="card green">
        <small>Daily Cash Today</small>
        <h3>₱<?php echo number_format($today_cash ?? 0, 2); ?></h3>
        <span class="muted small">Sum of Total Amount (Today's Logs)</span>
      </div>
    </div>

    <div class="panel">
      <h3>Today's Logs (<?php echo $today_count ?? 0; ?>)</h3>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Name</th><th>Customer Type</th><th>Membership Type</th><th>Amount</th><th>Weekly Renewal</th><th>Monthly Renewal</th><th>Membership Renewal</th><th>Total Amount</th><th>Time</th><th>Action</th></tr></thead>
          <tbody>
            <?php if (!empty($today_logs)): foreach ($today_logs as $l): ?>
              <tr>
                <td><?php echo htmlspecialchars($l['name'] ?? ''); ?></td>
                <td><span class="badge <?php echo $l['type'] ?? 'walk-in'; ?>"><?php echo htmlspecialchars($l['type'] ?? ''); ?></span></td>
                <td><?php echo htmlspecialchars($l['membership_type'] ?? 'daily'); ?></td>
                <td>₱<?php echo number_format($l['membership_amount'] ?? 0, 2); ?></td>
                <td>₱<?php echo number_format($l['weekly_renewal'] ?? 0, 2); ?></td>
                <td>₱<?php echo number_format($l['monthly_renewal'] ?? 0, 2); ?></td>
                <td>₱<?php echo number_format($l['membership_renewal'] ?? 0, 2); ?></td>
                <td>₱<?php echo number_format($l['total_amount'] ?? 0, 2); ?></td>
                <td><?php echo htmlspecialchars($l['time'] ?? ''); ?></td>
                <td>
                  <a href="/staff-barc/daily-logs/edit/<?php echo (int) ($l['id'] ?? 0); ?>" class="btn ghost small">Update</a>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="10" class="muted">No logs for today.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

</body>
</html>
