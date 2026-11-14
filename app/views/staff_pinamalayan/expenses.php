<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses Management - <?php echo $branch_name ?? 'Pinamalayan'; ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        :root { --brand-yellow: #F59E0B; --brand-dark: #0A0F0D; }
        .sidebar.staff { background: var(--brand-dark); border-right: 3px solid var(--brand-yellow); }
        .sidebar.staff .side-nav a.active { background: var(--brand-yellow); color: var(--brand-dark); }
        .btn.primary { background: var(--brand-yellow); color: var(--brand-dark); border-color: var(--brand-yellow); }
        .btn.primary:hover { background: var(--brand-dark); border-color: var(--brand-dark); color: #fff; }
        th { background: var(--brand-dark); color: #fff; }
    </style>
</head>
<body>

<div class="shell">
  <aside class="sidebar staff">
    <div class="side-brand">
      <div class="logo-circle sm" style="background: linear-gradient(135deg, #0A0F0D, #F59E0B);">PX</div>
      <div>
        <strong><?php echo $branch_name ?? 'Pinamalayan'; ?> Gym</strong>
        <small>Staff Panel</small>
      </div>
    </div>
    <nav class="side-nav">
      <a href="/staff-pina">Staff Dashboard</a>
      <a href="/staff-pina/todays-logs">Today's Log</a>
      <a href="/staff-pina/members">Member Management</a>
      <a href="/staff-pina/expenses" class="active">Expenses Management</a>
    </nav>
    <div class="side-foot">
      <small>Signed in as: <b><?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'staff'); ?></b></small>
      <small>Branch: <b><?php echo $branch_name ?? 'Pinamalayan'; ?></b></small>
      <a href="/logout" class="btn ghost small">Logout</a>
    </div>
  </aside>

  <main class="content">
    <div class="page-head">
      <div>
        <h2>Expenses Management</h2>
        <p class="muted">Add and update gym expenses. Delete is disabled for staff. Branch: <strong><?php echo $branch_name ?? 'Pinamalayan'; ?></strong></p>
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
      <form action="/staff-pina/expenses/store" method="post" class="form grid-form">
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
                  <a href="/staff-pina/expenses/edit/<?php echo (int) ($x['id'] ?? 0); ?>" class="btn small primary">Update</a>
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

<style>
  .actions .btn.primary {
    background: #F59E0B;
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
    background: #0A0F0D;
    color: #fff;
  }
</style>

</body>
</html>
