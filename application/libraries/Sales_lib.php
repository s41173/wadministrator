<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales_lib extends Custom_Model {
    
    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'sales';
    }

    protected $field = array('id', 'code', 'dates', 'agent_id', 'cust_id', 'amount', 'tax', 'cost', 'total', 'shipping',
                             'approved', 'log', 'created', 'updated', 'deleted');

    function cek_relation($id,$type)
    {
       $this->db->where($type, $id);
       $query = $this->db->get('product')->num_rows();
       if ($query > 0) { return FALSE; } else { return TRUE; }
    }
    
    function get_id_based_order($orderid){
        
        $this->db->where('code', $orderid);
        $query = $this->db->get($this->tableName)->row();
        if ($query){ return $query->id; }else{ return FALSE; }
    }
    
    function get_detail_sales($id=null)
    {
        if ($id)
        {
           $this->db->select($this->field);
           $this->db->where('id', $id);
           $res = $this->db->get($this->tableName)->row();
           return $res;
        }
    }
    
    function get_transaction_sales($id=null)
    {
        if ($id)
        {
           $this->db->where('sales_id', $id);
           $res = $this->db->get('sales_item');
           return $res;
        }
    }
    
    function total($pid)
    {
        $this->db->select_sum('tax');
        $this->db->select_sum('amount');
        $this->db->select_sum('price');
        $this->db->select_sum('qty');
        $this->db->where('sales_id', $pid);
        return $this->db->get('sales_item')->row_array();
    }
    
    function cek_shiping_based_sales($sid)
    {
       if ($sid)
        {
           $this->db->select($this->field);
           $this->db->where('sales_id', $sid);
           $res = $this->db->get($this->tableName)->row();
           if ($res){
              if ($res->shipdate){ return true; }else{ return false; } 
           }
           
        } 
    }
    
    function cek_customer($id)
    {
       $this->db->where('cust_id', $id);
       $this->db->where('deleted', NULL);
       $query = $this->db->get($this->tableName)->num_rows();
       if ($query > 0) { return FALSE; } else { return TRUE; }
    }

}

/* End of file Property.php */