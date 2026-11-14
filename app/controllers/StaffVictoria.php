<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * StaffVictoria Controller - Victoria branch staff portal.
 * Red + Black theme. Branch ID: 3.
 * Same features as Calapan staff, branch-filtered data.
 */
class StaffVictoria extends MY_Controller {

    const BRANCH_ID = 3;
    const BRANCH_NAME = 'Victoria';
    const THEME = 'victoria'; // red + black

    public function before_action()
    {
        parent::before_action();
        $this->_require_role('staff');
        $this->_require_branch();
    }

    private function _require_role($role)
    {
        if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== $role) {
            $_SESSION['error'] = 'Please log in as ' . $role . ' to access this page.';
            header('Location: /login');
            exit;
        }
    }

    private function _require_branch()
    {
        $bid = (int) ($_SESSION['user']['branch_id'] ?? 0);
        if ($bid !== self::BRANCH_ID) {
            $_SESSION['error'] = 'This portal is for ' . self::BRANCH_NAME . ' branch staff only.';
            header('Location: /login');
            exit;
        }
    }

    private function _branch_id()
    {
        return self::BRANCH_ID;
    }

    private function _filter_branch(array $rows): array
    {
        $bid = self::BRANCH_ID;
        return array_values(array_filter($rows, function ($r) use ($bid) {
            return (int) ($r['branch_id'] ?? 0) === $bid;
        }));
    }

    // =========================================================================
    // DASHBOARD
    // =========================================================================
    public function dashboard()
    {
        $this->call->model('Member');
        $this->call->model('DailyLog');

        $members = $this->_filter_branch($this->Member->_all());
        $logs    = $this->_compute_log_totals($this->_filter_branch($this->DailyLog->_all()));

        $today = date('Y-m-d');
        $today_logs = array_values(array_filter($logs, fn($l) => ($l['date'] ?? '') === $today));

        $today_cash = array_reduce(
            $today_logs,
            fn($c, $l) => $c + (float) ($l['total_amount'] ?? 0),
            0
        );

        $q = trim($_GET['q'] ?? '');
        $lookup = array_values(array_filter($members, function ($m) use ($q) {
            return $q === '' || stripos(($m['name'] ?? '') . ' ' . ($m['contact'] ?? ''), $q) !== false;
        }));
        $lookup = array_slice($lookup, 0, 10);

        $data = [
            'members'    => $members,
            'logs'       => $logs,
            'today_logs' => array_reverse($today_logs),
            'today_count'=> count($today_logs),
            'today_cash' => $today_cash,
            'today'      => $today,
            'q'          => $q,
            'lookup'     => $lookup,
            'branch_id'  => self::BRANCH_ID,
            'branch_name'=> self::BRANCH_NAME,
            'theme'      => self::THEME,
        ];

        $this->call->view('staff_victoria/dashboard', $data);
    }

    // =========================================================================
    // TODAY'S LOG PAGE
    // =========================================================================
    public function todaysLogs()
    {
        $this->call->model('DailyLog');

        $logs  = $this->_compute_log_totals($this->_filter_branch($this->DailyLog->_all()));
        $today = date('Y-m-d');
        $today_logs = array_values(array_filter($logs, fn($l) => ($l['date'] ?? '') === $today));

        $today_cash = array_reduce(
            $today_logs,
            fn($c, $l) => $c + (float) ($l['total_amount'] ?? 0),
            0
        );

        $data = [
            'today_logs'  => array_reverse($today_logs),
            'today_count' => count($today_logs),
            'today_cash'  => $today_cash,
            'today'       => $today,
            'today_label' => date('F d, Y'),
            'branch_id'   => self::BRANCH_ID,
            'branch_name' => self::BRANCH_NAME,
            'theme'       => self::THEME,
        ];

        $this->call->view('staff_victoria/todays_logs', $data);
    }

    // =========================================================================
    // CHECK-IN
    // =========================================================================
    public function checkin()
    {
        $this->call->model('DailyLog');
        $this->call->model('Member');

        $memberId = (int) ($_POST['member_id'] ?? 0);
        $walkin   = trim($_POST['walkin_name'] ?? '');
        $amount   = (float) ($_POST['amount'] ?? 0);

        $dailyPrices = ['student' => 50, 'member' => 65, 'non-member' => 75];
        $validCustomerTypes = ['student', 'member', 'non-member', 'walk-in'];

        $membershipType = 'daily';
        $customerType   = strtolower(trim($_POST['customer_type'] ?? ''));
        $customerType   = str_replace(' ', '-', $customerType);
        if ($customerType === 'nonmember') {
            $customerType = 'non-member';
        }
        if (!in_array($customerType, $validCustomerTypes, true)) {
            $customerType = $walkin !== '' ? 'walk-in' : 'member';
        }

        $label = $walkin;
        $memberIdDb = null;
        $membershipAmount = 0;

        if ($memberId > 0) {
            $member = $this->Member->_find($memberId);
            if ($member) {
                $label           = $member['name'];
                $memberIdDb      = (int) $member['id'];
                $membershipType  = $member['membership_type'] ?? 'daily';
                $customerType    = $member['customer_type'] ?? 'member';

                if ($membershipType === 'daily') {
                    $membershipAmount = $dailyPrices[$customerType] ?? 65;
                } else {
                    $membershipAmount = 0;
                }
            }
        } else {
            $membershipAmount = $amount;
        }

        $totalAmount = $membershipAmount;

        if ($label === '') {
            $_SESSION['error'] = 'Please select a member or enter a walk-in name.';
            header('Location: /staff-vic');
            exit;
        }

        $this->DailyLog->insert([
            'member_id'         => $memberIdDb,
            'name'              => $label,
            'type'              => $customerType,
            'membership_type'   => $membershipType,
            'membership_amount' => $membershipAmount,
            'weekly_renewal'    => 0,
            'monthly_renewal'   => 0,
            'membership_renewal'=> 0,
            'total_amount'      => $totalAmount,
            'date'              => date('Y-m-d'),
            'time'              => date('H:i'),
            'branch_id'         => self::BRANCH_ID,
        ]);

        header('Location: /staff-vic');
        exit;
    }

    // =========================================================================
    // EDIT DAILY LOG
    // =========================================================================
    public function editDailyLog($id)
    {
        $this->call->model('DailyLog');
        $log = $this->DailyLog->_find((int) $id);

        if (!$log) {
            $_SESSION['error'] = 'Daily log not found.';
            header('Location: /staff-vic');
            exit;
        }

        if ((int) ($log['branch_id'] ?? 0) !== self::BRANCH_ID) {
            $_SESSION['error'] = 'You can only edit daily logs from ' . self::BRANCH_NAME . ' branch.';
            header('Location: /staff-vic');
            exit;
        }

        $log = $this->_compute_log_totals([$log])[0];
        $this->call->view('staff_victoria/edit_daily_log', ['log' => $log, 'branch_id' => self::BRANCH_ID, 'branch_name' => self::BRANCH_NAME, 'theme' => self::THEME]);
    }

    public function updateDailyLog($id)
    {
        $this->call->model('DailyLog');
        $log = $this->DailyLog->_find((int) $id);

        if (!$log || (int) ($log['branch_id'] ?? 0) !== self::BRANCH_ID) {
            $_SESSION['error'] = 'Daily log not found or not accessible.';
            header('Location: /staff-vic');
            exit;
        }

        $name               = trim($_POST['name'] ?? '');
        $customerType       = strtolower(trim($_POST['customer_type'] ?? 'walk-in'));
        $customerType       = str_replace(' ', '-', $customerType);
        if ($customerType === 'nonmember') {
            $customerType = 'non-member';
        }
        if (!in_array($customerType, ['student', 'member', 'non-member', 'walk-in'], true)) {
            $customerType = 'walk-in';
        }
        $membershipType     = $_POST['membership_type'] ?? 'daily';
        if (!in_array($membershipType, ['daily', '2weeks', 'monthly'], true)) {
            $membershipType = 'daily';
        }
        $membershipAmount   = (float) ($_POST['membership_amount'] ?? 0);
        $weeklyRenewal      = (float) ($_POST['weekly_renewal'] ?? 0);
        $monthlyRenewal     = (float) ($_POST['monthly_renewal'] ?? 0);
        $membershipRenewal  = (float) ($_POST['membership_renewal'] ?? 0);
        $totalAmount        = $membershipAmount + $weeklyRenewal + $monthlyRenewal + $membershipRenewal;
        $date               = $_POST['date'] ?? '';
        $time               = $_POST['time'] ?? '';

        if ($name === '') {
            $_SESSION['error'] = 'Please enter a name.';
            header('Location: /staff-vic');
            exit;
        }

        $this->DailyLog->update((int) $id, [
            'name'              => $name,
            'type'              => $customerType,
            'membership_type'   => $membershipType,
            'membership_amount' => $membershipAmount,
            'weekly_renewal'    => $weeklyRenewal,
            'monthly_renewal'   => $monthlyRenewal,
            'membership_renewal'=> $membershipRenewal,
            'total_amount'      => $totalAmount,
            'date'              => $date ?: null,
            'time'              => $time ?: null,
        ]);

        $_SESSION['success'] = 'Daily log updated successfully.';
        header('Location: /staff-vic');
        exit;
    }

    // =========================================================================
    // MEMBERS
    // =========================================================================
    public function members()
    {
        $this->call->model('Member');

        $q      = trim($_GET['q'] ?? '');
        $filter = $_GET['filter'] ?? 'all';

        $members = $this->_filter_branch($this->Member->_all());

        $filtered = array_values(array_filter($members, function ($m) use ($q, $filter) {
            $okQ = stripos(($m['name'] ?? '') . ' ' . ($m['contact'] ?? '') . ' ' . ($m['address'] ?? ''), $q) !== false;
            $okF = ($filter === 'all') || ($m['membership_type'] === $filter);
            return $okQ && $okF;
        }));

        $data = [
            'members'  => $filtered,
            'q'        => $q,
            'filter'   => $filter,
            'branch_id'=> self::BRANCH_ID,
            'branch_name'=> self::BRANCH_NAME,
            'theme'    => self::THEME,
        ];

        $this->call->view('staff_victoria/members', $data);
    }

    public function storeMember()
    {
        $this->call->model('Member');

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $_SESSION['error'] = 'Please enter a name.';
            header('Location: /staff-vic/members');
            exit;
        }

        $payload = $this->_member_payload();
        $payload['branch_id'] = self::BRANCH_ID;
        $this->Member->insert($payload);
        header('Location: /staff-vic/members');
        exit;
    }

    public function editMember($id)
    {
        $this->call->model('Member');
        $member = $this->Member->_find((int) $id);

        if (!$member || (int) ($member['branch_id'] ?? 0) !== self::BRANCH_ID) {
            $_SESSION['error'] = 'Member not found or not accessible.';
            header('Location: /staff-vic/members');
            exit;
        }

        $this->call->view('staff_victoria/edit_member', ['member' => $member, 'branch_id' => self::BRANCH_ID, 'branch_name' => self::BRANCH_NAME, 'theme' => self::THEME]);
    }

    public function updateMember($id)
    {
        $this->call->model('Member');
        $member = $this->Member->_find((int) $id);

        if (!$member || (int) ($member['branch_id'] ?? 0) !== self::BRANCH_ID) {
            $_SESSION['error'] = 'Member not found or not accessible.';
            header('Location: /staff-vic/members');
            exit;
        }

        $payload = $this->_member_payload();
        $payload['branch_id'] = self::BRANCH_ID;

        if ($payload['name'] === '') {
            $_SESSION['error'] = 'Please enter a name.';
            header('Location: /staff-vic/members');
            exit;
        }

        $this->Member->update((int) $id, $payload);
        $_SESSION['success'] = 'Member updated successfully.';
        header('Location: /staff-vic/members');
        exit;
    }

    // =========================================================================
    // EXPENSES
    // =========================================================================
    public function expenses()
    {
        $this->call->model('Expense');

        $expenses = $this->_filter_branch($this->Expense->_all());
        $total = array_reduce($expenses, fn($c, $e) => $c + (float) $e['price'], 0);

        $data = [
            'expenses' => array_reverse($expenses),
            'total'    => $total,
            'branch_id'=> self::BRANCH_ID,
            'branch_name'=> self::BRANCH_NAME,
            'theme'    => self::THEME,
        ];

        $this->call->view('staff_victoria/expenses', $data);
    }

    public function storeExpense()
    {
        $this->call->model('Expense');

        $name = trim($_POST['name'] ?? '');
        $price= (float) ($_POST['price'] ?? 0);
        $date = $_POST['date'] ?? '';

        if ($name === '' || $price <= 0) {
            $_SESSION['error'] = 'Please complete name and price.';
            header('Location: /staff-vic/expenses');
            exit;
        }

        $this->Expense->insert([
            'name'  => $name,
            'price' => $price,
            'date'  => $date ?: null,
            'branch_id' => self::BRANCH_ID,
        ]);

        header('Location: /staff-vic/expenses');
        exit;
    }

    public function editExpense($id)
    {
        $this->call->model('Expense');
        $expense = $this->Expense->_find((int) $id);

        if (!$expense || (int) ($expense['branch_id'] ?? 0) !== self::BRANCH_ID) {
            $_SESSION['error'] = 'Expense not found or not accessible.';
            header('Location: /staff-vic/expenses');
            exit;
        }

        $this->call->view('staff_victoria/edit_expense', ['expense' => $expense, 'branch_id' => self::BRANCH_ID, 'branch_name' => self::BRANCH_NAME, 'theme' => self::THEME]);
    }

    public function updateExpense($id)
    {
        $this->call->model('Expense');
        $expense = $this->Expense->_find((int) $id);

        if (!$expense || (int) ($expense['branch_id'] ?? 0) !== self::BRANCH_ID) {
            $_SESSION['error'] = 'Expense not found or not accessible.';
            header('Location: /staff-vic/expenses');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $price= (float) ($_POST['price'] ?? 0);
        $date = $_POST['date'] ?? '';

        if ($name === '' || $price <= 0) {
            $_SESSION['error'] = 'Please complete name and price.';
            header('Location: /staff-vic/expenses');
            exit;
        }

        $this->Expense->update((int) $id, [
            'name'  => $name,
            'price' => $price,
            'date'  => $date ?: null,
            'branch_id' => self::BRANCH_ID,
        ]);

        $_SESSION['success'] = 'Expense updated successfully.';
        header('Location: /staff-vic/expenses');
        exit;
    }
}
