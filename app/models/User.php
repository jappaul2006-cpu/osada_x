<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class User extends Model {

    protected $table          = 'users';
    protected $primary_key    = 'id';
    protected $fillable       = ['username', 'email', 'password', 'role', 'is_active', 'branch_id'];
    protected $timestamps     = TRUE;
}
