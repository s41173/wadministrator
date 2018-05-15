<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sales_payment_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('sales_payment');
        $this->tableName = 'sales_payment';
    }
    
    protected $field = array('id', 'sales_id', 'code', 'dates', 'phase', 'amount', 'payment_id', 'bank_id', 'paid_contact', 'due_date',
                             'cc_no', 'cc_name', 'cc_bank', 'sender_name', 'sender_acc', 'sender_bank', 'sender_amount',
                             'confirmation', 'log', 'created', 'updated', 'deleted');
        
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
    
    function search($cust=null,$confirm=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null_string($cust, 'agent_id');
        
        $this->cek_null_string($confirm, 'approved');
        $this->db->order_by('dates', 'desc'); 
        return $this->db->get(); 
    }
    
    function report($start=null,$end=null,$paid=null,$confirm=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->between('dates', $start, $end);
        
        if ($paid == '1'){ $this->db->where('paid_date IS NOT NULL'); }
        elseif ($paid == '0'){ $this->db->where('paid_date IS NULL'); }
        $this->cek_null($confirm, 'confirmation');
        $this->db->order_by('dates', 'desc'); 
        return $this->db->get(); 
    }
    
    function counter($type=0)
    {
       $this->db->select_max('id');
       $query = $this->db->get($this->tableName)->row_array(); 
       if ($type == 0){ return intval($query['id']+1); }else { return intval($query['id']); }
    }
    
    function valid_confirm($sid,$type='id')
    {
       if ($type == 'id'){ $this->db->where('id', $sid); }else{ $this->db->where('code', $sid); }
       $query = $this->db->get($this->tableName)->row();
       if ($query->approved == 1){ return FALSE; }else{ return TRUE; }
    }
    
    function valid_orderid($orderid)
    {
       $this->db->where('code', $orderid);
       $query = $this->db->get($this->tableName)->num_rows();
       if ($query > 0){ return TRUE; }else{ return FALSE; }
    }
    
    function get_id_based_order($orderid){
        
        $this->db->where('code', $orderid);
        $query = $this->db->get($this->tableName)->row();
        return $query->id;
    }
    


}

?>