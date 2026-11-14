<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Log - LavaLust Gym</title>
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
      <a href="/staff/todays-logs" class="active">Today's Log</a>
      <a href="/staff/members">Member Management</a>
      <a href="/staff/expenses">Expenses Management</a>
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
        <h2>Today's Log</h2>
        <p class="muted">All check-in / daily logs for today (<?php echo htmlspecialchars($today_label ?? date('F d, Y')); ?>).</p>
      </div>
      <a href="/staff" class="btn ghost small">Back to Dashboard</a>
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
                  <a href="/staff/daily-logs/edit/<?php echo (int) ($l['id'] ?? 0); ?>" class="btn ghost small">Update</a>
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
