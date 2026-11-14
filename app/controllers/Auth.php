<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Auth Controller - Login / Logout (mirrors prototype Login page).
 * Admin: username=admin / password=admin123
 * Staff:  username=staff / password=staff123
 */
class Auth extends MY_Controller {

    public function login()
    {
        // Kung may user na sa session, redirect sa wastong dashboard
        if (!empty($_SESSION['user'])) {
            $role = $_SESSION['user']['role'];
            header('Location: ' . ($role === 'admin' ? '/admin' : '/staff'));
            exit;
        }

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $this->call->view('auth/login', ['error' => $error]);
    }

    public function doLogin()
    {
        $this->call->database();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Check if user exists at all (regardless of active status)
        $user = $this->db
            ->table('users')
            ->where('username', $username)
            ->row_array();

        if (!$user) {
            // No existing account with that username
            $_SESSION['error'] = 'No existing account found with that username.';
            header('Location: /login');
            exit;
        }

        if (empty($user['is_active']) || $user['is_active'] !== 1) {
            // Account exists but is inactive/disabled
            $_SESSION['error'] = 'Your account is inactive. Please contact an administrator.';
            header('Location: /login');
            exit;
        }

        // Account exists and is active, verify password
        if (!password_verify($password, $user['password'])) {
            $_SESSION['error'] = 'Invalid password.';
            header('Location: /login');
            exit;
        }

        $_SESSION['user'] = [
            'id'       => (int) $user['id'],
            'username' => $user['username'],
            'role'     => $user['role'],
            'branch_id'=> (int) ($user['branch_id'] ?? 0),
        ];

        // Redirect to correct dashboard based on role and branch
        if ($user['role'] === 'admin') {
            header('Location: /admin');
            exit;
        }

        // Staff: route to branch-specific portal
        $branchId = (int) ($user['branch_id'] ?? 0);
        switch ($branchId) {
            case 1: $staffRoute = '/staff'; break;      // Calapan (green + black)
            case 2: $staffRoute = '/staff-pina'; break; // Pinamalayan (yellow + black)
            case 3: $staffRoute = '/staff-vic'; break;  // Victoria (red + black)
            case 4: $staffRoute = '/staff-barc'; break; // Barcenaga (white + black)
            default: $staffRoute = '/staff';
        }
        header('Location: ' . $staffRoute);
        exit;
    }

    public function logout()
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: /login');
        exit;
    }
}
