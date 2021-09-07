<?php
class MY_Model extends CI_Model {

    protected $database = 'db_user';

    protected $table_name;
    
    protected $primary_key = 'id';

    protected $fields = [];
 
    public $related;

    public $hasMany = [];

    public $belongsTo = [];

    public $adminModels = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function setFields($row_array) {
        // $this->fields = $row_array;
        foreach ($row_array as $key => $value) {
            $this->$key = $value;
        }
    }

    // find one by primary key
    public function find($id) {

        return $this->findOneBy([$this->primary_key => $id]);
    }
 
    // find one by field => value
    public function findOneBy($arr) {
        
        $database = $this->database;

        $this->$database->select('*')
        ->from($this->table_name);
        
        foreach ($arr as $key => $value) {
            $this->$database->where($key, $value);
        }
        
        $this->$database->limit(1);

        $rows = $this->$database->get()->result_array();

        if (empty($rows)) {
            return null;
        }

        foreach ($rows as $row) {
            $this->setFields($row);
        }

        return $this;
    }

    //  find many by field => value
    public function findBy($arr, $order=null, $serialize=true) {

        $database = $this->database;

        $this->$database->select('*')
        ->from($this->table_name);
        
        foreach ($arr as $key => $value) {
  
            if (!is_array($value)) {
                $this->$database->where($key, $value);
            } else {
                foreach ($value as $v) {
                    $this->$database->where($key, $v);
                }
            }
        }

        if (!is_null($order)) {
            foreach ($order as $key => $value) {
                $arr2[] = $key . ' ' . $value;
            }
            $values = implode(', ', $arr2);
            $this->$database->order_by($values);
        }

        $rows = $this->$database->get()->result_array();

        if ($rows == null) {
            return null;
        }

        if ($serialize == false) {

            foreach ($rows as $row) {
                $temp = clone $this;
                $temp->setFields($row);
                $this_array[] = $temp;
            }

            return $this_array;
        }

        return $rows;
    }
    
    // find all
    public function findAll($order=null, $limit=null, $serialize=true) {

        $database = $this->database;

        $this->$database->select('*')
        ->from($this->table_name);

        if (!is_null($order)) {
            foreach ($order as $key => $value) {
                $this->$database->order_by($key, $value);
            }
        }

        if ((!is_null($limit)) && (is_numeric($limit))) {
            $this->$database->limit($limit);
        }

        $rows = $this->$database->get()->result_array();

        if ($rows == null) {
            return null;
        }

        if ($serialize == false) {

            foreach ($rows as $row) {
                $temp = clone $this;
                $temp->setFields($row);
                $this_array[] = $temp;
            }

            return $this_array;
        }

        return $rows;


    }

    public function save() {

        $database = $this->database;

        foreach ($this->fields as $field) {
            $data[$field] = $this->$field;
        }


        $id = $this->primary_key;
        if (isset($this->$id)) {

            $this->$database->where($this->primary_key, $this->$id);
            $this->$database->update($this->table_name, $data);

            $result = ($this->$database->affected_rows() > 0) ? true : false;
            
            return $result;
        }

        $this->$database->insert($this->table_name, $data);
        $insert_id = $this->$database->insert_id();

        return $insert_id;
    }

    public function delete($id=null) {

        $database = $this->database;

        $this->$database->where($this->primary_key, $id);
        $this->$database->delete($this->table_name);

        $result = ($this->$database->affected_rows() > 0) ? true : false;

        return $result;
    }

    // one to many
    public function gethasMany($relatedModel, $filterBy = null) {

        $this->load->model($relatedModel.'_model', $relatedModel);
        $id = $this->primary_key;
        $filter = [$this->primary_key => $this->$id];

        if ($filterBy != null) {
            $filter = array_merge($filter, $filterBy);
        }

        $result = $this->$relatedModel->findBy($filter, null, false);

        return $result;
    }

    // one to one
    // many to one
    public function getbelongsTo($relatedModel) {

        $this->load->model($relatedModel.'_model', $relatedModel);
        $relatedModelField = $relatedModel.'_id';
        $result = $this->$relatedModel->findOneBy([$relatedModelField  => $this->$relatedModelField]);

        return $result;
    }

    private function startsWith($string, $prefix){
        return strpos($string, $prefix) === 0;
    }

    private function funcToModel($string) {
        return lcfirst(substr($string, 3));
    }

    //dynamic functions (currently for get only)
    public function __call($func, $params) {

        $options = [];
        //read params (convert from array to key => values)
        if (isset($params)) {
            foreach ($params as $param) {
                foreach ($param as $key => $value) {
                    $options[$key] = $value;
                }
            }
        }

        if ($this->startsWith($func, 'get')) {
            
            if (isset($options['modelname'])) {
                $relatedModel = $options['modelname'];
            } 
            else {
                $relatedModel = $this->funcToModel($func);
            }

            $filterBy = null;
            if (isset($options['filter'])) {
                $filterBy = $options['filter'];
            }

        }
        
        // substr to remove plural
        if (in_array(substr($relatedModel, 0, -1), $this->hasMany)) {
            
            $relatedModel = substr($relatedModel, 0, -1);
            return $this->gethasMany($relatedModel, $filterBy);
        }

        if (in_array($relatedModel, $this->belongsTo)) {

            if(in_array($relatedModel, $this->adminModels)) {
                $this->database = 'db_admin';
            }

            return $this->getbelongsTo($relatedModel);
        }

        throw new Error('Call to undefined method '.$func.'()');
    }

    //call serialize to get all the data into an array
    public function serialize($level = 'default') {

        $serialized = [];
        foreach ($this->fields as $key => $value) {
            $serialized = array_merge($serialized, [$value => $this->$value]);
        }
        return $serialized;
    }

    public function filter($arr) {

        foreach ($arr as $key => $value) {
            if ($this->$key == $value) {
                echo $value;
            }
        }

    }

}