<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses Management - LavaLust Gym</title>
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
        <h2>Expenses Management</h2>
        <p class="muted">Add and update gym expenses. Delete is disabled for staff.</p>
      </div>
      <div class="card green inline"><small>Total</small><h3>₱<?php echo number_format($total ?? 0, 2); ?></h3></div>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="panel">
      <h3>New Expense</h3>
      <form action="/staff/expenses/store" method="post" class="form grid-form">
        <label>Expense Name
          <input name="name" placeholder="e.g. Electricity, Water, Equipment" required />
        </label>
        <label>Price (₱)
          <input type="number" name="price" step="0.01" min="0" placeholder="0" required />
        </label>
        <label>Date
          <input type="date" name="date" />
        </label>
        <div class="form-actions">
          <button class="btn primary" type="submit">Add</button>
        </div>
      </form>
    </div>

    <div class="panel">
      <h3>List (<?php echo count($expenses ?? []); ?>)</h3>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Name</th><th>Price</th><th>Date</th><th>Action</th></tr></thead>
          <tbody>
            <?php if (!empty($expenses)): foreach ($expenses as $x): ?>
              <tr>
                <td><?php echo htmlspecialchars($x['name'] ?? ''); ?></td>
                <td>₱<?php echo number_format($x['price'] ?? 0, 2); ?></td>
                <td><?php echo htmlspecialchars($x['date'] ?? ''); ?></td>
                <td class="actions">
                  <a href="/staff/expenses/edit/<?php echo (int) ($x['id'] ?? 0); ?>" class="btn small primary">Update</a>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colSpan="4" class="muted">No expenses found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<style>
  .actions .btn.primary {
    background: #2563eb;
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
    background: #1d4ed8;
  }
</style>

</body>
</html>
