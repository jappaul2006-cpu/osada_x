<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Daily Log - LavaLust Gym</title>
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
        <h2>Edit Daily Log</h2>
        <p class="muted">Update daily log details.</p>
      </div>
      <a href="/staff" class="btn ghost small">Back to Dashboard</a>
    </div>

    <div class="panel">
      <h3>Edit Log: <?php echo htmlspecialchars($log['name'] ?? ''); ?></h3>
      <form action="/staff/daily-logs/update/<?php echo (int) ($log['id'] ?? 0); ?>" method="post" class="form">
        <label>Name
          <input type="text" name="name" value="<?php echo htmlspecialchars($log['name'] ?? ''); ?>" required />
        </label>
        <label>Customer Type
          <select name="customer_type">
            <option value="walk-in" <?php echo (($log['type'] ?? '') === 'walk-in') ? 'selected' : ''; ?>>Walk-in</option>
            <option value="student" <?php echo (($log['type'] ?? '') === 'student') ? 'selected' : ''; ?>>Student</option>
            <option value="member" <?php echo (($log['type'] ?? '') === 'member') ? 'selected' : ''; ?>>Member</option>
            <option value="non-member" <?php echo (($log['type'] ?? '') === 'non-member') ? 'selected' : ''; ?>>Non-Member</option>
          </select>
        </label>
        <label>Membership Type
          <select name="membership_type">
            <option value="daily" <?php echo (($log['membership_type'] ?? '') === 'daily') ? 'selected' : ''; ?>>Daily</option>
            <option value="2weeks" <?php echo (($log['membership_type'] ?? '') === '2weeks') ? 'selected' : ''; ?>>2 Weeks</option>
            <option value="monthly" <?php echo (($log['membership_type'] ?? '') === 'monthly') ? 'selected' : ''; ?>>Monthly</option>
          </select>
        </label>
        <label>Membership Amount (₱)
          <input type="number" name="membership_amount" id="membershipAmount" step="0.01" value="<?php echo number_format($log['membership_amount'] ?? 0, 2); ?>" />
        </label>
        <label>Weekly Renewal (₱)
          <input type="number" name="weekly_renewal" id="weeklyRenewal" step="0.01" value="<?php echo number_format($log['weekly_renewal'] ?? 0, 2); ?>" />
        </label>
        <label>Monthly Renewal (₱)
          <input type="number" name="monthly_renewal" id="monthlyRenewal" step="0.01" value="<?php echo number_format($log['monthly_renewal'] ?? 0, 2); ?>" />
        </label>
        <label>Membership Renewal (₱)
          <input type="number" name="membership_renewal" id="membershipRenewal" step="0.01" value="<?php echo number_format($log['membership_renewal'] ?? 0, 2); ?>" />
        </label>
        <label>Total Amount (₱)
          <input type="number" name="total_amount" id="totalAmount" step="0.01" value="<?php echo number_format($log['total_amount'] ?? 0, 2); ?>" readonly />
          <small class="muted">Auto-computed: Amount + Weekly + Monthly + Membership Renewal</small>
        </label>
        <label>Date
          <input type="date" name="date" value="<?php echo htmlspecialchars($log['date'] ?? ''); ?>" />
        </label>
        <label>Time
          <input type="time" name="time" value="<?php echo htmlspecialchars($log['time'] ?? ''); ?>" />
        </label>
        <button class="btn primary big" type="submit">Update Log</button>
      </form>
    </div>
  </main>
</div>

<script>
// Auto-compute Total Amount = Amount + Weekly + Monthly + Membership Renewal
const membershipAmount = document.getElementById('membershipAmount');
const weeklyRenewal    = document.getElementById('weeklyRenewal');
const monthlyRenewal   = document.getElementById('monthlyRenewal');
const membershipRenewal= document.getElementById('membershipRenewal');
const totalAmount      = document.getElementById('totalAmount');

function recomputeTotal() {
  const sum = [membershipAmount, weeklyRenewal, monthlyRenewal, membershipRenewal]
    .reduce((acc, input) => acc + (parseFloat(input.value) || 0), 0);
  totalAmount.value = sum.toFixed(2);
}
[membershipAmount, weeklyRenewal, monthlyRenewal, membershipRenewal].forEach(input => {
  input.addEventListener('input', recomputeTotal);
});
recomputeTotal();
</script>

</body>
</html>
