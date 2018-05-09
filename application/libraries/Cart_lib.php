<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cart_lib extends Main_model {

    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'cart';
    }

    private $ci;
    
    protected $field = array('id', 'agent_id', 'product_id', 'qty', 'tax', 'amount', 'price', 'attribute', 'description' , 'created');
       
    
    function get_by_agent($agent=null)
    {
        $this->db->select($this->field);
        $this->cek_null($agent, 'agent_id');
        $this->db->order_by('created', 'desc'); 
        $this->db->from($this->tableName); 
        return $this->db->get(); 
    }


}

/* End of file Property.php */
