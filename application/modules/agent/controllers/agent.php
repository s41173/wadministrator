<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Agent extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Agent_model', '', TRUE);

        $this->properti = $this->property->get();
//        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->role = new Role_lib();
        $this->city = new City_lib();
        $this->disctrict = new District_lib();
        $this->sms = new Sms_lib();
        $this->log = new Log_lib();
        $this->login = new Agent_login_lib();
        $this->customer = new Customer_lib();
        $this->sales = new Sales_lib();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token'); 
        
    }

    private $properti, $modul, $title, $customer, $city, $disctrict, $sales;
    private $role, $sms, $log, $login;

    function index()
    {
       $this->get_last(); 
    }
    
    // ajax
    function get_pass(){ echo $this->random_password(); }
    
    // ------ json login -------------------
    function login(){
        
        $datas = (array)json_decode(file_get_contents('php://input'));
        $user = $datas['username'];
        $pass = $datas['password'];
        
        $status = true;
        $error = null;
        $userid = null;
        $logid = null;
        $group = null;
        
        if ($user != null && $pass != null){
            
            $res = $this->Agent_model->login($user,$pass);
            if ($res == FALSE){ $status = false; $error = 'Invalid Credential..!'; }
            else{
                
                if (isset($datas['mobile'])){ $mobile = $datas['mobile']; }else{ $mobile = 0; }
                $logid = $this->random_password();
                $res = $this->Agent_model->get_by_username($user)->row(); 
                $userid = $res->id; $group = $res->groups;
                $this->login->add($userid, $logid, $mobile);
            }
            
        }else{ $status = false; $error = "Wrong format..!!"; }
        
        $response = array('status' => $status, 'error' => $error, 'user' => $datas['username'], 'userid' => $userid, 'group' => $group, 'log' => $logid); 
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;
    }
    
    function forgot(){
        
        $datas = (array)json_decode(file_get_contents('php://input'));
        $user = $datas['username'];
        
        $status = true;
        $error = null;
//        $user = 'info@dswip.com';
        
        if ($user != null){
            
            $res = $this->Agent_model->cek_user($user);
            if ($res == TRUE){ 
                $val = $this->Agent_model->get_by_username($user)->row();
                if ($this->send_confirmation_email($val->id) == TRUE){ $status = true; $error = "Password has been sent to your email."; }else{ $status = false; $error = 'Email Not Sent..!'; }
            }else{ $status = false; $error = 'Invalid Agent Credential..!'; }
            
        }else{ $status = false; $error = "Wrong format..!!"; }
        
        $response = array('status' => $status, 'error' => $error, 'user' => $datas['username']); 
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;
    }
    
    function otentikasi(){
       
        $datas = (array)json_decode(file_get_contents('php://input'));
        $user = $datas['userid'];
        $log = $datas['log'];
        
        $status = true;
        $error = null;
        
        if (isset($datas['mobile'])){ $mobile = $datas['mobile']; }else{ $mobile = 0; }
        
        if ( isset($datas['userid']) && isset($datas['log']) ){
           if ( $this->login->valid($user, $log, $mobile) == FALSE ){ $status = false; $error = "user already login..!!"; }      
        }else{ $status = false; $error = "Wrong format..!!"; }
        
        $response = array('status' => $status, 'error' => $error); 
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;
    }
    
    function detail(){
       
        $datas = (array)json_decode(file_get_contents('php://input'));
        $user = $datas['userid'];
        
        $status = true;
        $error = null;
        
        if ( isset($datas['userid']) ){
            
        $res = $this->Agent_model->get_by_id($user)->row(); 
        $output[] = array ("id" => $res->id, "code" => strtoupper($res->code), "name" => $res->name,
                           "type" => $res->type, "address" => $res->address, "phone" => $res->phone1.' / '.$res->phone2,
                           "fax" => $res->fax, "email" => $res->email, "state" => $res->state, "statename" => $this->disctrict->get_province($res->state),
                           "city" => $res->city, "cityname" => $this->city->get_name($res->city),
                           "region" => $res->region, "regionname" => $this->disctrict->get_name($res->region), 
                           "zip" => $res->zip, "group" => $res->groups,
                           "image" => base_url().'images/agent/'.$res->image);
            
           
        }else{ $status = false; $error = "Wrong format..!!"; }
        
        $stts = array('status' => $status, 'error' => $error); 

        $response['content'] = $output;
        $response['status']  = $stts;
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit; 
    }
    
    function get_customer(){
        
        $datas = (array)json_decode(file_get_contents('php://input'));
        $agent = $datas['agent_id'];
        
        $result = $this->customer->get_customer_by_agent($agent)->result();
        
        if ($result){
	foreach($result as $res)
	{
	   $output[] = array ("id" => $res->id, "name" => $res->first_name, "email" => $res->email, "type" => $res->type, "phone" => $res->phone1.' / '.$res->phone2, "image" => base_url().'images/customer/'.$res->image);
	}
        $response['content'] = $output;
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit; 
        }   
    }
    
    function get_customer_id(){
        
        $datas = (array)json_decode(file_get_contents('php://input'));
        $uid = $datas['id'];
        
        $res = $this->customer->get_by_id($uid)->row();
        
	$output[] = array ("id" => $res->id, "name" => ucfirst($res->first_name), "last_name" => ucfirst($res->last_name), "type" => $res->type, "phone" => $res->phone1.' / '.$res->phone2, 
                           "agent_id" => $res->agent_id, "address" => $res->address, "shipping_address" => $res->shipping_address, "email" => $res->email,
                           "state" => $this->disctrict->get_province($res->state), "city" => $this->city->get_name($res->city), "cityid" => $res->city,
                           "region" => $this->disctrict->get_name($res->region), "regionid" => $res->region, "zip" => $res->zip,
                           "image" => base_url().'images/customer/'.$res->image);
        
        $response['content'] = $output;
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit;    
    }
    
    function add_customer(){
        
        $datax = (array)json_decode(file_get_contents('php://input')); 
        
        if ($this->customer->valid_customer($datax['email'], $datax['phone1'], $datax['phone2']) == TRUE){
            
            $customer = array('first_name' => strtolower($datax['fname']), 
                          'last_name' => strtolower($datax['lname']), 'agent_id' => $datax['agent'],
                          'type' => $datax['type'], 'address' => $datax['address'],
                          'shipping_address' => $datax['ship_address'], 'phone1' => $datax['phone1'], 'phone2' => $datax['phone2'],
                          'email' => $datax['email'], 'region' => $datax['region'],
                          'city' => $datax['city'], 'state' => $this->city->get_province_based_city($datax['city']),
                          'zip' => $datax['zip'], 'joined' => date('Y-m-d H:i:s'), 'status' => 1,
                          'created' => date('Y-m-d H:i:s'));

            $this->customer->add_customer($customer);
            $response = array('status' => true, 'error' => 'One Customer Successfully Added..!'); 
        }else{
            $response = array('status' => false, 'error' => 'Invalid Customer, Email Or Phone Registered..!'); 
        }
        
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;
        
    }
    
    function edit_customer(){
        
        $datax = (array)json_decode(file_get_contents('php://input')); 
            
        if ($datax['status'] == 'update'){
            $customer = array('first_name' => strtolower($datax['fname']), 
                          'last_name' => strtolower($datax['lname']), 'agent_id' => $datax['agent'],
                          'type' => $datax['type'], 'address' => $datax['address'],
                          'shipping_address' => $datax['ship_address'], 'phone1' => $datax['phone1'], 'phone2' => $datax['phone2'],
                          'email' => $datax['email'], 'region' => $datax['region'],
                          'city' => $datax['city'], 'state' => $this->city->get_province_based_city($datax['city']),
                          'zip' => $datax['zip'], 'updated' => date('Y-m-d H:i:s'));
        }else{
            $customer = array('first_name' => strtolower($datax['fname']), 
                          'last_name' => strtolower($datax['lname']), 'agent_id' => $datax['agent'],
                          'type' => $datax['type'], 'address' => $datax['address'],
                          'shipping_address' => $datax['ship_address'], 'phone1' => $datax['phone1'], 'phone2' => $datax['phone2'],
                          'email' => $datax['email'],
                          'zip' => $datax['zip'], 'updated' => date('Y-m-d H:i:s'));
        }

        $this->customer->edit_customer($customer,$datax['id']);
        $response = array('status' => true, 'error' => 'One Customer Successfully Updated..!'); 
        
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;
        
    }
    
    function remove_customer(){
        
        $datax = (array)json_decode(file_get_contents('php://input')); 
        
        if ($this->sales->cek_customer($datax['id']) == TRUE){
            
            $this->customer->delete_customer($datax['id']);
            $response = array('status' => true, 'error' => 'One Customer Successfully Removed..!'); 
        }else{
            $response = array('status' => false, 'error' => 'Customer Related To Sales Order..!'); 
        }
        
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;
        
    }
    
    // api batas
     
    public function getdatatable($search=null,$cat='null',$publish='null')
    {
        if(!$search){ $result = $this->Agent_model->get_last($this->modul['limit'])->result(); }
        else {$result = $this->Agent_model->search($cat,$publish)->result(); }
	
        $output = null;
        if ($result){
                
         foreach($result as $res)
	 {   
	   $output[] = array ($res->id, strtoupper($res->code), strtoupper($res->name), strtoupper($res->type),
                              $res->phone1, $res->phone2, $res->email, $this->city->get_name($res->city), 
                              base_url().'images/customer/'.$res->image, $res->status , tglin($res->joined)
                             );
	 } 
         
        $this->output
         ->set_status_header(200)
         ->set_content_type('application/json', 'utf-8')
         ->set_output(json_encode($output))
         ->_display();
         exit;  
        }
    }

    function get_last()
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Customer Manager');
        $data['h2title'] = 'Agent Manager';
        $data['main_view'] = 'agent_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));

        $data['city'] = $this->city->combo_city_db();
        $data['array'] = array('','');
        $data['code'] = $this->Agent_model->code_counter();
        
	// ---------------------------------------- //
 
        $config['first_tag_open'] = $config['last_tag_open']= $config['next_tag_open']= $config['prev_tag_open'] = $config['num_tag_open'] = '<li>';
        $config['first_tag_close'] = $config['last_tag_close']= $config['next_tag_close']= $config['prev_tag_close'] = $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = "<li><span><b>";
        $config['cur_tag_close'] = "</b></span></li>";

        // library HTML table untuk membuat template table class zebra
        $tmpl = array('table_open' => '<table id="datatable-buttons" class="table table-striped table-bordered">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('#','No', 'Code', 'Type', 'Name', 'Phone', 'Email', 'City', 'Joined', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    function publish($uid = null)
    {
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
       $val = $this->Agent_model->get_by_id($uid)->row();
       if ($val->status == 0){ 
           $this->send_confirmation_email($uid); 
           $this->send_confirmation_sms($uid);
           $lng = array('status' => 1); }else { $lng = array('status' => 0); }
       $this->Agent_model->update($uid,$lng);
       echo 'true|Status Changed...!';
       }else{ echo "error|Sorry, you do not have the right to change publish status..!"; }
    }
    
    private function send_confirmation_sms($pid){
       
        $agent = $this->Agent_model->get_by_id($pid)->row();
        $mess = "Anda telah terdaftar menjadi agen Delica Alumunium, dengan code : ".strtoupper($agent->code).". Mohon cek email anda untuk informasi lebih lanjut. Terima Kasih.";
        return $this->sms->send($agent->phone1, $mess);
    }
    
    private function send_confirmation_email($pid)
    {   
        // property display
       $data['p_logo'] = $this->properti['logo'];
       $data['p_name'] = $this->properti['name'];
       $data['p_site_name'] = $this->properti['sitename'];
       $data['p_address'] = $this->properti['address'];
       $data['p_zip'] = $this->properti['zip'];
       $data['p_city'] = $this->properti['city'];
       $data['p_phone'] = $this->properti['phone1'];
       $data['p_email'] = $this->properti['email'];
       
       $agent = $this->Agent_model->get_by_id($pid)->row();

       $data['code']    = strtoupper($agent->code);
       $data['name']    = strtoupper($agent->name);
       $data['type']    = strtoupper($agent->type);
       $data['address'] = $agent->address;
       $data['phone']   = $agent->phone1.' / '.$agent->phone2;
       $data['email']    = $agent->email;
       $data['password'] = $agent->password;
       $data['zip']     = $agent->zip;
       $data['city']    = $this->city->get_name($agent->city);
       $data['joined']  = tglin($agent->joined).' / '. timein($agent->joined);
         
        // email send
        $this->load->library('email');
        $config['charset']  = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';

        $this->email->initialize($config);
        $this->email->from($this->properti['email'], $this->properti['name']);
        $this->email->to($agent->email);
        $this->email->cc($this->properti['cc_email']); 
        
        $html = $this->load->view('agent_confirmation',$data,true); 
        $this->email->subject('Agent Confirmation - '.strtoupper($agent->code));
        $this->email->message($html);
//        $pdfFilePath = FCPATH."/downloads/".$no.".pdf";

        if (!$this->email->send()){ return false; }else{ return true;  }
    }
    
    function delete_all($type='soft')
    {
      if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
      
        $cek = $this->input->post('cek');
        $jumlah = count($cek);

        if($cek)
        {
          $jumlah = count($cek);
          $x = 0;
          for ($i=0; $i<$jumlah; $i++)
          {
             if ($type == 'soft') { $this->Agent_model->delete($cek[$i]); }
             else { $this->remove_img($cek[$i],'force');
                    $this->attribute_customer->force_delete_by_customer($cek[$i]);
                    $this->Agent_model->force_delete($cek[$i]);  }
             $x=$x+1;
          }
          $res = intval($jumlah-$x);
          //$this->session->set_flashdata('message', "$res $this->title successfully removed &nbsp; - &nbsp; $x related to another component..!!");
          $mess = "$res $this->title successfully removed &nbsp; - &nbsp; $x related to another component..!!";
          echo 'true|'.$mess;
        }
        else
        { //$this->session->set_flashdata('message', "No $this->title Selected..!!"); 
          $mess = "No $this->title Selected..!!";
          echo 'false|'.$mess;
        }
      }else{ echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
      
    }

    function delete($uid)
    {
        if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
            $this->Agent_model->delete($uid);
            
            $this->session->set_flashdata('message', "1 $this->title successfully removed..!");

            echo "true|1 $this->title successfully removed..!";
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
        
    }
    
    function add()
    {

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Create New '.$this->modul['title'];
        $data['main_view'] = 'article_form';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $data['language'] = $this->language->combo();
        $data['category'] = $this->category->combo();
        $data['currency'] = $this->currency->combo();
        $data['source'] = site_url($this->title.'/getdatatable');
        
        $this->load->helper('editor');
        editor();

        $this->load->view('template', $data);
    }

    function add_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'category_view';
	$data['form_action'] = site_url($this->title.'/add_process');
	$data['link'] = array('link_back' => anchor('category/','<span>back</span>', array('class' => 'back')));

	// Form validation
        $this->form_validation->set_rules('tcode', 'Agent-Code', 'required|callback_valid_agent');
        $this->form_validation->set_rules('tname', 'Agent Name', 'required');
        $this->form_validation->set_rules('ctype',  'Agent Type', 'required');
        $this->form_validation->set_rules('taddress', 'Address', 'required');
        $this->form_validation->set_rules('tphone1', 'Phone 1', 'required');
        $this->form_validation->set_rules('tphone2', 'Phone 2', '');
        $this->form_validation->set_rules('temail', 'Email', 'required|valid_email|callback_valid_email');
        $this->form_validation->set_rules('twebsite', 'Website', '');
        $this->form_validation->set_rules('ccity', 'City', 'required');
        $this->form_validation->set_rules('cdistrict', 'District', 'required');
        $this->form_validation->set_rules('tzip', 'Zip', '');

        if ($this->form_validation->run($this) == TRUE)
        {
            $config['upload_path'] = './images/agent/';
            $config['file_name'] = split_space($this->input->post('tcode'));
            $config['allowed_types'] = 'jpg|gif|png';
            $config['overwrite'] = true;
            $config['max_size']	= '10000';
            $config['max_width']  = '30000';
            $config['max_height']  = '30000';
            $config['remove_spaces'] = TRUE;

            $this->load->library('upload', $config);
//
            if ( !$this->upload->do_upload("userfile")) // if upload failure
            {
                $info['file_name'] = null;
                $data['error'] = $this->upload->display_errors();
                $customer = array('code' => strtolower($this->input->post('tcode')), 
                                  'name' => strtolower($this->input->post('tname')),
                                  'type' => $this->input->post('ctype'), 'address' => $this->input->post('taddress'),
                                  'phone1' => $this->input->post('tphone1'), 'phone2' => $this->input->post('tphone2'),
                                  'email' => $this->input->post('temail'), 'password' => $this->random_password(),
                                  'website' => $this->input->post('twebsite'), 'region' => $this->input->post('cdistrict'),
                                  'city' => $this->input->post('ccity'), 'state' => $this->city->get_province_based_city($this->input->post('ccity')),
                                  'zip' => $this->input->post('tzip'), 'joined' => date('Y-m-d H:i:s'), 'groups' => $this->input->post('cgroup'),
                                  'acc_no' => $this->input->post('taccno'), 'acc_name' => $this->input->post('taccname'), 'acc_bank' => $this->input->post('tbank'),
                                  'image' => null, 'created' => date('Y-m-d H:i:s'));
            }
            else
            {
                $info = $this->upload->data();
                
                $customer = array('code' => strtolower($this->input->post('tcode')), 
                                  'name' => strtolower($this->input->post('tname')),
                                  'type' => $this->input->post('ctype'), 'address' => $this->input->post('taddress'),
                                  'phone1' => $this->input->post('tphone1'), 'phone2' => $this->input->post('tphone2'),
                                  'email' => $this->input->post('temail'), 'password' => $this->random_password(),
                                  'website' => $this->input->post('twebsite'), 'region' => $this->input->post('cdistrict'),
                                  'city' => $this->input->post('ccity'), 'state' => $this->city->get_province_based_city($this->input->post('ccity')),
                                  'zip' => $this->input->post('tzip'), 'joined' => date('Y-m-d H:i:s'),
                                  'acc_no' => $this->input->post('taccno'), 'acc_name' => $this->input->post('taccname'), 'acc_bank' => $this->input->post('tbank'),
                                  'image' => $info['file_name'], 'created' => date('Y-m-d H:i:s'));
            }

            $this->Agent_model->add($customer);
            $this->session->set_flashdata('message', "One $this->title data successfully saved!");
//            redirect($this->title);
            
            if ($this->upload->display_errors()){ echo "warning|".$this->upload->display_errors(); }
            else { echo 'true|'.$this->title.' successfully saved..!|'.base_url().'images/customer/'.$info['file_name']; }
            
          //  echo 'true';
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }

    }
    
    private function cek_tick($val)
    {
        if (!$val)
        { return 0;} else { return 1; }
    }
    
    private function split_array($val)
    { return implode(",",$val); }
   
    function details($uid){
        $customer = $this->Agent_model->get_by_id($uid)->row();
        
        echo strtoupper($customer->code).'|'.$customer->name.'|'.$customer->type.'|'.$customer->address.'|'.$customer->phone1.'|'.
        $customer->phone2.'|'.$customer->email.'|'.$customer->password.'|'.$customer->website.'|'.
        $customer->city.'|'.$customer->region.'|'.$customer->zip.'|'.base_url().'images/agent/'.$customer->image.'|'.
        $customer->acc_bank.'|'.$customer->acc_no.'|'.$customer->acc_name;
    }

    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($uid=null)
    {        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        $data['main_view'] = 'agent_update';
	$data['form_action'] = site_url($this->title.'/update_process');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));
        $data['source'] = site_url($this->title.'/getdatatable');

        $data['city'] = $this->city->combo_city_db();
        $data['district'] = $this->disctrict->combo_district_db(null);
        $data['array'] = array('','');
        
        $customer = $this->Agent_model->get_by_id($uid)->row();
	$this->session->set_userdata('langid', $customer->id);
        
        $data['default']['code'] = strtoupper($customer->code);
        $data['default']['name'] = $customer->name;
        $data['default']['type'] = $customer->type;
        $data['address'] = $customer->address;
        $data['default']['phone1'] = $customer->phone1;
        $data['default']['phone2'] = $customer->phone2;
        $data['default']['email'] = $customer->email;
        $data['default']['password'] = $customer->password;
        $data['default']['website'] = $customer->website;
        $data['default']['city'] = $customer->city;
        $data['default']['district'] = $customer->region;
        $data['default']['zip'] = $customer->zip;
        $data['default']['image'] = base_url().'images/agent/'.$customer->image;
        $data['default']['pass'] = $customer->password;
        
        $data['bank'] = $customer->acc_bank;
        $data['default']['accno'] = $customer->acc_no;
        $data['default']['accname'] = $customer->acc_name;

        $data['default']['group'] = $customer->groups;
        
        $this->load->view('template', $data);
    }
    
    function image_gallery($pid=null)
    {        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        $data['main_view'] = 'article_form';
	$data['form_action'] = site_url($this->title.'/add_image/'.$pid);
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $result = $this->Agent_model->get_by_id($pid)->row();
        
        // library HTML table untuk membuat template table class zebra
        $tmpl = array('table_open' => '<table id="" class="table table-striped table-bordered">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No', 'Name', 'Image');
        
        for ($i=1; $i<=5; $i++)
        {   
            switch ($i) {
                case 1:$url = $result->url1; break;
                case 2:$url = $result->url2; break;
                case 3:$url = $result->url3; break;
                case 4:$url = $result->url4; break;
                case 5:$url = $result->url5; break;
            }
            
            if ($url){ if ($result->url_upload == 1){ $url = base_url().'images/customer/'.$url; } }
            
            $image_properties = array('src' => $url, 'alt' => 'Image'.$i, 'class' => 'img_customer', 'width' => '60', 'title' => 'Image'.$i,);
            $this->table->add_row
            (
               $i, 'Image'.$i, !empty($url) ? img($image_properties) : ''
            );
        }

        $data['table'] = $this->table->generate();
        
        $this->load->view('customer_image', $data);
    }
    
    function valid_agent($code){
        if ($this->Agent_model->valid('code',$code) == FALSE){
            $this->form_validation->set_message('valid_agent','Invalid Agent Code..!'); return FALSE;
        }else{ return TRUE; } 
    }
    
    function valid_image($val)
    {
        if ($val == 0)
        {
            if (!$this->input->post('turl')){ $this->form_validation->set_message('valid_image','Image Url Required..!'); return FALSE; }
            else { return TRUE; }            
        }
    }
    
    function add_image($pid)
    {
        if ($this->acl->otentikasi2($this->title) == TRUE){

            $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Customer Manager');
            $data['h2title'] = 'Customer Manager';
            $data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

            // Form validation
            
            $this->form_validation->set_rules('cname', 'Image Attribute', 'required|');
            $this->form_validation->set_rules('userfile', 'Image Value', '');

            if ($this->form_validation->run($this) == TRUE)
            {  
                $result = $this->Agent_model->get_by_id($pid)->row();
                if ($result->url_upload == 1)               
                {
                    switch ($this->input->post('cname')) {
                    case 1:$img = "./images/customer/".$result->url1; break;
                    case 2:$img = "./images/customer/".$result->url2; break;
                    case 3:$img = "./images/customer/".$result->url3; break;
                    case 4:$img = "./images/customer/".$result->url4; break;
                    case 5:$img = "./images/customer/".$result->url5; break;
                  }
                  @unlink("$img"); 
                }
                
                    $config['upload_path'] = './images/customer/';
                    $config['file_name'] = split_space($result->name.'_'.$this->input->post('cname'));
                    $config['allowed_types'] = 'jpg|gif|png';
                    $config['overwrite']  = true;
                    $config['max_size']   = '1000';
                    $config['max_width']  = '30000';
                    $config['max_height'] = '30000';
                    $config['remove_spaces'] = TRUE;

                    $this->load->library('upload', $config);
                    
                    if ( !$this->upload->do_upload("userfile")) // if upload failure
                    {
                        $attr = array('url'.$this->input->post('cname') => null, 'url_upload' => 1);
                    }
                    else {$info = $this->upload->data();
                         $attr = array('url'.$this->input->post('cname') => $info['file_name'], 'url_upload' => 1); 
                    } 
                
                $this->Agent_model->update($pid, $attr);
                $this->session->set_flashdata('message', "One $this->title data successfully saved!");
                
                echo 'true|Data successfully saved..!'; 
            }
            else
            {
    //            echo validation_errors();
                echo 'error|'.validation_errors();
            }
        }
        else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function attribute($pid=null,$category=null)
    {        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        $data['main_view'] = 'article_form';
	$data['form_action'] = site_url($this->title.'/add_attribute/'.$pid);
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $data['attributes'] = $this->attribute->combo($category);  
        $result = $this->attribute_customer->get_list($pid)->result();
        
        // library HTML table untuk membuat template table class zebra
        $tmpl = array('table_open' => '<table id="" class="table table-striped table-bordered">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No','Attribute', 'Value', '#');
        
        $i = 0;
        foreach ($result as $res)
        {
            $this->table->add_row
            (
                ++$i, $this->attribute_list->get_name($res->attribute_id), $res->value,
                anchor('#','<span>delete</span>',array('class'=> 'btn btn-danger btn-sm text-danger', 'id' => $res->id, 'title' => 'delete'))
            );
        }

        $data['table'] = $this->table->generate();
        
        $this->load->view('customer_attribute', $data);
    }
    
    function add_attribute($pid)
    {
        if ($this->acl->otentikasi2($this->title) == TRUE){

            $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Customer Manager');
            $data['h2title'] = 'Customer Manager';
            $data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

            // Form validation
            
            $this->form_validation->set_rules('cattribute', 'Attribute List', 'required|maxlength[100]|callback_valid_attribute['.$pid.']');
            $this->form_validation->set_rules('tvalue', 'Attribute Value', 'required');

            if ($this->form_validation->run($this) == TRUE)
            {  
                $attr = array('customer_id' => $pid, 'attribute_id' => $this->input->post('cattribute'), 'value' => $this->input->post('tvalue'));
                $this->attribute_customer->add($attr);
                $this->session->set_flashdata('message', "One $this->title data successfully saved!");
                
                echo 'true|Data successfully saved..!'; 
            }
            else
            {
    //            echo validation_errors();
                echo 'error|'.validation_errors();
            }
        }
        else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function valid_email($val)
    {
        if ($this->Agent_model->valid('email',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_email','Email registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }

    function validating_email($val)
    {
	$id = $this->session->userdata('langid');
	if ($this->Agent_model->validating('email',$val,$id) == FALSE)
        {
            $this->form_validation->set_message('validating_email', "Email registered!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    // Fungsi update untuk mengupdate db
    function update_process($param=0)
    {
        if ($this->acl->otentikasi_admin($this->title) == TRUE){

        $data['title'] = $this->properti['name'].' | Customeristrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'customer_update';
	$data['form_action'] = site_url($this->title.'/update_process');
	$data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

	// Form validation

        $this->form_validation->set_rules('tcode', 'Agent-Code', 'required');
        $this->form_validation->set_rules('tname', 'Agent Name', 'required');
        $this->form_validation->set_rules('ctype', 'Customer Type', 'required');
        $this->form_validation->set_rules('taddress', 'Address', 'required');
        $this->form_validation->set_rules('tphone1', 'Phone 1', 'required');
        $this->form_validation->set_rules('tphone2', 'Phone 2', '');
        $this->form_validation->set_rules('temail', 'Email', 'required|valid_email|callback_validating_email');
        $this->form_validation->set_rules('twebsite', 'Website', '');
        $this->form_validation->set_rules('ccity', 'City', 'required');
        $this->form_validation->set_rules('cdistrict', 'District', 'required');
        $this->form_validation->set_rules('tzip', 'Zip', '');
            
        if ($this->form_validation->run($this) == TRUE)
        {
            // start update 1
            $config['upload_path'] = './images/agent/';
            $config['file_name'] = split_space($this->input->post('tfname').'_'.waktuindo());
            $config['allowed_types'] = 'jpg|gif|png';
            $config['overwrite'] = true;
            $config['max_size']	= '10000';
            $config['max_width']  = '30000';
            $config['max_height']  = '30000';
            $config['remove_spaces'] = TRUE;

            $this->load->library('upload', $config);

            if ( !$this->upload->do_upload("userfile")) // if upload failure
            {
                $info['file_name'] = null;
                $data['error'] = $this->upload->display_errors();
                
                $customer = array('code' => strtolower($this->input->post('tcode')), 
                                  'name' => strtolower($this->input->post('tname')),
                                  'type' => $this->input->post('ctype'), 'address' => $this->input->post('taddress'),
                                  'phone1' => $this->input->post('tphone1'), 'phone2' => $this->input->post('tphone2'),
                                  'email' => $this->input->post('temail'), 'password' => $this->input->post('tpass'),
                                  'website' => $this->input->post('twebsite'), 'region' => $this->input->post('cdistrict'),
                                  'city' => $this->input->post('ccity'), 'state' => $this->city->get_province_based_city($this->input->post('ccity')),
                                  'zip' => $this->input->post('tzip'), 'groups' => $this->input->post('cgroup'),
                                  'acc_no' => $this->input->post('taccno'), 'acc_name' => $this->input->post('taccname'), 'acc_bank' => $this->input->post('tbank'));

            }
            else
            {
                $info = $this->upload->data();

                $customer = array('code' => strtolower($this->input->post('tcode')), 
                                  'name' => strtolower($this->input->post('tname')),
                                  'type' => $this->input->post('ctype'), 'address' => $this->input->post('taddress'),
                                  'phone1' => $this->input->post('tphone1'), 'phone2' => $this->input->post('tphone2'),
                                  'email' => $this->input->post('temail'),
                                  'website' => $this->input->post('twebsite'), 'region' => $this->input->post('cdistrict'),
                                  'city' => $this->input->post('ccity'), 'state' => $this->city->get_province_based_city($this->input->post('ccity')),
                                  'zip' => $this->input->post('tzip'), 'groups' => $this->input->post('cgroup'),
                                  'acc_no' => $this->input->post('taccno'), 'acc_name' => $this->input->post('taccname'), 'acc_bank' => $this->input->post('tbank'),
                                  'image' => $info['file_name']);
            }

            $this->Agent_model->update($this->session->userdata('langid'), $customer);
            $this->session->set_flashdata('message', "One $this->title has successfully updated!");
            redirect($this->title.'/update/'.$this->session->userdata('langid'));

            // end update 1
        }
        else{ $this->session->set_flashdata('message', validation_errors());
              redirect($this->title.'/update/'.$this->session->userdata('langid'));
            }
        
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function ajaxcombo_district()
    {
        $cityid = $this->input->post('value');
        if ($cityid != null){ $district = $this->disctrict->combo_district_db($cityid);}else{ $district = null; }
        $js = "class='select2_single form-control' id='cdistrict' tabindex='-1' style='width:100%;' "; 
        echo @form_dropdown('cdistrict', $district, isset($default['district']) ? $default['district'] : '', $js);
    }
    
    function random_password() 
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $password = array(); 
        $alpha_length = strlen($alphabet) - 1; 
        for ($i = 0; $i < 8; $i++) 
        {
            $n = rand(0, $alpha_length);
            $password[] = $alphabet[$n];
        }
        return implode($password); 
    }

}

?>
