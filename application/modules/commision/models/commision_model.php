<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Commision_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('commision');
        $this->tableName = 'commision';
    }
    
    protected $field = array('commision.id', 'commision.sales_id', 'commision.code', 'commision.dates', 'commision.phase', 'commision.amount', 'commision.payment_id', 'commision.bank_id',
                             'commision.confirmation', 'commision.log', 'commision.created', 'commision.updated', 'commision.deleted');
        
    protected $com;
    
    function get_last($sid=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->where('sales_id', $sid);
        $this->db->order_by('id', 'desc'); 
        return $this->db->get(); 
    }
    
    function search($agent=null)
    {   
        $this->db->select($this->field);
        $this->db->from('sales, commision');
        $this->db->where('sales.id = commision.sales_id');
        
        $this->db->where('commision.deleted', $this->deleted);
        $this->cek_null_string($agent, 'sales.agent_id');
        $this->db->order_by('commision.dates', 'desc'); 
        return $this->db->get(); 
    }
    
    function search_json($agent=null)
    {   
        $this->db->select($this->field);
        $this->db->from('sales, commision');
        $this->db->where('sales.id = commision.sales_id');
        
        $this->db->where('commision.deleted', $this->deleted);
        $this->cek_null_string($agent, 'sales.agent_id');
        $this->db->where('commision.confirmation', 1);
        $this->db->order_by('commision.dates', 'desc'); 
        return $this->db->get(); 
    }
    
    function report($start=null,$end=null)
    {   
        $this->db->select($this->field);
        $this->db->from('sales, commision');
        $this->db->where('sales.id = commision.sales_id');
        
        $this->db->where('commision.deleted', $this->deleted);
        $this->between('commision.dates', $start, $end);
        
        $this->db->order_by('commision.dates', 'desc'); 
        return $this->db->get(); 
    }
    
    function counter($type=0)
    {
       $this->db->select_max('id');
       $query = $this->db->get($this->tableName)->row_array(); 
       if ($type == 0){ return intval($query['id']+1); }else { return intval($query['id']); }
    }
    

    
    function valid_phase($phase,$sid)
    {
       $this->db->where('phase', $phase);
       $this->db->where('sales_id', $sid);
       $query = $this->db->get($this->tableName)->num_rows();
       if ($query > 0){ return FALSE; }else{ return TRUE; }
    }
    

    
}

?>