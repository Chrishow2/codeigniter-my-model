<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Avenger_model extends MY_Model
{
    protected $table_name = 'avengers';

    protected $primary_key = 'avenger_id';

    protected $fields = [
        'name',
        'age',
        'flies'
    ]; 

    // array of models
    public $hasMany = ['power']; 

    // array of models
    public $belongsTo = ['organization']; 

    public function __construct()
    {
        parent::__construct();
    }
}