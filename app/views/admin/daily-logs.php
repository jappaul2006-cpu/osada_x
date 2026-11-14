<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Log History - LavaLust Gym</title>
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
      <a href="/admin/daily-logs" class="active">Daily Log History</a>
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
        <h2>Daily Log History</h2>
        <p class="muted">Logged customers from staff. Type depends on registered membership. <strong>Branch: <?php echo htmlspecialchars($activeBranch ?? '—'); ?></strong></p>
      </div>
    </div>

    <div class="cards">
      <div class="card">
        <small>Total Check-ins (filtered)</small>
        <h3><?php echo $count ?? 0; ?></h3>
      </div>
      <div class="card green">
        <small>Total Income (filtered)</small>
        <h3>₱<?php echo number_format($total_income ?? 0, 2); ?></h3>
        <span class="muted small">Sum of Total Amount column</span>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head wrap">
        <h3>History</h3>
        <div class="toolbar">
          <form method="get" class="toolbar" style="display:flex;gap:8px;align-items:center;">
            <input type="hidden" name="branch" value="<?php echo (int) ($branch ?? 0); ?>" />
            <input class="search" name="q" placeholder="Search name..." value="<?php echo htmlspecialchars($q ?? ''); ?>" />
            <select name="filter" onchange="this.form.submit()">
              <option value="all" <?php echo (($filter ?? '') === 'all') ? 'selected' : ''; ?>>All</option>
              <option value="daily" <?php echo (($filter ?? '') === 'daily') ? 'selected' : ''; ?>>Daily</option>
              <option value="2weeks" <?php echo (($filter ?? '') === '2weeks') ? 'selected' : ''; ?>>2 Weeks</option>
              <option value="monthly" <?php echo (($filter ?? '') === 'monthly') ? 'selected' : ''; ?>>Monthly</option>
            </select>
          </form>
        </div>
      </div>

      <!-- BRANCH TABS - parang nav bar pero para sa table -->
      <nav class="branch-tabs">
        <?php foreach (($branches ?? []) as $b): ?>
          <?php $bid = (int) ($b['id'] ?? 0); ?>
          <a class="branch-tab b-<?php echo $bid; ?><?php echo ($bid === (int) ($branch ?? 0)) ? ' active' : ''; ?>"
             href="/admin/daily-logs?branch=<?php echo $bid; ?>&amp;q=<?php echo urlencode($q ?? ''); ?>&amp;filter=<?php echo urlencode($filter ?? 'all'); ?>">
            <?php echo htmlspecialchars($b['location'] ?? ''); ?>
            <span class="tab-count"><?php echo (int) (($branchCounts ?? [])[$bid] ?? 0); ?></span>
          </a>
        <?php endforeach; ?>
      </nav>

      <div class="table-wrap">
        <table>
          <thead><tr><th>Name</th><th>Customer Type</th><th>Membership Type</th><th>Amount</th><th>Weekly Renewal</th><th>Monthly Renewal</th><th>Membership Renewal</th><th>Total Amount</th><th>Date</th><th>Time</th></tr></thead>
          <tbody>
            <?php if (!empty($logs)): foreach ($logs as $l): ?>
              <tr>
                <td><?php echo htmlspecialchars($l['name'] ?? ''); ?></td>
                <td><span class="badge <?php echo $l['type'] ?? 'walk-in'; ?>"><?php echo htmlspecialchars($l['type'] ?? ''); ?></span></td>
                <td><?php echo htmlspecialchars($l['membership_type'] ?? 'daily'); ?></td>
                <td>₱<?php echo number_format($l['membership_amount'] ?? 0, 2); ?></td>
                <td>₱<?php echo number_format($l['weekly_renewal'] ?? 0, 2); ?></td>
                <td>₱<?php echo number_format($l['monthly_renewal'] ?? 0, 2); ?></td>
                <td>₱<?php echo number_format($l['membership_renewal'] ?? 0, 2); ?></td>
                <td>₱<?php echo number_format($l['total_amount'] ?? 0, 2); ?></td>
                <td><?php echo htmlspecialchars($l['date'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($l['time'] ?? ''); ?></td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colSpan="10" class="muted">No logs found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

</body>
</html>
