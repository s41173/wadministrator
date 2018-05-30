<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Courier_lib extends Main_model {

    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'courier';
    }

    private $ci;
    
    protected $field = array('id', 'ic', 'name', 'phone', 'address', 'email', 'image', 'company', 'joined', 'status', 'created', 'updated', 'deleted');
        
    function get_detail($id=null,$type=null)
    {
        $this->db->select($this->field);
        $this->db->where('id', $id);
        $val = $this->db->get($this->tableName)->row();
        if ($val){ return ucfirst($val->$type); }
    }
    
    function get_type($id=null)
    {
        if ($id)
        {
            $this->db->select('type');
            $this->db->where('id', $id);
            $val = $this->db->get($this->tableName)->row();
            if ($val){ return $val->type; }else { return 0; }
        }
        else { return 0; }
    }
    
    function get_cust_type($type=null)
    {
        $this->db->select($this->field);
        $this->db->where('type', $type);
        $this->db->where('status', 1);
        $this->db->where('deleted', $this->deleted);
        $val = $this->db->get($this->tableName)->result();
        return $val;
    }
    
    function get_details($id)
    {
       $this->db->where('id', $id);
       return $this->db->get($this->tableName); 
    }
    
    function combo()
    {
        $this->db->select($this->field);
        $this->db->where('deleted', NULL);
        $this->db->order_by('name', 'asc');
        $val = $this->db->get($this->tableName)->result();
        $data = null;
        if ($val){
          foreach($val as $row){ $data['options'][$row->id] = ucfirst($row->name); }    
        }else{ $data['options'][''] = '--'; }
        return $data;
    }
    
    function valid_customer($email,$phone1,$phone2){
        
        $this->db->select($this->field);
        $this->db->where('deleted', NULL);
        $this->db->where('email', $email);
        $this->db->or_where('phone1', $phone1); 
        $this->db->or_where('phone2', $phone2); 
        $val = $this->db->get($this->tableName)->num_rows();
        if ($val > 0){ return FALSE; }else{ return TRUE; }
    }
    
    function add_customer($users)
    {
        $this->db->insert($this->tableName, $users);
    }
    
    function delete_customer($uid)
    {
        $this->db->where('id', $uid);
        $this->db->delete($this->tableName);
    }
    
    function edit_customer($customer,$id){
        
        $this->db->where('id', $id);
        $this->db->update($this->tableName, $customer);
    }
    
    


}

/* End of file Property.php */