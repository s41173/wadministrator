<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Discount_lib extends Custom_Model {
    
    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'discount';
    }

    protected $field = array('id', 'name', 'start', 'end', 'type', 'minimum', 'percentage', 'agent', 'status', 'created', 'updated', 'deleted');
    
    private function cek_discount($dates=null){
       
        $val = array('updated' => date('Y-m-d H:i:s'), 'status' => 0);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->where('end <', $dates);
        $this->db->where('status', 1);
        $this->db->update($this->tableName, $val);
    }
    
    function get_discount($nominal=0,$agent=0,$dates=0){
        
        $this->cek_discount($dates); // cek discount
        
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->where('minimum <=', $nominal);
        $this->db->where('end >=', $dates);
        $this->db->where('status', 1);
        $this->db->order_by('minimum', 'desc'); 
        $this->db->order_by('type', 'desc'); 
        $this->db->limit(1);
        $res = $this->db->get()->row(); 
        if ($res){
          $source = explode(',', $res->agent);
          $res1 = array_search($agent, $source);
          if ($res1 !== false) { return $res->percentage; } else { return 0; }
          
        }else{ return 0; }
    }

}

/* End of file Property.php */