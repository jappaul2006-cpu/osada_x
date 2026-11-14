<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Branch extends Model {

    protected $table          = 'branches';
    protected $primary_key    = 'id';
    protected $fillable       = ['location'];
    protected $timestamps     = TRUE;
}
