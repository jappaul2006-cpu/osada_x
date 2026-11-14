<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class DailyLog extends Model {

    protected $table          = 'daily_logs';
    protected $primary_key    = 'id';
    protected $fillable       = ['member_id', 'name', 'type', 'membership_type', 'membership_amount', 'weekly_renewal', 'monthly_renewal', 'membership_renewal', 'total_amount', 'date', 'time', 'branch_id'];
    protected $timestamps     = FALSE;
}
