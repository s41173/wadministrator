<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cart_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('cart');
        $this->tableName = 'cart';
    }
    
    protected $field = array('id', 'agent_id', 'product_id', 'qty', 'tax', 'amount', 'price', 'attribute', 'description' , 'publish', 'created');
    protected $com;
    
    function get_by_agent($agent=null)
    {
        $this->db->select($this->field);
        $this->cek_null($agent, 'agent_id');
        $this->db->order_by('created', 'desc'); 
        $this->db->from($this->tableName); 
        return $this->db->get(); 
    }
    
    function delete_by_agent($agent=null,$publish=1){
        
        $this->db->where('agent_id', $agent);
        $this->db->where('publish', $publish);
        $this->db->delete($this->tableName);
    }
    
    function updateid($uid, $users)
    {
        $this->db->where('id', $uid);
        $this->db->update($this->tableName, $users);
        
        $val = array('created' => date('Y-m-d H:i:s'));
        $this->db->where('id', $uid);
        $this->db->update($this->tableName, $val);
    }
    
    function publish($uid)
    {
        $this->db->where('id', $uid);
        $this->db->from($this->tableName); 
        $value = $this->db->get()->row();
        if ($value->publish == 1){ $publish = 0; $stts = 'unpublished'; }else{ $publish = 1; $stts = 'published'; }
        
        $val = array('publish' => $publish);
        $this->db->where('id', $uid);
        $this->db->update($this->tableName, $val);
        return $stts;
    }
    
    function total($agent=0,$publish=0){
        
        $this->db->select_sum('amount');
        $this->db->select_sum('price');
        $this->db->select_sum('qty');
        $this->db->where('agent_id', $agent);
        $this->db->where('publish', $publish);
        return $this->db->get($this->tableName)->row_array();
    }

}

?>