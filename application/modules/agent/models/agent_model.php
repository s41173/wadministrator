<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Agent_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('agent');
        $this->tableName = 'agent';
    }
    
    protected $field = array('id', 'code', 'name', 'type', 'address', 'phone1', 'phone2', 'fax', 'email', 'password', 'groups',
                             'website', 'state', 'city', 'region', 'zip', 'notes', 'image', 'joined', 'acc_no', 'acc_name', 'acc_bank',
                             'status', 'created', 'updated', 'deleted');
    protected $com;
    
    function login($user=null,$pass=null){
        
        $this->db->where('email', $user);
        $this->db->where('password', $pass);
        $this->db->where('status', 1);
        $this->db->limit(1);
        $res = $this->db->get($this->tableName)->num_rows();
        if ($res > 0){ return TRUE; }else{ return FALSE; }
    }
    
    function get_last($limit, $offset=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('id', 'desc'); 
        $this->db->limit($limit, $offset);
        return $this->db->get(); 
    }
    
    function search($cat=null,$publish=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null_string($cat, 'city');
        $this->cek_null_string($publish, 'status');
        
        $this->db->order_by('id', 'asc'); 
        return $this->db->get(); 
    }
    
    function code_counter(){
        $val = $this->counter()->row_array();
        $val = intval($val['id']+1);
        return 'DA-0'.$val;
    }
    
    function get_by_username($username=null){
        
        $this->db->select($this->field);
        $this->db->where('email', $username);
        $this->db->where('deleted', $this->deleted);
        return $this->db->get($this->tableName);
    }
    
    function cek_user($username){
        
        $this->db->select($this->field);
        $this->db->where('email', $username);
        $this->db->where('deleted', $this->deleted);
        $res = $this->db->get($this->tableName)->num_rows();
        if ($res > 0){ return TRUE; }else{ return FALSE; }
    }

}

?>