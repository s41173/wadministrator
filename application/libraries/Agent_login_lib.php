<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Agent_login_lib
{
    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'agent_login_status';
        $this->ci = & get_instance();
    }

    private $ci,$tableName,$deleted;
    
    public function add($user=0, $log=0, $mobile=0)
    {
        $trans = array('userid' => $user, 'log' => $log, 'mobile' => $mobile);
        if ($this->cek($user,$mobile) == TRUE){ $this->ci->db->insert($this->tableName, $trans); }
        else { $this->edit($user,$log,$mobile); }
    }

    private function cek($user,$mobile=0)
    {
        $this->ci->db->where('userid', $user);
        $this->ci->db->where('mobile', $mobile);
        $num = $this->ci->db->get($this->tableName)->num_rows();
        if ($num > 0){ return FALSE; }else { return TRUE; }
    }
    
    private function edit($user,$log,$mobile)
    {
        $trans = array('log' => $log, 'mobile' => $mobile);
        $this->ci->db->where('userid', $user);
        $this->ci->db->update($this->tableName, $trans);
    }
    
    function valid($user,$log,$mobile)
    {
       $this->ci->db->where('userid', $user);
       $this->ci->db->where('log', $log);
       $this->ci->db->where('mobile', $mobile);
       $num = $this->ci->db->get($this->tableName)->num_rows(); 
       if ($num > 0){ return TRUE; }else { return FALSE; }
    }
    
}


/* End of file Property.php */