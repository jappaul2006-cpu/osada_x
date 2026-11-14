<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - <?php echo $branch_name ?? 'Victoria'; ?></title>
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
      <a href="/staff-vic" class="active">Staff Dashboard</a>
      <a href="/staff-vic/todays-logs">Today's Log</a>
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
        <h2>Staff Dashboard</h2>
        <p class="muted">Check-in and daily logging for walk-in and registered members. <strong>Branch: <?php echo $branch_name ?? 'Victoria'; ?></strong></p>
      </div>
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
      <div class="card">
        <small>Registered Members</small>
        <h3><?php echo count($members ?? []); ?></h3>
      </div>
    </div>

    <div class="panel">
      <h3>New Check-in / Daily Log</h3>
      <form action="/staff-vic/checkin" method="post" class="form">
        <label>Registered Member (optional)
          <select name="member_id" id="memberSelect">
            <option value="">— Walk-in / Daily —</option>
            <?php foreach ($members ?? [] as $m): ?>
              <option value="<?php echo (int) ($m['id'] ?? 0); ?>" data-type="<?php echo htmlspecialchars($m['membership_type'] ?? ''); ?>" data-ctype="<?php echo htmlspecialchars($m['customer_type'] ?? ''); ?>"><?php echo htmlspecialchars($m['name'] ?? ''); ?> (<?php echo htmlspecialchars($m['membership_type'] ?? ''); ?>)</option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Walk-in Name
          <input type="text" name="walkin_name" id="walkinName" placeholder="Walk-in full name" />
        </label>
        <label>Customer Type
          <input type="text" name="customer_type" id="customerTypeInput" list="customerTypeOptions" placeholder="student / member / non-member / walk-in" autocomplete="off" />
          <datalist id="customerTypeOptions">
            <option value="student"></option>
            <option value="member"></option>
            <option value="non-member"></option>
            <option value="walk-in"></option>
          </datalist>
        </label>
        <label>Membership Type
          <input type="text" name="membership_type" id="membershipTypeDisplay" disabled />
        </label>
        <label>Amount (₱)
          <input type="number" name="amount" id="amountInput" step="0.01" value="0" readonly />
        </label>
        <input type="hidden" name="membership_amount" id="membershipAmountInput" value="0" />
        <p class="muted small" id="typeNote">Walk-in: choose/type the customer type and enter the amount manually. Registered member: customer type and membership type are filled in automatically.</p>
        <button class="btn primary big" type="submit">Log Check-in</button>
      </form>
    </div>

  </main>

<script>
const memberSelect = document.getElementById('memberSelect');
const walkinName = document.getElementById('walkinName');
const customerTypeInput = document.getElementById('customerTypeInput');
const membershipTypeDisplay = document.getElementById('membershipTypeDisplay');
const amountInput = document.getElementById('amountInput');
const membershipAmountInput = document.getElementById('membershipAmountInput');
const typeNote = document.getElementById('typeNote');
const dailyPrices = { student: 50, member: 65, 'non-member': 75 };

function updateType() {
  const sel = memberSelect.options[memberSelect.selectedIndex];
  const isWalkIn = memberSelect.value === '';
  walkinName.disabled = !isWalkIn;
  if (isWalkIn) {
    if (customerTypeInput.readOnly) {
      customerTypeInput.value = 'walk-in';
    }
    customerTypeInput.readOnly = false;
    if (customerTypeInput.value === '') {
      customerTypeInput.value = 'walk-in';
    }
    membershipTypeDisplay.value = 'daily';
    amountInput.disabled = false;
    amountInput.value = amountInput.value || '0';
    membershipAmountInput.value = amountInput.value;
    typeNote.textContent = 'Walk-in: choose/type the customer type and enter the amount manually.';
  } else {
    const mtype   = sel.dataset.type || 'daily';
    const ctype   = sel.dataset.ctype || 'member';
    customerTypeInput.value = ctype;
    customerTypeInput.readOnly = true;
    membershipTypeDisplay.value = mtype;
    if (mtype === 'daily') {
      const price = dailyPrices[ctype] ?? 65;
      amountInput.value = price;
      amountInput.disabled = true;
      membershipAmountInput.value = price;
      typeNote.textContent = 'Registered member (daily): ₱' + price + ' (' + ctype + ").";
    } else {
      amountInput.value = '0';
      amountInput.disabled = true;
      membershipAmountInput.value = '0';
      typeNote.textContent = 'Registered member (' + mtype + '): no payment on check-in (covered by membership).';
    }
  }
}
memberSelect.addEventListener('change', updateType);
memberSelect.querySelectorAll('option').forEach(opt => {
  if (opt.value) {
    const mtype = opt.text.match(/\((daily|2weeks|monthly)\)/);
    opt.dataset.type  = mtype ? mtype[1] : 'daily';
    opt.dataset.ctype = opt.getAttribute('data-ctype') || 'member';
  }
});
updateType();

</script>

</body>
</html>
