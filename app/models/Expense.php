<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Expense extends Model {

    protected $table          = 'expenses';
    protected $primary_key    = 'id';
    protected $fillable       = ['name', 'price', 'date', 'branch_id'];
    protected $timestamps     = TRUE;
}
