<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses History - LavaLust Gym</title>
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
      <a href="/admin/daily-logs">Daily Log History</a>
      <a href="/admin/expenses" class="active">Expenses History</a>
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
        <h2>Expenses History</h2>
        <p class="muted">Gym expenses by name, price, and date. <strong>Branch: <?php echo htmlspecialchars($activeBranch ?? '—'); ?></strong></p>
      </div>
      <div class="card green inline"><small>Total</small><h3>₱<?php echo number_format($total ?? 0, 2); ?></h3></div>
    </div>

    <div class="panel">
      <h3>New Expense</h3>
      <form action="/admin/expenses/store" method="post" class="form grid-form">
        <label>Expense Name
          <input name="name" placeholder="e.g. Electricity, Water, Equipment" required />
        </label>
        <label>Price (₱)
          <input type="number" name="price" step="0.01" min="0" placeholder="0" required />
        </label>
        <label>Date
          <input type="date" name="date" />
        </label>
        <label>Branch Location
          <select name="branch_id" id="branchSelect" required>
            <option value="">— Select branch —</option>
            <?php foreach (($branches ?? []) as $b): ?>
              <option value="<?php echo (int) ($b['id'] ?? 0); ?>" <?php echo ((int) ($branch ?? 0) === (int) ($b['id'] ?? 0)) ? 'selected' : ''; ?>><?php echo htmlspecialchars($b['location'] ?? ''); ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <div class="form-actions">
          <button class="btn primary" type="submit">Add</button>
        </div>
      </form>
    </div>

    <div class="panel">
      <div class="panel-head wrap">
        <h3>List (<?php echo count($expenses ?? []); ?>)</h3>
      </div>

      <!-- BRANCH TABS - parang nav bar pero para sa table -->
      <nav class="branch-tabs">
        <?php foreach (($branches ?? []) as $b): ?>
          <?php $bid = (int) ($b['id'] ?? 0); ?>
          <a class="branch-tab b-<?php echo $bid; ?><?php echo ($bid === (int) ($branch ?? 0)) ? ' active' : ''; ?>"
             href="/admin/expenses?branch=<?php echo $bid; ?>">
            <?php echo htmlspecialchars($b['location'] ?? ''); ?>
            <span class="tab-count"><?php echo (int) (($branchCounts ?? [])[$bid] ?? 0); ?></span>
          </a>
        <?php endforeach; ?>
      </nav>

      <div class="table-wrap">
        <table>
          <thead><tr><th>Name</th><th>Price</th><th>Date</th><th>Branch</th><th>Action</th></tr></thead>
          <tbody>
            <?php if (!empty($expenses)): foreach ($expenses as $x): ?>
              <tr>
                <td><?php echo htmlspecialchars($x['name'] ?? ''); ?></td>
                <td>₱<?php echo number_format($x['price'] ?? 0, 2); ?></td>
                <td><?php echo htmlspecialchars($x['date'] ?? ''); ?></td>
                <td><span class="badge branch-badge b-<?php echo (int) ($x['branch_id'] ?? 0); ?>"><?php echo htmlspecialchars($branchNames[(int) ($x['branch_id'] ?? 0)] ?? '—'); ?></span></td>
                <td class="actions">
                  <a href="/admin/expenses/edit/<?php echo (int) ($x['id'] ?? 0); ?>" class="btn small primary" style="margin-right:6px;">Update</a>
                  <form action="/admin/expenses/delete/<?php echo (int) ($x['id'] ?? 0); ?>" method="post" style="display:inline;" onsubmit="event.preventDefault(); showConfirm('Delete this expense?', function(){ this.submit(); }.bind(this));">
                    <button class="btn small danger" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colSpan="5" class="muted">No expenses found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<style>
  .actions .btn.danger {
    background: #dc2626;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }
  .actions .btn.danger:hover {
    background: #b91c1c;
  }
  .actions .btn.primary {
    background: #16233A;
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
    background: #0B0E14;
  }
</style>
<style>
  #confirmDialog {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 2000;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
  }
  #confirmDialog .confirm-box {
    background: #fff;
    border-radius: 16px;
    padding: 28px 36px;
    max-width: 440px;
    width: 90%;
    text-align: center;
    box-shadow: 0 24px 80px rgba(0, 0, 0, 0.35);
    border: 1px solid var(--line, #e5e7eb);
    animation: confirmSlide 0.2s ease-out;
  }
  @keyframes confirmSlide {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
  }
  #confirmDialog #confirmMessage {
    margin-bottom: 24px;
    font-size: 16px;
    color: #111;
    font-weight: 600;
  }
  #confirmDialog .confirm-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
  }
  #confirmDialog #confirmOk {
    background: #dc2626;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 24px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }
  #confirmDialog #confirmOk:hover {
    background: #b91c1c;
  }
  #confirmDialog #confirmCancel {
    background: #f3f4f6;
    color: #374151;
    border: none;
    border-radius: 8px;
    padding: 10px 24px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
  }
  #confirmDialog #confirmCancel:hover {
    background: #e5e7eb;
  }
</style>
<div id="confirmDialog">
  <div class="confirm-box">
    <div id="confirmMessage" style="margin-bottom:24px; font-size:16px; color:#111; font-weight:600;">Delete this expense?</div>
    <div class="confirm-actions">
      <button id="confirmOk" style="background:#dc2626; color:#fff; border:none; border-radius:8px; padding:10px 24px; font-size:14px; font-weight:600; cursor:pointer;">Yes, Delete</button>
      <button id="confirmCancel" style="background:#f3f4f6; color:#374151; border:none; border-radius:8px; padding:10px 24px; font-size:14px; font-weight:500; cursor:pointer;">Cancel</button>
    </div>
  </div>
</div>

<script>
let confirmCallback = null;
function showConfirm(msg, cb) {
  document.getElementById('confirmMessage').textContent = msg;
  confirmCallback = cb;
  document.getElementById('confirmDialog').style.display = 'flex';
}
document.getElementById('confirmOk').addEventListener('click', function(){
  document.getElementById('confirmDialog').style.display = 'none';
  if (confirmCallback) confirmCallback();
  confirmCallback = null;
});
document.getElementById('confirmCancel').addEventListener('click', function(){
  document.getElementById('confirmDialog').style.display = 'none';
  confirmCallback = null;
});
</script>

</body>
</html>
