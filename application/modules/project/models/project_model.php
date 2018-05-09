<?php

class Project_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    var $table = 'project';
    
    function count_all_num_rows()
    {
        //method untuk mengembalikan nilai jumlah baris dari database.
        return $this->db->count_all($this->table);
    }
    
    function get_last_project($limit, $offset)
    {
        $this->db->select('id, name, dates, desc, image, url, status');
        $this->db->from($this->table);
        $this->db->order_by('dates', 'desc');
        $this->db->limit($limit, $offset);
        return $this->db->get();
    }

        
    function delete($uid)
    {
        $this->db->where('id', $uid);
        $this->db->delete($this->table); // perintah untuk delete data dari db
    }
    
    function add($users)
    {
        $this->db->insert($this->table, $users);
    }
    
    function get_project_by_id($uid)
    {
        $this->db->select('id, name, dates, desc, image, url, status');
        $this->db->where('id', $uid);
        return $this->db->get($this->table);
    }
   
    function update($uid, $value)
    {
        $this->db->where('id', $uid);
        $this->db->update($this->table, $value);
    }

    function valid_name($name)
    {
        $this->db->where('name', $name);
        $query = $this->db->get($this->table)->num_rows();
        if($query > 0) { return FALSE; } else { return TRUE; }
    }

    function validating_name($name,$id)
    {
        $this->db->where('name', $name);
        $this->db->where_not_in('id', $id);
        $query = $this->db->get($this->table)->num_rows();
        if($query > 0){ return FALSE; } else { return TRUE; }
    }
}

?>