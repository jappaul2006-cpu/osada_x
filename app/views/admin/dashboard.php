<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LavaLust Gym</title>
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
      <a href="/admin" class="active">Dashboard</a>
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
        <h2>Admin Dashboard</h2>
        <p class="muted">Revenue overview, expenses, and registered members.</p>
      </div>
    </div>

    <div class="cards">
      <div class="card green">
        <small>Total Gym Revenue</small>
        <h3>₱<?php echo number_format($revenue['total'] ?? 0); ?></h3>
        <span class="muted small">Weekly ₱<?php echo number_format($revenue['weekly'] ?? 0); ?> • Monthly ₱<?php echo number_format($revenue['monthly'] ?? 0); ?> • Daily ₱<?php echo number_format($revenue['daily'] ?? 0); ?><br>Based on Total Amount of logs</span>
      </div>
      <div class="card dark">
        <small>Total Expenses</small>
        <h3>₱<?php echo number_format($exp_total ?? 0); ?></h3>
        <span class="muted small"><?php echo count($expenses ?? []); ?> expense records</span>
      </div>
      <div class="card black">
        <small>Net (Revenue − Expenses)</small>
        <h3>₱<?php echo number_format($net ?? 0); ?></h3>
        <span class="muted small"><?php echo ($net ?? 0) >= 0 ? 'Profitable' : 'Loss'; ?></span>
      </div>
      <div class="card">
        <small>Total Registered Members</small>
        <h3><?php echo $total_members ?? 0; ?></h3>
        <span class="muted small">Daily <?php echo $counts['daily'] ?? 0; ?> • Weekly <?php echo $counts['weekly'] ?? 0; ?> • Monthly <?php echo $counts['monthly'] ?? 0; ?></span>
      </div>
    </div>

    <div class="grid2">
      <div class="panel">
        <div class="panel-head">
          <h3>Registered Members</h3>
          <a href="/admin/members" class="btn small">Manage</a>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Name</th><th>Type</th><th>Contact</th></tr></thead>
            <tbody>
              <?php if (!empty($recent_members)): foreach ($recent_members as $m): ?>
                <tr>
                  <td><?php echo htmlspecialchars($m['name'] ?? ''); ?></td>
                  <td><span class="badge <?php echo $m['membership_type'] ?? 'monthly'; ?>"><?php echo htmlspecialchars($m['membership_type'] ?? ''); ?></span></td>
                  <td><?php echo htmlspecialchars($m['contact'] ?? ''); ?></td>
                </tr>
              <?php endforeach; else: ?>
                <tr><td colSpan="3" class="muted">No members found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="panel">
        <div class="panel-head">
          <h3>Latest Daily Logs</h3>
          <a href="/admin/daily-logs" class="btn small">History</a>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Name</th><th>Customer Type</th><th>Total Amount</th><th>Date</th></tr></thead>
            <tbody>
              <?php if (!empty($recent_logs)): foreach ($recent_logs as $l): ?>
                <tr>
                  <td><?php echo htmlspecialchars($l['name'] ?? ''); ?></td>
                  <td><span class="badge <?php echo $l['type'] ?? 'walk-in'; ?>"><?php echo htmlspecialchars($l['type'] ?? ''); ?></span></td>
                  <td>₱<?php echo number_format($l['total_amount'] ?? 0, 2); ?></td>
                  <td><?php echo htmlspecialchars($l['date'] ?? ''); ?></td>
                </tr>
              <?php endforeach; else: ?>
                <tr><td colSpan="4" class="muted">No logs found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

</body>
</html>
