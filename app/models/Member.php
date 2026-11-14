<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Member extends Model {

    protected $table          = 'members';
    protected $primary_key    = 'id';
    protected $fillable       = [
        'name', 'contact', 'address', 'birthday', 'age',
        'gender', 'membership_type', 'start_date', 'end_date',
        'customer_type', 'yearly_subscription', 'yearly_start_date', 'yearly_end_date',
        'branch_id',
    ];
    protected $timestamps     = TRUE;
}
