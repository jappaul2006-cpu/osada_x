<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Admin Controller - full access (mirrors prototype Admin pages).
 * Routes: /admin, /admin/members, /admin/daily-logs, /admin/expenses + CRUD actions
 */
class Admin extends MY_Controller {

    public function before_action()
    {
        parent::before_action();
        $this->_require_role('admin');
    }

    private function _require_role($role)
    {
        if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== $role) {
            $_SESSION['error'] = 'Please log in as ' . $role . ' to access this page.';
            header('Location: /login');
            exit;
        }
    }

    /**
     * Alamin ang branch_id na dapat gamitin sa tab filtering.
     * Kung walang napili, default sa unang branch (Calapan).
     */
    private function _resolve_branch(array $branches, $requested): int
    {
        $requested = (int) $requested;

        if ($requested > 0) {
            foreach ($branches as $b) {
                if ((int) ($b['id'] ?? 0) === $requested) {
                    return $requested;
                }
            }
        }

        // Default: unang branch sa listahan
        return (int) ($branches[0]['id'] ?? 0);
    }

    // =========================================================================
    // DASHBOARD
    // =========================================================================
    public function dashboard()
    {
        $this->call->model('Member');
        $this->call->model('DailyLog');
        $this->call->model('Expense');

        $members = $this->Member->_all();
        $logs    = $this->_compute_log_totals($this->DailyLog->_all());
        $expenses= $this->Expense->_all();

        // Member counts (2weeks members are counted as weekly)
        $weekly_count  = count(array_filter($members, fn($m) => in_array($m['membership_type'] ?? '', ['weekly', '2weeks'], true)));
        $monthly_count = count(array_filter($members, fn($m) => ($m['membership_type'] ?? '') === 'monthly'));
        $daily_count   = count(array_filter($members, fn($m) => ($m['membership_type'] ?? '') === 'daily'));

        // Revenue = aktwal na cash na nalog (TOTAL AMOUNT per log), naka-grupo ayon sa membership type.
        // Hindi na ginagamit ang estimate na count × fixed price.
        $revenueFor = function (array $types) use ($logs) {
            return array_reduce(
                array_filter($logs, fn($l) => in_array($l['membership_type'] ?? '', $types, true)),
                fn($c, $l) => $c + (float) ($l['total_amount'] ?? 0),
                0
            );
        };
        $revenue_daily   = $revenueFor(['daily']);
        $revenue_weekly  = $revenueFor(['weekly', '2weeks']);
        $revenue_monthly = $revenueFor(['monthly']);
        $revenue_total   = $revenue_daily + $revenue_weekly + $revenue_monthly;

        $exp_total = array_reduce($expenses, fn($c, $e) => $c + (float) $e['price'], 0);
        $net = $revenue_total - $exp_total;

        // Recent 8 members & logs (newest first)
        $recent_members = array_slice(array_reverse($members), 0, 8);
        $recent_logs    = array_slice(array_reverse($logs), 0, 8);

        $data = [
            'members'       => $members,
            'logs'          => $logs,
            'expenses'      => $expenses,
            'revenue'       => ['weekly' => $revenue_weekly, 'monthly' => $revenue_monthly, 'daily' => $revenue_daily, 'total' => $revenue_total],
            'exp_total'     => $exp_total,
            'net'           => $net,
            'counts'        => ['daily' => $daily_count, 'weekly' => $weekly_count, 'monthly' => $monthly_count],
            'total_members' => count($members),
            'recent_members'=> $recent_members,
            'recent_logs'   => $recent_logs,
        ];

        $this->call->view('admin/dashboard', $data);
    }

    // =========================================================================
    // MEMBERS (add + delete, NO edit)
    // =========================================================================
    public function members()
    {
        $this->call->model('Member');
        $this->call->model('Branch');

        $q      = trim($_GET['q'] ?? '');
        $filter = $_GET['filter'] ?? 'all';

        $allMembers = $this->Member->_all();
        $branches   = $this->Branch->_all();

        // Napiling branch tab (default: unang branch)
        $branch = $this->_resolve_branch($branches, $_GET['branch'] ?? 0);

        // Map ng branch_id => location (para sa badge sa table)
        $branchNames = [];
        foreach ($branches as $b) {
            $branchNames[(int) ($b['id'] ?? 0)] = $b['location'] ?? '';
        }

        // bilang ng members kada branch (para sa badge sa tab)
        $branchCounts = [];
        foreach ($branches as $b) {
            $bid = (int) ($b['id'] ?? 0);
            $branchCounts[$bid] = count(array_filter(
                $allMembers,
                fn($m) => (int) ($m['branch_id'] ?? 0) === $bid
            ));
        }

        // Filter ayon sa napiling branch tab
        $members = $allMembers;
        if ($branch > 0) {
            $members = array_values(array_filter(
                $members,
                fn($m) => (int) ($m['branch_id'] ?? 0) === $branch
            ));
        }

        // Search + filter (mirrors prototype)
        $filtered = array_values(array_filter($members, function ($m) use ($q, $filter) {
            $okQ = stripos(($m['name'] ?? '') . ' ' . ($m['contact'] ?? '') . ' ' . ($m['address'] ?? ''), $q) !== false;
            $okF = ($filter === 'all') || ($m['membership_type'] === $filter);
            return $okQ && $okF;
        }));

        $data = [
            'members'      => $filtered,
            'branches'     => $branches,
            'branchCounts' => $branchCounts,
            'branchNames'  => $branchNames,
            'branch'       => $branch,
            'activeBranch' => ($branchNames[$branch] ?? ''),
            'q'            => $q,
            'filter'       => $filter,
        ];

        $this->call->view('admin/members', $data);
    }

    public function storeMember()
    {
        $this->call->model('Member');
        $this->call->model('Branch');

        $payload = $this->_member_payload();
        $branchId = (int) ($_POST['branch_id'] ?? 0);

        if ($payload['name'] === '') {
            $_SESSION['error'] = 'Please enter a name.';
            header('Location: /admin/members');
            exit;
        }

        // Siguraduhing valid ang branch
        if (!$this->_branch_exists($branchId)) {
            $_SESSION['error'] = 'Please select a valid branch location.';
            header('Location: /admin/members');
            exit;
        }

        $payload['branch_id'] = $branchId;

        $this->Member->insert($payload);

        $_SESSION['success'] = 'Member added successfully.';
        header('Location: /admin/members?branch=' . $branchId);
        exit;
    }

    public function editMember($id)
    {
        $this->call->model('Member');
        $this->call->model('Branch');
        $member = $this->Member->_find((int) $id);

        if (!$member) {
            $_SESSION['error'] = 'Member not found.';
            header('Location: /admin/members');
            exit;
        }

        $data = [
            'member'   => $member,
            'branches' => $this->Branch->_all(),
        ];

        $this->call->view('admin/edit_member', $data);
    }

    public function updateMember($id)
    {
        $this->call->model('Member');
        $this->call->model('Branch');

        $payload = $this->_member_payload();
        $branchId = (int) ($_POST['branch_id'] ?? 0);

        if ($payload['name'] === '') {
            $_SESSION['error'] = 'Please enter a name.';
            header('Location: /admin/members');
            exit;
        }

        // Siguraduhing valid ang branch
        if (!$this->_branch_exists($branchId)) {
            $_SESSION['error'] = 'Please select a valid branch location.';
            header('Location: /admin/members');
            exit;
        }

        $payload['branch_id'] = $branchId;

        $this->Member->update((int) $id, $payload);

        $_SESSION['success'] = 'Member updated successfully.';
        header('Location: /admin/members?branch=' . $branchId);
        exit;
    }

    public function deleteMember($id)
    {
        $this->call->model('Member');
        $this->Member->delete((int) $id);
        $_SESSION['success'] = 'Member deleted successfully.';
        header('Location: /admin/members');
        exit;
    }

    // =========================================================================
    // DAILY LOGS (read-only list, add happens from staff but admin views all)
    // =========================================================================
    public function dailyLogs()
    {
        $this->call->model('DailyLog');
        $this->call->model('Branch');

        $q      = trim($_GET['q'] ?? '');
        $filter = $_GET['filter'] ?? 'all';

        $allLogs  = $this->_compute_log_totals($this->DailyLog->_all());
        $branches = $this->Branch->_all();

        // Napiling branch tab (default: unang branch)
        $branch = $this->_resolve_branch($branches, $_GET['branch'] ?? 0);

        // Map ng branch_id => location (para sa badge sa tab)
        $branchNames = [];
        foreach ($branches as $b) {
            $branchNames[(int) ($b['id'] ?? 0)] = $b['location'] ?? '';
        }

        // bilang ng logs kada branch (para sa badge sa tab)
        $branchCounts = [];
        foreach ($branches as $b) {
            $bid = (int) ($b['id'] ?? 0);
            $branchCounts[$bid] = count(array_filter(
                $allLogs,
                fn($l) => (int) ($l['branch_id'] ?? 0) === $bid
            ));
        }

        // Filter ayon sa napiling branch tab
        $logs = $allLogs;
        if ($branch > 0) {
            $logs = array_values(array_filter(
                $logs,
                fn($l) => (int) ($l['branch_id'] ?? 0) === $branch
            ));
        }

        $filtered = array_values(array_filter(array_reverse($logs), function ($l) use ($q, $filter) {
            $okQ = stripos($l['name'] ?? '', $q) !== false;
            $okF = ($filter === 'all') || (($l['membership_type'] ?? '') === $filter);
            return $okQ && $okF;
        }));

        // Total income = kabuuan ng TOTAL AMOUNT ng lahat ng filtered logs (kita kada log ng customer)
        $total_income = array_reduce(
            $filtered,
            fn($c, $l) => $c + (float) ($l['total_amount'] ?? 0),
            0
        );

        $data = [
            'logs'         => $filtered,
            'branches'     => $branches,
            'branchCounts' => $branchCounts,
            'branch'       => $branch,
            'activeBranch' => ($branchNames[$branch] ?? ''),
            'q'            => $q,
            'filter'       => $filter,
            'total_income' => $total_income,
            'count'        => count($filtered),
        ];

        $this->call->view('admin/daily-logs', $data);
    }

    // =========================================================================
    // EXPENSES (add + edit + delete)
    // =========================================================================
    public function expenses()
    {
        $this->call->model('Expense');
        $this->call->model('Branch');

        $allExpenses = $this->Expense->_all();
        $branches    = $this->Branch->_all();

        // Napiling branch tab (default: unang branch)
        $branch = $this->_resolve_branch($branches, $_GET['branch'] ?? 0);

        // Map ng branch_id => location (para sa badge sa table)
        $branchNames = [];
        foreach ($branches as $b) {
            $branchNames[(int) ($b['id'] ?? 0)] = $b['location'] ?? '';
        }

        // bilang ng expenses kada branch (para sa badge sa tab)
        $branchCounts = [];
        foreach ($branches as $b) {
            $bid = (int) ($b['id'] ?? 0);
            $branchCounts[$bid] = count(array_filter(
                $allExpenses,
                fn($e) => (int) ($e['branch_id'] ?? 0) === $bid
            ));
        }

        // Filter ayon sa napiling branch tab
        $expenses = $allExpenses;
        if ($branch > 0) {
            $expenses = array_values(array_filter(
                $expenses,
                fn($e) => (int) ($e['branch_id'] ?? 0) === $branch
            ));
        }

        $total = array_reduce($expenses, fn($c, $e) => $c + (float) $e['price'], 0);

        $data = [
            'expenses'     => array_reverse($expenses),
            'branches'     => $branches,
            'branchCounts' => $branchCounts,
            'branchNames'  => $branchNames,
            'branch'       => $branch,
            'activeBranch' => ($branchNames[$branch] ?? ''),
            'total'        => $total,
        ];

        $this->call->view('admin/expenses', $data);
    }

    public function storeExpense()
    {
        $this->call->model('Expense');
        $this->call->model('Branch');

        $name = trim($_POST['name'] ?? '');
        $price= (float) ($_POST['price'] ?? 0);
        $date = $_POST['date'] ?? '';
        $branchId = (int) ($_POST['branch_id'] ?? 0);

        if ($name === '' || $price <= 0) {
            $_SESSION['error'] = 'Please complete name and price.';
            header('Location: /admin/expenses');
            exit;
        }

        // Siguraduhing valid ang branch
        if (!$this->_branch_exists($branchId)) {
            $_SESSION['error'] = 'Please select a valid branch location.';
            header('Location: /admin/expenses');
            exit;
        }

        $this->Expense->insert([
            'name'  => $name,
            'price' => $price,
            'date'  => $date ?: null,
            'branch_id' => $branchId,
        ]);

        $_SESSION['success'] = 'Expense added successfully.';
        header('Location: /admin/expenses?branch=' . $branchId);
        exit;
    }

    public function deleteExpense($id)
    {
        $this->call->model('Expense');
        $this->Expense->delete((int) $id);
        $_SESSION['success'] = 'Expense deleted successfully.';
        header('Location: /admin/expenses');
        exit;
    }

    public function editExpense($id)
    {
        $this->call->model('Expense');
        $this->call->model('Branch');
        $expense = $this->Expense->_find((int) $id);

        if (!$expense) {
            $_SESSION['error'] = 'Expense not found.';
            header('Location: /admin/expenses');
            exit;
        }

        $data = [
            'expense'  => $expense,
            'branches' => $this->Branch->_all(),
        ];

        $this->call->view('admin/edit_expense', $data);
    }

    public function updateExpense($id)
    {
        $this->call->model('Expense');
        $this->call->model('Branch');

        $name = trim($_POST['name'] ?? '');
        $price= (float) ($_POST['price'] ?? 0);
        $date = $_POST['date'] ?? '';
        $branchId = (int) ($_POST['branch_id'] ?? 0);

        if ($name === '' || $price <= 0) {
            $_SESSION['error'] = 'Please complete name and price.';
            header('Location: /admin/expenses');
            exit;
        }

        // Siguraduhing valid ang branch
        if (!$this->_branch_exists($branchId)) {
            $_SESSION['error'] = 'Please select a valid branch location.';
            header('Location: /admin/expenses');
            exit;
        }

        $this->Expense->update((int) $id, [
            'name'  => $name,
            'price' => $price,
            'date'  => $date ?: null,
            'branch_id' => $branchId,
        ]);

        $_SESSION['success'] = 'Expense updated successfully.';
        header('Location: /admin/expenses?branch=' . $branchId);
        exit;
    }

    // =========================================================================
    // USER MANAGEMENT (add, edit, view, delete staff users)
    // =========================================================================
    public function users()
    {
        $this->call->model('User');
        $this->call->model('Branch');

        $q      = trim($_GET['q'] ?? '');
        $filter = $_GET['filter'] ?? 'all';

        $users    = $this->User->_all();
        $branches = $this->Branch->_all();

        // Map ng branch_id => branch location para madaling i-display
        $branchMap = [];
        foreach ($branches as $b) {
            $branchMap[(int) ($b['id'] ?? 0)] = $b['location'] ?? '';
        }

        $filtered = array_values(array_filter($users, function ($u) use ($q, $filter) {
            $okQ = stripos(($u['username'] ?? '') . ' ' . ($u['role'] ?? ''), $q) !== false;
            $okF = ($filter === 'all') || ($u['role'] === $filter);
            return $okQ && $okF;
        }));

        $data = [
            'users'      => $filtered,
            'branches'   => $branches,
            'branchMap'  => $branchMap,
            'q'          => $q,
            'filter'     => $filter,
        ];

        $this->call->view('admin/users', $data);
    }

    public function storeUser()
    {
        $this->call->model('User');
        $this->call->model('Branch');

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = 'staff'; // Only staff can be created; admin role is locked
        $branchId = (int) ($_POST['branch_id'] ?? 0);

        if ($username === '' || $password === '') {
            $_SESSION['error'] = 'Please complete username and password.';
            header('Location: /admin/users');
            exit;
        }

        // Siguraduhing valid ang branch_id (dapat umiiral sa branches table)
        if (!$this->_branch_exists($branchId)) {
            $_SESSION['error'] = 'Please select a valid branch.';
            header('Location: /admin/users');
            exit;
        }

        $this->User->insert([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role'     => $role,
            'is_active'=> 1,
            'branch_id'=> $branchId,
        ]);

        $_SESSION['success'] = 'User added successfully.';
        header('Location: /admin/users');
        exit;
    }

    public function editUser($id)
    {
        $this->call->model('User');
        $this->call->model('Branch');
        $user = $this->User->_find((int) $id);

        if (!$user) {
            $_SESSION['error'] = 'User not found.';
            header('Location: /admin/users');
            exit;
        }

        $data = [
            'user'     => $user,
            'branches' => $this->Branch->_all(),
        ];

        $this->call->view('admin/edit_user', $data);
    }

    public function updateUser($id)
    {
        $this->call->model('User');
        $this->call->model('Branch');

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = 'staff'; // Only staff role is allowed; admin role is locked
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $branchId = (int) ($_POST['branch_id'] ?? 0);

        if ($username === '') {
            $_SESSION['error'] = 'Please complete username.';
            header('Location: /admin/users');
            exit;
        }

        // Siguraduhing valid ang branch_id (dapat umiiral sa branches table)
        if (!$this->_branch_exists($branchId)) {
            $_SESSION['error'] = 'Please select a valid branch.';
            header('Location: /admin/users');
            exit;
        }

        $data = [
            'username' => $username,
            'role'     => $role,
            'is_active'=> $isActive,
            'branch_id'=> $branchId,
        ];

        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->User->update((int) $id, $data);

        $_SESSION['success'] = 'User updated successfully.';
        header('Location: /admin/users');
        exit;
    }

    /**
     * Check kung umiiral ang branch_id sa branches table.
     */
    private function _branch_exists($branchId): bool
    {
        if ((int) $branchId <= 0) {
            return false;
        }
        foreach ($this->Branch->_all() as $b) {
            if ((int) ($b['id'] ?? 0) === (int) $branchId) {
                return true;
            }
        }
        return false;
    }

    public function deleteUser($id)
    {
        $this->call->model('User');
        $this->User->delete((int) $id);
        $_SESSION['success'] = 'User deleted successfully.';
        header('Location: /admin/users');
        exit;
    }

    // =========================================================================
    // BRANCH MANAGEMENT (admin only) - view, add, edit, delete branches
    // =========================================================================
    public function branches()
    {
        $this->call->model('Branch');

        $q = trim($_GET['q'] ?? '');
        $branches = $this->Branch->_all();

        $filtered = array_values(array_filter($branches, function ($b) use ($q) {
            return $q === '' || stripos(($b['location'] ?? '') . ' ' . ($b['id'] ?? ''), $q) !== false;
        }));

        $data = [
            'branches' => $filtered,
            'q' => $q,
        ];

        $this->call->view('admin/branches', $data);
    }

    public function storeBranch()
    {
        $this->call->model('Branch');

        $location = trim($_POST['location'] ?? '');

        if ($location === '') {
            $_SESSION['error'] = 'Please enter a branch location/name.';
            header('Location: /admin/branches');
            exit;
        }

        $this->Branch->insert([
            'location' => $location,
        ]);

        $_SESSION['success'] = 'Branch added successfully.';
        header('Location: /admin/branches');
        exit;
    }

    public function editBranch($id)
    {
        $this->call->model('Branch');
        $branch = $this->Branch->_find((int) $id);

        if (!$branch) {
            $_SESSION['error'] = 'Branch not found.';
            header('Location: /admin/branches');
            exit;
        }

        $this->call->view('admin/edit_branch', ['branch' => $branch]);
    }

    public function updateBranch($id)
    {
        $this->call->model('Branch');
        $branch = $this->Branch->_find((int) $id);

        if (!$branch) {
            $_SESSION['error'] = 'Branch not found.';
            header('Location: /admin/branches');
            exit;
        }

        $location = trim($_POST['location'] ?? '');

        if ($location === '') {
            $_SESSION['error'] = 'Please enter a branch location/name.';
            header('Location: /admin/branches');
            exit;
        }

        $this->Branch->update((int) $id, [
            'location' => $location,
        ]);

        $_SESSION['success'] = 'Branch updated successfully.';
        header('Location: /admin/branches');
        exit;
    }

    public function deleteBranch($id)
    {
        $this->call->model('Branch');
        $this->Branch->delete((int) $id);
        $_SESSION['success'] = 'Branch deleted successfully.';
        header('Location: /admin/branches');
        exit;
    }
}
