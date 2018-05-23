<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shiprate_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('shiprate');
        $this->tableName = 'delivery_rate';
    }
    
    protected $field = array('id', 'period', 'distance', 'payment_type', 'minimum', 'rate', 'created', 'updated', 'deleted');
    protected $com;
            
    function count_all_num_rows()
    {
        //method untuk mengembalikan nilai jumlah baris dari database.
        return $this->db->count_all($this->table);
    }
    
    function get_last($limit, $offset=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->limit($limit, $offset);
        return $this->db->get(); 
    }
    
    function report()
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('code', 'asc'); 
        return $this->db->get(); 
    }
    
    function search($payment)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null_string($payment, 'payment_type');
        return $this->db->get(); 
    }
    
    function valid_delivery($period,$distance,$payment,$minimum=0)
    {
       $this->db->where('period', $period);
       $this->db->where('distance', $distance);
       $this->db->where('payment_type', $payment);
       $this->db->where('minimum', $minimum);
       $query = $this->db->get($this->tableName)->num_rows();

       if($query > 0){ return FALSE; }else{ return TRUE; } 
    }
    
    function validating_delivery($id,$period,$distance,$payment,$minimum=0)
    {
       $this->db->where('period', $period);
       $this->db->where('distance', $distance);
       $this->db->where('payment_type', $payment);
       $this->db->where('minimum', $minimum);
       $this->db->where_not_in('id', $id);
       $query = $this->db->get($this->tableName)->num_rows();

       if($query > 0){ return FALSE; }else{ return TRUE; } 
    }
    
    
}

?>