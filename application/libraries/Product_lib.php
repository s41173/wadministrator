<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_lib extends Custom_Model {
    
    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'product';
    }

    protected $field = array('id', 'sku', 'category', 'assembly', 'name', 'model', 'description', 
                             'bone', 'daun', 'daunhidup', 'kacamati', 'weight',
                             'kacamati_bawah', 'tulang_daun', 'panel',
                             'image', 'color', 'url1', 'url2', 'url3', 'url4', 'url5', 'url6', 'flat_price', 'publish',
                             'created', 'updated', 'deleted');

    function cek_relation($id,$type)
    {
       $this->db->where($type, $id);
       $query = $this->db->get('product')->num_rows();
       if ($query > 0) { return FALSE; } else { return TRUE; }
    }
    
    function valid_product($id)
    {
       $this->db->where('id', $id);
       $query = $this->db->get('product')->num_rows();
       if ($query > 0) { return TRUE; } else { return FALSE; }
    }

    function get_details($name=null)
    {
        if ($name)
        {
           $this->db->select($this->field);
           $this->db->where('name', $name);
           return $this->db->get('product')->row();
        }
    }

    function get_id($name=null)
    {
        if ($name)
        {
           $this->db->select($this->field);
           $this->db->where('name', $name);
           $res = $this->db->get('product')->row();
           return $res->id;
        }
    }

    function get_name($id=null)
    {
        if ($id)
        {
           $this->db->select($this->field);
           $this->db->where('id', $id);
           $res = $this->db->get('product')->row();
           return @$res->name;
        }
    }
    
    function get_sku($id=null)
    {
        if ($id)
        {
           $this->db->select($this->field);
           $this->db->where('id', $id);
           $res = $this->db->get('product')->row();
           return @$res->sku;
        }
    }
    
    function get_model($id=null)
    {
        if ($id)
        {
           $this->db->select($this->field);
           $this->db->where('id', $id);
           $res = $this->db->get('product')->row();
           return $res->model;
        }
    }
    
    function get_bone($id=null)
    {
        if ($id)
        {
           $this->db->select($this->field);
           $this->db->where('id', $id);
           $res = $this->db->get('product')->row();
           return $res->bone;
        }
    }
    
    function get_detail_based_id($id=null)
    {
        if ($id)
        {
           $this->db->select($this->field);
           $this->db->where('id', $id);
           $res = $this->db->get('product')->row();
           return $res;
        }
    }
    
    function get_weight($id=null)
    {
        if ($id)
        {
           $this->db->select($this->field);
           $this->db->where('id', $id);
           $res = $this->db->get('product')->row();
           return $res->weight;
        }
    }

    function get_unit($id=null)
    {
        if ($id)
        {
           $this->db->select($this->field);
           $this->db->where('id', $id);
           $res = $this->db->get('product')->row();
           return $res->unit;
        }
    }

    function get_all()
    {
      $this->db->select($this->field);
      $this->db->order_by('name', 'asc');
      return $this->db->get('product');
    }
    
    function combo()
    {
        $this->db->select($this->field);
        $this->db->where('deleted', $this->deleted);
        $this->db->where('publish', 1);
        $val = $this->db->get($this->tableName)->result();
        if ($val){ foreach($val as $row){$data['options'][$row->id] = ucfirst($row->name);} }
        else { $data['options'][''] = '--'; }        
        return $data;
    }
    
    function combo_publish($id)
    {
        $this->db->select($this->field);
        $this->db->where('deleted', $this->deleted);
        $this->db->where('publish', 1);
        $this->db->where_not_in('id', $id);
        $val = $this->db->get($this->tableName)->result();
        if ($val){ foreach($val as $row){$data['options'][$row->id] = ucfirst($row->name);} }
        else { $data['options'][''] = '--'; }        
        return $data;
    }
    
    function get_product_based_category($cat)
    {
        $this->db->select_sum('qty');
        $this->db->where('deleted', $this->deleted);
        $this->db->where('publish', 1);
        $this->db->where('category', $cat);
        $res = $this->db->get($this->tableName)->row_array();
        return intval($res['qty']); 
    }
    
    // api purpose
    
    function get_series_based_cat($catid){
        
        $this->db->select('model');
        $this->db->where('deleted', $this->deleted);
        $this->db->where('publish', 1);
        $this->db->where('category', $catid);
        $this->db->distinct();
        return $this->db->get($this->tableName)->result();
    }
    
    function get_poduct_based_cat_model($catid,$model){
        
        $this->db->select($this->field);
        $this->db->where('deleted', $this->deleted);
        $this->db->where('publish', 1);
        $this->db->where('category', $catid);
        $this->db->where('model', $model);
        return $this->db->get($this->tableName)->result();
    }

}

/* End of file Property.php */