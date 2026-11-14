<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Log - <?php echo $branch_name ?? 'Victoria'; ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        :root { --brand-red: #DC2626; --brand-dark: #0A0F0D; }
        .sidebar.staff { background: var(--brand-dark); border-right: 3px solid var(--brand-red); }
        .sidebar.staff .side-nav a.active { background: var(--brand-red); color: #fff; }
        .card.green { border-top: 4px solid var(--brand-red); border-left: 5px solid var(--brand-red); }
        .card.green small { color: var(--brand-red); }
        .btn.primary { background: var(--brand-red); color: #fff; border-color: var(--brand-red); }
        .btn.primary:hover { background: #B91C1C; border-color: #B91C1C; }
        .badge { border: 1px solid var(--brand-red); }
        .badge.daily { background: var(--brand-dark); color: var(--brand-red); border-color: var(--brand-red); }
        .badge.weekly { background: var(--brand-red); color: #fff; border-color: var(--brand-red); }
        .badge.monthly { background: var(--brand-dark); color: var(--brand-red); border-color: var(--brand-red); }
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
      <a href="/staff-vic/todays-logs" class="active">Today's Log</a>
      <a href="/staff-vic/members">Member Management</a>
      <a href="/staff-vic/expenses">Expenses Management</a>
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
        <h2>Today's Log</h2>
        <p class="muted">All check-in / daily logs for today (<?php echo htmlspecialchars($today_label ?? date('F d, Y')); ?>). <strong>Branch: <?php echo $branch_name ?? 'Victoria'; ?></strong></p>
      </div>
      <a href="/staff-vic" class="btn ghost small">Back to Dashboard</a>
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
                  <a href="/staff-vic/daily-logs/edit/<?php echo (int) ($l['id'] ?? 0); ?>" class="btn ghost small">Update</a>
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
