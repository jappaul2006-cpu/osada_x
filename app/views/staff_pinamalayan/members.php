<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Management - <?php echo $branch_name ?? 'Pinamalayan'; ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        :root { --brand-yellow: #F59E0B; --brand-dark: #0A0F0D; }
        .sidebar.staff { background: var(--brand-dark); border-right: 3px solid var(--brand-yellow); }
        .sidebar.staff .side-nav a.active { background: var(--brand-yellow); color: var(--brand-dark); }
        .btn.primary { background: var(--brand-yellow); color: var(--brand-dark); border-color: var(--brand-yellow); }
        .btn.primary:hover { background: var(--brand-dark); border-color: var(--brand-dark); color: #fff; }
        th { background: var(--brand-dark); color: #fff; }
        .badge { border: 1px solid var(--brand-yellow); }
        .badge.daily { background: var(--brand-dark); color: var(--brand-yellow); border-color: var(--brand-yellow); }
        .badge.weekly { background: var(--brand-yellow); color: var(--brand-dark); border-color: var(--brand-yellow); }
        .badge.monthly { background: var(--brand-dark); color: var(--brand-yellow); border-color: var(--brand-yellow); }
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
      <a href="/staff-pina/members" class="active">Member Management</a>
      <a href="/staff-pina/expenses">Expenses Management</a>
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
        <h2>Member Management List</h2>
        <p class="muted">Add and update members. Delete is disabled for staff. Branch: <strong><?php echo $branch_name ?? 'Pinamalayan'; ?></strong></p>
      </div>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="panel">
      <h3>New Member</h3>
      <form action="/staff-pina/members/store" method="post" class="form grid-form" id="memberForm">
        <div class="form-section">
          <h4 class="form-section-title">Personal Information</h4>
          <div class="form-row">
            <label>Name
              <input name="name" placeholder="Full name" required />
            </label>
            <label>Contact Number
              <input name="contact" placeholder="09xxxxxxxxx" />
            </label>
          </div>
          <div class="form-row">
            <label>Address
              <input name="address" placeholder="Street / Area" />
            </label>
            <label>Birthday
              <input type="date" name="birthday" id="birthdayInput" />
            </label>
          </div>
          <div class="form-row">
            <label>Age
              <input type="number" name="age" id="ageInput" placeholder="Auto from birthday" />
            </label>
            <label>Gender
              <select name="gender">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
            </label>
          </div>
        </div>

        <div class="form-section">
          <h4 class="form-section-title">Customer Type</h4>
          <div class="form-row">
            <label>Customer Type
              <select name="customer_type" id="customerTypeSelect">
                <option value="student">Student</option>
                <option value="member">Member</option>
                <option value="non-member">Non-Member</option>
              </select>
            </label>
          </div>
          <div class="form-row">
            <label>Yearly Start Date
              <input type="date" name="yearly_start_date" id="yearlyStartInput" />
            </label>
            <label>Yearly End Date (auto, +1 year)
              <input type="date" name="yearly_end_date" id="yearlyEndInput" readonly />
            </label>
          </div>
        </div>

        <div class="form-section">
          <h4 class="form-section-title">Membership Type</h4>
          <div class="form-row">
            <label>Membership Type
              <select name="membership_type" id="membershipTypeSelect">
                <option value="daily">Daily</option>
                <option value="2weeks">2 Weeks</option>
                <option value="monthly">Monthly</option>
              </select>
            </label>
            <span id="priceDisplay" class="price-badge"></span>
          </div>
          <div class="form-row">
            <label>Start Date
              <input type="date" name="start_date" id="startDateInput" />
            </label>
            <label>End Date (auto)
              <input type="date" name="end_date" id="endDateInput" readonly />
            </label>
          </div>
        </div>

        <div class="form-actions">
          <button class="btn primary" type="submit">Add Member</button>
        </div>
      </form>
    </div>

    <div class="panel">
      <div class="panel-head wrap">
        <h3>List (<?php echo count($members ?? []); ?>)</h3>
        <div class="toolbar">
          <form method="get" class="toolbar" style="display:flex;gap:8px;align-items:center;">
            <input class="search" name="q" placeholder="Search name / contact / address..." value="<?php echo htmlspecialchars($q ?? ''); ?>" />
            <select name="filter" onchange="this.form.submit()">
              <option value="all" <?php echo (($filter ?? '') === 'all') ? 'selected' : ''; ?>>All</option>
              <option value="daily" <?php echo (($filter ?? '') === 'daily') ? 'selected' : ''; ?>>Daily</option>
              <option value="2weeks" <?php echo (($filter ?? '') === '2weeks') ? 'selected' : ''; ?>>2 Weeks</option>
              <option value="monthly" <?php echo (($filter ?? '') === 'monthly') ? 'selected' : ''; ?>>Monthly</option>
            </select>
          </form>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Name</th><th>Contact</th><th>Address</th><th>Birthday</th><th>Age</th><th>Gender</th><th>Customer Type</th><th>Type</th><th>Yearly End</th><th>Membership End</th><th>Action</th></tr></thead>
          <tbody>
            <?php if (!empty($members)): foreach ($members as $m): ?>
              <tr>
                <td><?php echo htmlspecialchars($m['name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($m['contact'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($m['address'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($m['birthday'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($m['age'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($m['gender'] ?? ''); ?></td>
                <td><span class="badge"><?php echo htmlspecialchars(ucfirst($m['customer_type'] ?? '')); ?></span></td>
                <td><span class="badge <?php echo $m['membership_type'] ?? 'monthly'; ?>"><?php echo htmlspecialchars(strtoupper($m['membership_type'] ?? '')); ?></span></td>
                <td><?php echo htmlspecialchars($m['yearly_end_date'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($m['end_date'] ?? ''); ?></td>
                <td class="actions">
                  <a href="/staff-pina/members/edit/<?php echo (int) ($m['id'] ?? 0); ?>" class="btn small primary">Update</a>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colSpan="11" class="muted">No results found.</td></tr>
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
  .form-section {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 20px;
  }
  .form-section-title {
    margin: 0 0 16px 0;
    font-size: 15px;
    font-weight: 700;
    color: #1e293b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 10px;
  }
  .form-row {
    display: flex;
    gap: 16px;
    margin-bottom: 14px;
    flex-wrap: wrap;
  }
  .form-row > label {
    flex: 1;
    min-width: 200px;
    display: flex;
    flex-direction: column;
  }
  .form-row > label > select,
  .form-row > label > input {
    margin-top: 6px;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    background: #fff;
  }
  .form-row > label > select:focus,
  .form-row > label > input:focus {
    outline: none;
    border-color: #F59E0B;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
  }
  .price-badge {
    display: inline-block;
    margin-top: 42px;
    padding: 8px 16px;
    background: #fef3c7;
    color: #92400e;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
    align-self: flex-start;
  }
</style>

<script>
document.getElementById('birthdayInput').addEventListener('change', function() {
  const bday = this.value;
  const ageInput = document.getElementById('ageInput');
  if (bday && ageInput.value === '') {
    const birthYear = parseInt(bday.substring(0, 4));
    const currentYear = new Date().getFullYear();
    ageInput.value = currentYear - birthYear;
  }
});

function toggleYearlyFields() {
  const customerType = document.getElementById('customerTypeSelect').value;
  const yearlyStart = document.getElementById('yearlyStartInput');
  const yearlyEnd = document.getElementById('yearlyEndInput');
  if (customerType === 'student' || customerType === 'non-member') {
    yearlyStart.disabled = true;
    yearlyEnd.disabled = true;
    yearlyStart.value = '';
    yearlyEnd.value = '';
  } else {
    yearlyStart.disabled = false;
    yearlyEnd.disabled = false;
    calculateYearlyEnd();
  }
}

function calculateYearlyEnd() {
  const start = document.getElementById('yearlyStartInput').value;
  const endInput = document.getElementById('yearlyEndInput');
  if (start) {
    const d = new Date(start + 'T00:00:00');
    d.setFullYear(d.getFullYear() + 1);
    endInput.value = d.toISOString().split('T')[0];
  } else {
    endInput.value = '';
  }
}

function toggleStartDate() {
  const mtype = document.getElementById('membershipTypeSelect').value;
  const startInput = document.getElementById('startDateInput');
  const endInput = document.getElementById('endDateInput');
  if (mtype === 'daily') {
    startInput.disabled = true;
    endInput.disabled = true;
    startInput.value = '';
    endInput.value = '';
  } else {
    startInput.disabled = false;
    endInput.disabled = false;
    calculateEndDate();
  }
  updatePriceDisplay();
}

function calculateEndDate() {
  const mtype = document.getElementById('membershipTypeSelect').value;
  const start = document.getElementById('startDateInput').value;
  const endInput = document.getElementById('endDateInput');
  if (start && (mtype === '2weeks' || mtype === 'monthly')) {
    const d = new Date(start + 'T00:00:00');
    if (mtype === '2weeks') {
      d.setDate(d.getDate() + 14);
    } else {
      d.setDate(d.getDate() + 30);
    }
    endInput.value = d.toISOString().split('T')[0];
  } else {
    endInput.value = '';
  }
}

function updatePriceDisplay() {
  const customerType = document.getElementById('customerTypeSelect').value;
  const mtype = document.getElementById('membershipTypeSelect').value;
  const priceDisplay = document.getElementById('priceDisplay');
  const prices = {
    student: { daily: '₱50', '2weeks': '₱350', monthly: '₱600' },
    member: { daily: '₱65', '2weeks': '₱450', monthly: '₱650' },
    'non-member': { daily: '₱75', '2weeks': '₱650', monthly: '₱850' }
  };
  const price = prices[customerType]?.[mtype] ?? '—';
  priceDisplay.textContent = 'Price: ' + price;
  priceDisplay.style.display = price === '—' ? 'none' : 'inline';
}

document.getElementById('customerTypeSelect').addEventListener('change', toggleYearlyFields);
document.getElementById('yearlyStartInput').addEventListener('change', calculateYearlyEnd);
document.getElementById('membershipTypeSelect').addEventListener('change', toggleStartDate);
document.getElementById('startDateInput').addEventListener('change', calculateEndDate);
toggleYearlyFields();
toggleStartDate();
updatePriceDisplay();
</script>

</body>
</html>
