<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Material_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('material');
        $this->tableName = 'material';
    }
    
    protected $field = array('id', 'name', 'model', 'material_list', 'price', 'color', 'type', 'glass', 'weight', 'groups', 'created', 'updated', 'deleted');
    protected $com;
    
    function get_last($limit)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('name', 'asc'); 
        $this->db->limit($limit);
        return $this->db->get(); 
    }
    
    function search($model, $material, $color, $type, $group)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('name', 'asc'); 
        $this->cek_null_string($model, 'model');
        $this->cek_null_string($material, 'material_list'); 
        $this->cek_null_string($color, 'color'); 
        $this->cek_null_string($type, 'type'); 
        $this->cek_null_string($group, 'groups'); 
        return $this->db->get(); 
    }
    
    function report($model, $material)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('name', 'asc'); 
        $this->cek_null($model, 'model');
        $this->cek_null($material, 'material_list'); 
        return $this->db->get(); 
    }
    
    function valid_material($type,$color,$model,$item, $group)
    {
        $this->db->where('color', $color);
        $this->db->where('type', $type);
        $this->db->where('model', $model);
        $this->db->where('material_list', $item);
        $this->db->where('groups', $group);
        $this->db->where('deleted', $this->deleted);
        $query = $this->db->get($this->tableName)->num_rows();

        if($query > 0){ return FALSE; }else{ return TRUE; }
    }
    
    function validating_material($type,$color,$model,$item,$group,$id)
    {
        $this->db->where('color', $color);
        $this->db->where('type', $type);
        $this->db->where('model', $model);
        $this->db->where('material_list', $item);
        $this->db->where('groups', $group);
        $this->db->where('deleted', $this->deleted);
        $this->db->where_not_in('id', $id);
        $query = $this->db->get($this->tableName)->num_rows();

        if($query > 0){ return FALSE; }else{ return TRUE; }
    }
    
}

?>