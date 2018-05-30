<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Categoryproduct_lib extends Main_Model {

    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'category';
    }
    
    protected $field = array('id', 'name', 'parent_id', 'publish', 'orders', 'image', 'permalink');
    
    function get()
    {
        $this->db->select($this->field);
        $this->db->where('deleted', NULL);
        $this->db->where('publish',1);
        $this->db->order_by('name', 'asc');
        return $this->db->get($this->tableName)->result();
    }
    
    function get_based_parent($parent=0)
    {
        $this->db->select($this->field);
        $this->db->where('deleted', NULL);
        $this->db->where('publish',1);
        $this->db->where('parent_id',$parent);
        $this->db->order_by('orders', 'asc');
        return $this->db->get($this->tableName)->result();
    }

    function combo()
    {
        $this->db->select($this->field);
        $this->db->where('deleted', NULL);
        $this->db->where('publish',1);
//        $this->db->where('parent_id >',0);
        $this->db->order_by('name', 'asc');
        $val = $this->db->get($this->tableName)->result();
        $data['options'][0] = 'Top';
        foreach($val as $row){ $data['options'][$row->id] = ucfirst($row->name); }
        return $data;
    }

    function combo_all()
    {
        $this->db->select($this->field);
        $this->db->where('deleted', NULL);
        $this->db->where('publish',1);
        $this->db->order_by('name', 'asc');
        $data['options'][''] = '-- All --';
        $val = $this->db->get($this->tableName)->result();
        foreach($val as $row){ $data['options'][$row->id] = ucfirst($row->name); }
        return $data;
    }

    function combo_update($id)
    {
        $this->db->select($this->field);
        $this->db->where('deleted', NULL);
        $this->db->where('publish',1);
        $this->db->order_by('name', 'asc');
        $this->db->where_not_in('id', $id);
        $val = $this->db->get($this->tableName)->result();
        $data['options'][0] = 'Top';
        foreach($val as $row){ $data['options'][$row->id] = ucfirst($row->name); }
        return $data;
    }

    function get_name($id=null)
    {
        if ($id)
        {
            $this->db->select($this->field);
            $this->db->where('id', $id);
            $val = $this->db->get($this->tableName)->row();
            if ($val){ return $val->name; }
        }
        else if($id == 0){ return 'Top'; }
        else { return ''; }
    }
    
    function get_id($id=null)
    {
        if ($id)
        {
            $this->db->select($this->field);
            $this->db->where('name', $id);
            $val = $this->db->get($this->tableName)->row();
            if ($val){ return $val->id; }else { return 0; }
        }
        else { return 0; }
    }

}

/* End of file Property.php */