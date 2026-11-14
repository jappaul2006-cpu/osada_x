<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Management - LavaLust Gym</title>
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
      <a href="/admin/members" class="active">Member Management</a>
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
        <h2>Member Management List</h2>
        <p class="muted">Add, update, and delete members. Weekly ₱350 • Monthly ₱600 • Daily from staff log. <strong>Branch: <?php echo htmlspecialchars($activeBranch ?? '—'); ?></strong></p>
      </div>
    </div>

    <div class="panel">
      <h3>New Member</h3>
      <form action="/admin/members/store" method="post" class="form grid-form" id="memberForm">

        <!-- SECTION 0: Branch Location -->
        <div class="form-section">
          <h4 class="form-section-title">Branch Location</h4>
          <div class="form-row">
            <label>Branch
              <select name="branch_id" id="branchSelect" required>
                <option value="">— Select branch —</option>
                <?php foreach (($branches ?? []) as $b): ?>
                  <option value="<?php echo (int) ($b['id'] ?? 0); ?>" <?php echo ((int) ($branch ?? 0) === (int) ($b['id'] ?? 0)) ? 'selected' : ''; ?>><?php echo htmlspecialchars($b['location'] ?? ''); ?></option>
                <?php endforeach; ?>
              </select>
            </label>
          </div>
        </div>

        <!-- SECTION 1: Personal Information -->
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

        <!-- SECTION 2: Customer Type -->
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

        <!-- SECTION 3: Membership Type -->
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
            <input type="hidden" name="branch" value="<?php echo (int) ($branch ?? 0); ?>" />
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

      <!-- BRANCH TABS - parang nav bar pero para sa table -->
      <nav class="branch-tabs">
        <?php foreach (($branches ?? []) as $b): ?>
          <?php $bid = (int) ($b['id'] ?? 0); ?>
          <a class="branch-tab b-<?php echo $bid; ?><?php echo ($bid === (int) ($branch ?? 0)) ? ' active' : ''; ?>"
             href="/admin/members?branch=<?php echo $bid; ?>&amp;q=<?php echo urlencode($q ?? ''); ?>&amp;filter=<?php echo urlencode($filter ?? 'all'); ?>">
            <?php echo htmlspecialchars($b['location'] ?? ''); ?>
            <span class="tab-count"><?php echo (int) (($branchCounts ?? [])[$bid] ?? 0); ?></span>
          </a>
        <?php endforeach; ?>
      </nav>

      <div class="table-wrap">
        <table>
          <thead><tr><th>Name</th><th>Contact</th><th>Address</th><th>Birthday</th><th>Age</th><th>Gender</th><th>Customer Type</th><th>Type</th><th>Yearly End</th><th>Membership End</th><th>Branch</th><th>Action</th></tr></thead>
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
                <td><span class="badge branch-badge b-<?php echo (int) ($m['branch_id'] ?? 0); ?>"><?php echo htmlspecialchars($branchNames[(int) ($m['branch_id'] ?? 0)] ?? '—'); ?></span></td>
                <td class="actions">
                  <a href="/admin/members/edit/<?php echo (int) ($m['id'] ?? 0); ?>" class="btn small primary" style="margin-right:6px;">Update</a>
                  <form action="/admin/members/delete/<?php echo (int) ($m['id'] ?? 0); ?>" method="post" style="display:inline;" onsubmit="event.preventDefault(); showConfirm('Delete this member?', function(){ this.submit(); }.bind(this));">
                    <button class="btn small danger" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colSpan="12" class="muted">No results found.</td></tr>
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
  /* Form section styling */
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
    color: #16233A;
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
    border-color: #16233A;
    box-shadow: 0 0 0 3px rgba(22, 35, 58, 0.12);
  }
  .price-badge {
    display: inline-block;
    margin-top: 42px;
    padding: 8px 16px;
    background: #E9EEF6;
    color: #16233A;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
    align-self: flex-start;
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
    <div id="confirmMessage" style="margin-bottom:24px; font-size:16px; color:#111; font-weight:600;">Delete this member?</div>
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

<script>
// ===== NEW MEMBER FORM LOGIC =====

// Auto-calculate age from birthday (current year - birth year)
document.getElementById('birthdayInput').addEventListener('change', function() {
  const bday = this.value;
  const ageInput = document.getElementById('ageInput');
  if (bday && ageInput.value === '') {
    const birthYear = parseInt(bday.substring(0, 4));
    const currentYear = new Date().getFullYear();
    ageInput.value = currentYear - birthYear;
  }
});

// Toggle yearly subscription fields based on customer type
function toggleYearlyFields() {
  const customerType = document.getElementById('customerTypeSelect').value;
  const yearlyStart = document.getElementById('yearlyStartInput');
  const yearlyEnd = document.getElementById('yearlyEndInput');

  // Student and non-member: no yearly subscription
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

// Calculate yearly end date (start + 1 year)
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

// Toggle start date field based on membership type
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

// Calculate end date based on membership type
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

// Update price display based on customer type and membership type
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

// Event listeners
document.getElementById('customerTypeSelect').addEventListener('change', toggleYearlyFields);
document.getElementById('yearlyStartInput').addEventListener('change', calculateYearlyEnd);
document.getElementById('membershipTypeSelect').addEventListener('change', toggleStartDate);
document.getElementById('startDateInput').addEventListener('change', calculateEndDate);

// Initialize on page load
toggleYearlyFields();
toggleStartDate();
updatePriceDisplay();
</script>

</body>
</html>