<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Power_model extends MY_Model
{
    protected $table_name = 'powers';

    protected $primary_key = 'power_id';

    protected $fields = [
        'name'
    ]; 

    public $belongsTo = ['avenger']; 

    public function __construct()
    {
        parent::__construct();
    }
}