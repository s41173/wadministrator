<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Prodesc_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    var $table = 'prodesc';
    
    function count_all_num_rows()
    {
        //method untuk mengembalikan nilai jumlah baris dari database.
        return $this->db->count_all($this->table);
    }
    
    function get_last_prodesc($pid=null)
    {
        $this->db->select('id, product, category, desc');
        $this->db->from($this->table);
        $this->cek_null($pid,"product");
        $this->db->order_by('id', 'asc');
        return $this->db->get(); 
    }

    private function cek_null($val,$field)
    {
        if ($val == ""){return null;}
        else {return $this->db->where($field, $val);}
    }
    
    function delete($uid)
    {
        $this->db->where('id', $uid);
        $this->db->delete($this->table); // perintah untuk delete data dari db
    }

    function delete_by_product($product)
    {
        $this->db->where('product', $product);
        $this->db->delete($this->table);
    }
    
    function add($users)
    {
        $this->db->insert($this->table, $users);
    }
    
    function get_prodesc_by_id($uid)
    {
        $this->db->select('id, product, category, desc');
        $this->db->where('id', $uid);
        return $this->db->get($this->table);
    }
    
    function update($uid, $users)
    {
        $this->db->where('id', $uid);
        $this->db->update($this->table, $users);
    }
    
    function valid_prodesc($category,$product)
    {
        $this->db->where('product', $product);
        $this->db->where('category', $category);
        $query = $this->db->get($this->table)->num_rows();
        if($query > 0) { return FALSE; } else { return TRUE; }
    }

    function validating_prodesc($category,$pid,$id)
    {
        $this->db->where('product', $pid);
        $this->db->where('category', $category);
        $this->db->where_not_in('id', $id);
        $query = $this->db->get($this->table)->num_rows();
        if($query > 0) {  return FALSE; } else { return TRUE; }
    }

}

?>