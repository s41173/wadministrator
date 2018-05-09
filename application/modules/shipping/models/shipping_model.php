<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shipping_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('shipping');
        $this->tableName = 'shipping';
    }
    
    protected $field = array('shipping.id', 'shipping.sales_id', 'shipping.shipdate', 'shipping.courier', 'shipping.awb',
                             'shipping.origin', 'shipping.origin_id', 'shipping.origin_desc',
                             'shipping.dest', 'shipping.district', 'shipping.dest_desc', 'shipping.package', 'shipping.rate', 
                             'shipping.weight', 'shipping.amount', 'shipping.paid_date', 'shipping.status');
    protected $com;
    
    function get_last($limit, $offset=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->order_by('id', 'desc'); 
        $this->db->limit($limit, $offset);
        return $this->db->get(); 
    }
    
    function search($cust=null,$paid=null,$ship=null)
    {   
        $this->db->select($this->field);
        $this->db->from('sales, shipping');
        $this->db->where('sales.id = shipping.sales_id');

        $this->cek_null_string($cust, 'sales.cust_id');
        
        if ($paid == '1'){ $this->db->where('shipping.paid_date IS NOT NULL'); }
        elseif ($paid == '0'){ $this->db->where('shipping.paid_date IS NULL'); }
        
        if ($ship == '1'){ $this->db->where('shipping.shipdate IS NOT NULL'); }
        elseif ($ship == '0'){ $this->db->where('shipping.shipdate IS NULL'); }
        
        $this->db->order_by('shipping.sales_id', 'desc'); 
        return $this->db->get(); 
    }
    
    function report($sales_start=null,$sales_end=null,$shipping_start=null,$shipping_end=null,$status=null)
    {   
        $this->db->select($this->field);
        $this->db->from('sales, shipping');
        $this->db->where('sales.id = shipping.sales_id');

        $this->between('sales.dates', $sales_start, $sales_end);
        $this->between('shipping.shipdate', $shipping_start, $shipping_end);
        
        if ($status == '1'){ $this->db->where('shipping.status',$status); }
        elseif ($status == '0'){ $this->db->where('shipping.status',$status); }

        $this->db->order_by('shipping.id', 'desc'); 
        return $this->db->get(); 
    }
    
    function counter($type=0)
    {
       $this->db->select_max('id');
       $query = $this->db->get($this->tableName)->row_array(); 
       if ($type == 0){ return intval($query['id']+1); }else { return intval($query['id']); }
    }
    
    function valid_payment_confirm($sid)
    {
       $this->db->where('id', $sid);
       $query = $this->db->get($this->tableName)->row();
       if ($query->status == 1){ return FALSE; }else{ return TRUE; }
    }
    
    function valid_shipdate($sid)
    {
       $this->db->where('id', $sid);
       $query = $this->db->get($this->tableName)->row();
       if ($query->shipdate){ return FALSE; }else{ return TRUE; }
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
        $this->db->where('sales.confirmation', 1);
        $query = $this->db->get()->row_array();
        return intval($query['qtys']);
    }

}

?>