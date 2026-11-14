<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member - LavaLust Gym</title>
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
        <h2>Edit Member</h2>
        <p class="muted">Update member information.</p>
      </div>
      <a href="/admin/members" class="btn ghost small">Back to List</a>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="panel">
      <h3>Edit Member</h3>
      <form action="/admin/members/update/<?php echo (int) ($member['id'] ?? 0); ?>" method="post" class="form grid-form" id="memberForm">

        <!-- SECTION 0: Branch Location -->
        <div class="form-section">
          <h4 class="form-section-title">Branch Location</h4>
          <div class="form-row">
            <label>Branch
              <select name="branch_id" id="branchSelect" required>
                <option value="">— Select branch —</option>
                <?php foreach (($branches ?? []) as $b): ?>
                  <option value="<?php echo (int) ($b['id'] ?? 0); ?>" <?php echo ((int) ($member['branch_id'] ?? 0) === (int) ($b['id'] ?? 0)) ? 'selected' : ''; ?>><?php echo htmlspecialchars($b['location'] ?? ''); ?></option>
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
              <input name="name" placeholder="Full name" required value="<?php echo htmlspecialchars($member['name'] ?? ''); ?>" />
            </label>
            <label>Contact Number
              <input name="contact" placeholder="09xxxxxxxxx" value="<?php echo htmlspecialchars($member['contact'] ?? ''); ?>" />
            </label>
          </div>
          <div class="form-row">
            <label>Address
              <input name="address" placeholder="Street / Area" value="<?php echo htmlspecialchars($member['address'] ?? ''); ?>" />
            </label>
            <label>Birthday
              <input type="date" name="birthday" id="birthdayInput" value="<?php echo htmlspecialchars($member['birthday'] ?? ''); ?>" />
            </label>
          </div>
          <div class="form-row">
            <label>Age
              <input type="number" name="age" id="ageInput" placeholder="Auto from birthday" value="<?php echo htmlspecialchars($member['age'] ?? ''); ?>" />
            </label>
            <label>Gender
              <select name="gender">
                <option value="Male" <?php echo (($member['gender'] ?? '') === 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo (($member['gender'] ?? '') === 'Female') ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo (($member['gender'] ?? '') === 'Other') ? 'selected' : ''; ?>>Other</option>
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
                <option value="student" <?php echo (($member['customer_type'] ?? '') === 'student') ? 'selected' : ''; ?>>Student</option>
                <option value="member" <?php echo (($member['customer_type'] ?? '') === 'member') ? 'selected' : ''; ?>>Member</option>
                <option value="non-member" <?php echo (($member['customer_type'] ?? '') === 'non-member') ? 'selected' : ''; ?>>Non-Member</option>
              </select>
            </label>
          </div>
          <div class="form-row">
            <label>Yearly Start Date
              <input type="date" name="yearly_start_date" id="yearlyStartInput" value="<?php echo htmlspecialchars($member['yearly_start_date'] ?? ''); ?>" />
            </label>
            <label>Yearly End Date (auto, +1 year)
              <input type="date" name="yearly_end_date" id="yearlyEndInput" readonly value="<?php echo htmlspecialchars($member['yearly_end_date'] ?? ''); ?>" />
            </label>
          </div>
        </div>

        <!-- SECTION 3: Membership Type -->
        <div class="form-section">
          <h4 class="form-section-title">Membership Type</h4>
          <div class="form-row">
            <label>Membership Type
              <select name="membership_type" id="membershipTypeSelect">
                <option value="daily" <?php echo (($member['membership_type'] ?? '') === 'daily') ? 'selected' : ''; ?>>Daily</option>
                <option value="2weeks" <?php echo (($member['membership_type'] ?? '') === '2weeks') ? 'selected' : ''; ?>>2 Weeks</option>
                <option value="monthly" <?php echo (($member['membership_type'] ?? '') === 'monthly') ? 'selected' : ''; ?>>Monthly</option>
              </select>
            </label>
            <span id="priceDisplay" class="price-badge"></span>
          </div>
          <div class="form-row">
            <label>Start Date
              <input type="date" name="start_date" id="startDateInput" value="<?php echo htmlspecialchars($member['start_date'] ?? ''); ?>" />
            </label>
            <label>End Date (auto)
              <input type="date" name="end_date" id="endDateInput" readonly value="<?php echo htmlspecialchars($member['end_date'] ?? ''); ?>" />
            </label>
          </div>
        </div>

        <div class="form-actions">
          <button class="btn primary" type="submit">Update Member</button>
          <a href="/admin/members" class="btn ghost">Cancel</a>
        </div>
      </form>
    </div>

  </main>
</div>

<style>
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

<script>
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