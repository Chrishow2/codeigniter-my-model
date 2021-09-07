# CodeIgniter-MY_Model

This **CodeIgniter MY_Model** is a very basic yet helpful ORM to help you deal with basic operations in codeigniter

## Installation

Download and drag the **MY_Model.php** file into your **application/core** directory. CodeIgniter will load and initialise this class automatically. Then extend your model classes from MY_Model and you're good to go!
```php
class Avenger_model extends MY_Model
{
    protected $table_name = 'avengers';
    
    protected $primary_key = 'avenger_id';

    // array of fields available for modification
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
```

## Usage
```php
$this->load->model('avenger_model');

// find one avenger by id
$avenger = $this->avenger_model->find(1);

// find list of avengers that can fly sorted by age
$avengers = $this->avenger_model->findBy(['flies' => true], ['age' => 'asc']);

// find all avengers
$avengers = $this->avenger_model->findAll();

// get all the powers of the current avenger
$organizations = $avenger->getPowers();

// change the name of one avenger
$avenger->name('Ironguy');
$avenger->save();

// delete an avenger both will work
$avenger->delete();
$this->avenger_model->delete(1);
```
## Notes

Most functions return objects (so you can edit its properties or call their functions directly) however functions that return a list such as **findBy** and **findAll** that return arrays will convert each object to array by default, should yo need objects you can change its third parameter to false:
```php
$avengers = $this->avenger_model->findBy(['flies' => true], ['age' => 'asc'], false);
```
