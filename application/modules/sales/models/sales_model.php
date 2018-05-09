<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sales_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('sales');
        $this->tableName = 'sales';
    }
    
    protected $field = array('id', 'code', 'dates', 'agent_id', 'cust_id', 'amount', 'tax', 'cost', 'discount', 'total', 'shipping',                            
                             'approved', 'log', 'created', 'updated', 'deleted');
    protected $com;
    
    function get_last($limit, $offset=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('id', 'desc'); 
        $this->db->limit($limit, $offset);
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
    
    function search_json($agent=null,$confirm=null,$limit=0)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->where('agent_id', $agent);
        $this->db->where('approved', $confirm);
        $this->db->limit($limit);
        $this->db->order_by('dates', 'desc'); 
        return $this->db->get(); 
    }
    
    function report($start=null,$end=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->between('dates', $start, $end);
        
        $this->db->where('approved', 1);
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
    
     function get_sales_based_order($orderid){
        
        $this->db->where('code', $orderid);
        return $this->db->get($this->tableName);
    }
    
    function get_sales_qty_based_category($cat=0,$month=null,$year=null)
    {
        if (!$month){ $month = date('n'); }
        if (!$year){ $year = date('Y'); }
        
        $this->db->select_sum('sales_item.qty', 'qtys');
        
        $this->db->from('sales, sales_item, product, category');
        $this->db->where('sales.id = sales_item.sales_id');
        $this->db->where('sales_item.product_id = product.id');
        $this->db->where('product.category = category.id');
        
        $this->db->where('MONTH(sales.dates)', $month);
        $this->db->where('YEAR(sales.dates)', $year);
        $this->db->where('category.id', $cat);
        $this->db->where('sales.approved', 1);
        $query = $this->db->get()->row_array();
        return intval($query['qtys']);
    }

}

?>