<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * MY_Controller - Base controller para sa Lahat ng LavaLust controllers ng gym system.
 *
 * Nag-ensure lamang ng database connection. Hindi nag-a-create ng table
 * dahil mayroon na kayong existing tables sa Aiven.
 */
class MY_Controller extends Controller {

    public function before_action()
    {
        $this->_ensure_database();
    }

    /**
     * Siguraduhing nakaload ang database library para ma-access ang $this->db.
     */
    protected function _ensure_database()
    {
        $this->call->database();
    }

    /**
     * I-set ang total_amount ng bawat daily log =
     * AMOUNT (membership_amount) + WEEKLY + MONTHLY + MEMBERSHIP RENEWAL.
     * Hindi aasa sa naka-store na total_amount para laging tama ang display at revenue.
     */
    protected function _compute_log_totals(array $logs): array
    {
        return array_map(function ($l) {
            $l['total_amount'] = (float) ($l['membership_amount'] ?? 0)
                + (float) ($l['weekly_renewal'] ?? 0)
                + (float) ($l['monthly_renewal'] ?? 0)
                + (float) ($l['membership_renewal'] ?? 0);
            return $l;
        }, $logs);
    }

    /**
     * Build the member data array from $_POST (shared by Admin & Staff Member Management).
     * Handles yearly subscription (member only), auto end_date (2weeks +14d / monthly +30d),
     * and auto age from birthday.
     */
    protected function _member_payload(): array
    {
        $name            = trim($_POST['name'] ?? '');
        $contact         = trim($_POST['contact'] ?? '');
        $address         = trim($_POST['address'] ?? '');
        $birthday        = $_POST['birthday'] ?? '';
        $age             = $_POST['age'] ?? '';
        $gender          = $_POST['gender'] ?? 'Male';

        // Customer Type: student, member, non-member
        $customerType    = $_POST['customer_type'] ?? 'member';

        // Yearly Subscription (only for member / non-member, hindi para sa student)
        $yearlyStartDate = $_POST['yearly_start_date'] ?? '';
        $yearlyEndDate   = '';
        $yearlySub       = 0;

        if ($customerType !== 'student' && $yearlyStartDate !== '') {
            $yearlySub    = 1;
            $yearlyEndDate= date('Y-m-d', strtotime($yearlyStartDate . ' + 1 year'));
        }

        // Membership Type: daily, 2weeks, monthly
        $mtype           = $_POST['membership_type'] ?? 'daily';

        // Start / End date for 2weeks or monthly membership
        $startDate       = $_POST['start_date'] ?? '';
        $endDate         = '';

        if (in_array($mtype, ['2weeks', 'monthly'], true) && $startDate !== '') {
            $endDate = date('Y-m-d', strtotime($startDate . ($mtype === '2weeks' ? ' + 14 days' : ' + 30 days')));
        }

        // Auto age from birthday
        if ($birthday !== '' && $age === '') {
            $b = new DateTime($birthday);
            $now = new DateTime();
            $age = $now->diff($b)->y;
        }

        return [
            'name'              => $name,
            'contact'           => $contact,
            'address'           => $address,
            'birthday'          => $birthday ?: null,
            'age'               => $age ?: null,
            'gender'            => $gender,
            'customer_type'     => $customerType,
            'membership_type'   => $mtype,
            'start_date'        => $startDate ?: null,
            'end_date'          => $endDate ?: null,
            'yearly_start_date' => $yearlyStartDate ?: null,
            'yearly_end_date'   => $yearlyEndDate ?: null,
            'yearly_subscription' => $yearlySub,
        ];
    }
}
