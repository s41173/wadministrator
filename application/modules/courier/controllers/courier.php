<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Courier extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Courier_model', '', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->role = new Role_lib();
    }

    private $properti, $modul, $title;
    private $role;

    function index()
    {
       $this->get_last(); 
    }
     
    public function getdatatable($search=null,$publish='null')
    {
        if(!$search){ $result = $this->Courier_model->get_last($this->modul['limit'])->result(); }
        else {$result = $this->Courier_model->search($publish)->result(); }
	
        $output = null;
        if ($result){
                
         foreach($result as $res)
	 {   
	   $output[] = array ($res->id, $res->ic, $res->name, $res->phone, $res->address, $res->email, 
                              $res->company, base_url().'images/courier/'.$res->image, $res->status , tglin($res->joined)
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

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Courier Manager');
        $data['h2title'] = 'Courier Manager';
        $data['main_view'] = 'courier_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));

        $data['array'] = array('','');
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
        $this->table->set_heading('#','No', 'Image', 'IC', 'Name', 'Phone', 'Email', 'Company', 'Joined', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    function publish($uid = null)
    {
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
       $val = $this->Courier_model->get_by_id($uid)->row();
       if ($val->status == 0){ $lng = array('status' => 1); }else { $lng = array('status' => 0); }
       $this->Courier_model->update($uid,$lng);
       echo 'true|Status Changed...!';
       }else{ echo "error|Sorry, you do not have the right to change publish status..!"; }
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
             if ($type == 'soft') { $this->Courier_model->delete($cek[$i]); }
             else { $this->remove_img($cek[$i],'force');
                    $this->attribute_courier->force_delete_by_courier($cek[$i]);
                    $this->Courier_model->force_delete($cek[$i]);  }
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
            $this->Courier_model->delete($uid);
            
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
        $this->form_validation->set_rules('tname', 'Name', 'required');
        $this->form_validation->set_rules('tic', 'IC', 'required|callback_valid_ic');
        $this->form_validation->set_rules('temail', 'Contact Email', 'required|valid_email|callback_valid_email');
        $this->form_validation->set_rules('tcompany', 'Company', 'required');
        $this->form_validation->set_rules('taddress', 'Address', 'required');
        $this->form_validation->set_rules('tphone', 'Phone', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $config['upload_path'] = './images/courier/';
            $config['file_name'] = split_space($this->input->post('tname').'_'.$this->input->post('tic'));
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
                $courier = array('name' => strtolower($this->input->post('tname')), 
                                 'ic' => strtolower($this->input->post('tic')),
                                 'company' => $this->input->post('tcompany'), 'address' => $this->input->post('taddress'),
                                 'phone' => $this->input->post('tphone'), 'email' => $this->input->post('temail'),
                                 'joined' => date('Y-m-d H:i:s'),
                                 'image' => null, 'created' => date('Y-m-d H:i:s'));
            }
            else
            {
                $info = $this->upload->data();
                $courier = array('name' => strtolower($this->input->post('tname')), 
                                 'ic' => strtolower($this->input->post('tic')),
                                 'company' => $this->input->post('tcompany'), 'address' => $this->input->post('taddress'),
                                 'phone' => $this->input->post('tphone'), 'email' => $this->input->post('temail'),
                                 'joined' => date('Y-m-d H:i:s'),
                                 'image' => $info['file_name'], 'created' => date('Y-m-d H:i:s'));
            }

            $this->Courier_model->add($courier);
            $this->session->set_flashdata('message', "One $this->title data successfully saved!");
//            redirect($this->title);
            
            if ($this->upload->display_errors()){ echo "warning|".$this->upload->display_errors(); }
            else { echo 'true|'.$this->title.' successfully saved..!|'.base_url().'images/courier/'.$info['file_name']; }
            
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
   

    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($uid=null)
    {        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        $data['main_view'] = 'courier_update';
	$data['form_action'] = site_url($this->title.'/update_process');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));
        $data['source'] = site_url($this->title.'/getdatatable');

        $data['array'] = array('','');
        
        $courier = $this->Courier_model->get_by_id($uid)->row();
	$this->session->set_userdata('langid', $courier->id);
        
        $data['default']['name'] = $courier->name;
        $data['default']['ic'] = $courier->ic;
        $data['default']['company'] = $courier->company;
        $data['address'] = $courier->address;
        $data['default']['phone'] = $courier->phone;
        $data['default']['email'] = $courier->email;
        $data['default']['image'] = base_url().'images/courier/'.$courier->image;

        $this->load->view('template', $data);
    }
    
    function image_gallery($pid=null)
    {        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        $data['main_view'] = 'article_form';
	$data['form_action'] = site_url($this->title.'/add_image/'.$pid);
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $result = $this->Courier_model->get_by_id($pid)->row();
        
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
            
            if ($url){ if ($result->url_upload == 1){ $url = base_url().'images/courier/'.$url; } }
            
            $image_properties = array('src' => $url, 'alt' => 'Image'.$i, 'class' => 'img_courier', 'width' => '60', 'title' => 'Image'.$i,);
            $this->table->add_row
            (
               $i, 'Image'.$i, !empty($url) ? img($image_properties) : ''
            );
        }

        $data['table'] = $this->table->generate();
        
        $this->load->view('courier_image', $data);
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

            $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Courier Manager');
            $data['h2title'] = 'Courier Manager';
            $data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

            // Form validation
            
            $this->form_validation->set_rules('cname', 'Image Attribute', 'required|');
            $this->form_validation->set_rules('userfile', 'Image Value', '');

            if ($this->form_validation->run($this) == TRUE)
            {  
                $result = $this->Courier_model->get_by_id($pid)->row();
                if ($result->url_upload == 1)               
                {
                    switch ($this->input->post('cname')) {
                    case 1:$img = "./images/courier/".$result->url1; break;
                    case 2:$img = "./images/courier/".$result->url2; break;
                    case 3:$img = "./images/courier/".$result->url3; break;
                    case 4:$img = "./images/courier/".$result->url4; break;
                    case 5:$img = "./images/courier/".$result->url5; break;
                  }
                  @unlink("$img"); 
                }
                
                    $config['upload_path'] = './images/courier/';
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
                
                $this->Courier_model->update($pid, $attr);
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
        $result = $this->attribute_courier->get_list($pid)->result();
        
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
        
        $this->load->view('courier_attribute', $data);
    }
    
    function add_attribute($pid)
    {
        if ($this->acl->otentikasi2($this->title) == TRUE){

            $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Courier Manager');
            $data['h2title'] = 'Courier Manager';
            $data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

            // Form validation
            
            $this->form_validation->set_rules('cattribute', 'Attribute List', 'required|maxlength[100]|callback_valid_attribute['.$pid.']');
            $this->form_validation->set_rules('tvalue', 'Attribute Value', 'required');

            if ($this->form_validation->run($this) == TRUE)
            {  
                $attr = array('courier_id' => $pid, 'attribute_id' => $this->input->post('cattribute'), 'value' => $this->input->post('tvalue'));
                $this->attribute_courier->add($attr);
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
    
    function valid_ic($val)
    {
        if ($this->Courier_model->valid('ic',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_ic','IC registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function validating_ic($val)
    {
	$id = $this->session->userdata('langid');
	if ($this->Courier_model->validating('ic',$val,$id) == FALSE)
        {
            $this->form_validation->set_message('validating_ic', "IC registered!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_email($val)
    {
        if ($this->Courier_model->valid('email',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_email','Email registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }

    function validating_email($val)
    {
	$id = $this->session->userdata('langid');
	if ($this->Courier_model->validating('email',$val,$id) == FALSE)
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

        $data['title'] = $this->properti['name'].' | Courieristrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'courier_update';
	$data['form_action'] = site_url($this->title.'/update_process');
	$data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

	// Form validation            
        $this->form_validation->set_rules('tname', 'Name', 'required');
        $this->form_validation->set_rules('tic', 'IC', 'required|callback_validating_ic');
        $this->form_validation->set_rules('temail', 'Contact Email', 'required|valid_email|callback_validating_email');
        $this->form_validation->set_rules('tcompany', 'Company', 'required');
        $this->form_validation->set_rules('taddress', 'Address', 'required');
        $this->form_validation->set_rules('tphone', 'Phone', 'required');
        
        if ($this->form_validation->run($this) == TRUE)
        {
            // start update 1
            $config['upload_path'] = './images/courier/';
            $config['file_name'] = split_space($this->input->post('tname').'_'.$this->input->post('tic'));
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

                $courier = array('name' => strtolower($this->input->post('tname')), 
                                 'ic' => strtolower($this->input->post('tic')),
                                 'company' => $this->input->post('tcompany'), 'address' => $this->input->post('taddress'),
                                 'phone' => $this->input->post('tphone'), 'email' => $this->input->post('temail'));

            }
            else
            {
                $info = $this->upload->data();
                $courier = array('name' => strtolower($this->input->post('tname')), 
                                 'ic' => strtolower($this->input->post('tic')),
                                 'company' => $this->input->post('tcompany'), 'address' => $this->input->post('taddress'),
                                 'phone' => $this->input->post('tphone'), 'email' => $this->input->post('temail'),
                                 'image' => $info['file_name']);
            }

            $this->Courier_model->update($this->session->userdata('langid'), $courier);
            $this->session->set_flashdata('message', "One $this->title has successfully updated!");
            redirect($this->title.'/update/'.$this->session->userdata('langid'));

            // end update 1
        }
        else{ $this->session->set_flashdata('message', validation_errors());
              redirect($this->title.'/update/'.$this->session->userdata('langid'));
            }
        
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function report_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $data['rundate'] = tglin(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');

//        Property Details
        $data['company'] = $this->properti['name'];
        $data['reports'] = $this->Courier_model->report()->result();
        
        if ($this->input->post('ctype') == 0){ $this->load->view('courier_report', $data); }
        else { $this->load->view('courier_pivot', $data); }
    }
    
    function ajaxcombo_district()
    {
        $cityid = $this->input->post('value');
        if ($cityid != null){
            $district = $this->disctrict->combo_district_db($cityid);
            $js = "class='select2_single form-control' id='cdistrict' tabindex='-1' style='width:100%;' "; 
            echo form_dropdown('cdistrict', $district, isset($default['district']) ? $default['district'] : '', $js);
        }
    }
   

}

?>