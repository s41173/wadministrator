<?php

class Project extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Project_model', '', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
    }
    
    private $properti, $modul, $title;
    
    function index()
    { $this->get_last_project(); }
    
    function get_last_project()
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'project_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['link'] = array('link_back' => anchor('main/','<span>back</span>', array('class' => 'back')));

	$uri_segment = 3;
        $offset = $this->uri->segment($uri_segment);
        
        $projects = $this->Project_model->get_last_project($this->modul['limit'], $offset)->result(); // ambil data dari db
        $num_rows = $this->Project_model->count_all_num_rows(); // hitung jumlah baris

        if ($num_rows > 0)
        {
	    $config['base_url'] = site_url($this->title.'/get_last_project');
            $config['total_rows'] = $num_rows;
            $config['per_page'] = $this->modul['limit'];
            $config['uri_segment'] = $uri_segment;
            $this->pagination->initialize($config);
            $data['pagination'] = $this->pagination->create_links(); //array menampilkan link untuk pagination.
            // akhir dari config untuk pagination
	    
            // library HTML table untuk membuat template table class zebra
            $tmpl = array('table_open' => '<table cellpadding="2" cellspacing="1" class="tablemaster table table-bordered">');
            
            $this->table->set_template($tmpl);
            $this->table->set_empty("&nbsp;");
            
            //Set heading untuk table
	    $this->table->set_heading('#','No', 'Name', 'Date', 'Status', 'Action');
            
            $i = 0 + $offset;
            foreach ($projects as $project)
            {
                $datax = array('name'=> 'cek[]','id'=> 'cek'.$i,'value'=> $project->id,'checked'=> FALSE, 'style'=> 'margin:0px');
                $this->table->add_row
                (
                    form_checkbox($datax), ++$i, $project->name, tgleng($project->dates), $this->stts($project->status),
		    anchor($this->title.'/update/'.$project->id,'<span>update</span>',array('class' => 'edit', 'title' => '')).' '.
                    anchor($this->title.'/delete/'.$project->id,'<span>delete</span>',array('class'=> 'delete', 'title' => 'delete' ,'onclick'=>"return confirm('Are you sure you will delete this data?')"))
                );
            }
            
            // table di generate 
            $data['table'] = $this->table->generate();
        }
        else{ $data['message'] = "Not found any $this->title of data!"; }
	
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    private function stts($val){ if ($val == 0){ return 'N'; }else{ return 'Y'; } }
    
    function delete($uid)
    {
        $this->acl->otentikasi3($this->title);
        
        $img = $this->Project_model->get_project_by_id($uid)->row();
        $img = $img->image;
        if ($img){ $img = "./images/project/".$img; unlink("$img"); }
        
        $this->Project_model->delete($uid); // memanggil model untuk mendelete data
        $this->session->set_flashdata('message', "1 $this->title successfully removed..!!"); // set flash data message dengan session
        redirect($this->title);
    }
    
    function add_process()
    {
        $this->acl->otentikasi2($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'project_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['link'] = array('link_back' => anchor($this->title,'<span>back</span>', array('class' => 'back')));
         
	$this->form_validation->set_rules('tname', 'Category Name', 'required|callback_valid_name');
        $this->form_validation->set_rules('tdate', 'Project Date', 'required');
        $this->form_validation->set_rules('tlocation', 'Location', 'required');
        $this->form_validation->set_rules('turl1', 'Url1', 'required');
        $this->form_validation->set_rules('rpublish', 'Status', 'required');
        
        if ($this->form_validation->run($this) == TRUE)
        {
            $config['upload_path'] = './images/project/';
            $config['file_name'] = $this->input->post('tname');
            $config['allowed_types'] = 'gif|jpg|png';
            $config['overwrite'] = true;
            $config['max_size']	= '1000';
            $config['max_width']  = '2000';
            $config['max_height']  = '2000';
            $config['remove_spaces'] = TRUE;

            $this->load->library('upload', $config);
            
            if ( !$this->upload->do_upload("userfile")) // if upload failure
            {
                $data['error'] = $this->upload->display_errors();
                
                $project = array('name' => $this->input->post('tname'), 'dates' => $this->input->post('tdate'), 'desc' => $this->input->post('tlocation'),
                                 'image' => null,'url' => $this->input->post('turl1'), 'status' => $this->input->post('rpublish'));
            }
            else
            {
                $info = $this->upload->data();
                $project = array('name' => $this->input->post('tname'), 'dates' => $this->input->post('tdate'), 'desc' => $this->input->post('tlocation'),
                                 'image' => $info['file_name'],'url' => $this->input->post('turl1'), 'status' => $this->input->post('rpublish'));
            }

            $this->Project_model->add($project);
            $this->session->set_flashdata('message', "One data $this->title successfully saved!");
            redirect($this->title);
//            echo "true";
        }
        else
        {
            $this->load->view('template', $data);
//            echo validation_errors();
        }
    }

    function valid_name($name)
    {
        if ($this->Project_model->valid_name($name) == FALSE)
        {
            $this->form_validation->set_message('valid_name', $this->title.' name registered');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    function validating_name($name)
    {
	$id = $this->session->userdata('cnid');
	if ($this->Project_model->validating_name($name,$id) == FALSE)
        {
            $this->form_validation->set_message('validating_name', "This $this->title name is already registered!");
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($uid)
    {
        $this->acl->otentikasi2($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'project_view';
	$data['form_action'] = site_url($this->title.'/update_process/'.$uid);
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['link'] = array('link_back' => anchor($this->title,'<span>back</span>', array('class' => 'back')));
        
        $project = $this->Project_model->get_project_by_id($uid)->row();
        
	$data['default']['name']     = $project->name;
        $data['default']['dates']    = $project->dates;
        $data['default']['location'] = $project->desc;
        $data['default']['url1']     = $project->url;
        $data['default']['publish']     = $project->status;
        $data['default']['image']    = base_url()."images/project/".$project->image;

//	$this->session->set_userdata('cnid', $project->id);
    
       $this->load->view('template', $data);
    }
    
    // Fungsi update untuk mengupdate db
    function update_process($uid)
    {
        $this->acl->otentikasi2($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'project_view';
	$data['form_action'] = site_url($this->title.'/update_process/'.$uid);
        $data['link'] = array('link_back' => anchor($this->title,'<span>back</span>', array('class' => 'back')));
        
	$this->form_validation->set_rules('tname', 'Name', 'required|callback_validating_name');
        $this->form_validation->set_rules('tdate', 'Project Date', 'required');
        $this->form_validation->set_rules('tlocation', 'Desc', 'required');
        $this->form_validation->set_rules('turl1', 'Url', 'required');
        $this->form_validation->set_rules('rpublish', 'Status', 'required');
        
        if ($this->form_validation->run() == TRUE)
        {   
            $config['upload_path'] = './images/project/';
            $config['file_name'] = $this->input->post('tname');
            $config['allowed_types'] = 'gif|jpg|png';
            $config['overwrite'] = true;
            $config['max_size']	= '1000';
            $config['max_width']  = '2000';
            $config['max_height']  = '2000';
            $config['remove_spaces'] = TRUE;

            $this->load->library('upload', $config);
            
            if (!$this->upload->do_upload("userfile")) // if upload failure
            {
                $data['error'] = $this->upload->display_errors();
                
                $project = array('name' => $this->input->post('tname'), 'dates' => $this->input->post('tdate'), 'desc' => $this->input->post('tlocation'),
                                 'url' => $this->input->post('turl1'), 'status' => $this->input->post('rpublish'));
            }
            else
            {
                $info = $this->upload->data();
                $project = array('name' => $this->input->post('tname'), 'dates' => $this->input->post('tdate'), 'desc' => $this->input->post('tlocation'),
                                 'image' => $info['file_name'],'url' => $this->input->post('turl1'), 'status' => $this->input->post('rpublish'));
            }

            $this->Project_model->update($uid, $project);
            $this->session->set_flashdata('message', "One $this->title of data successfully updated!");

            redirect($this->title.'/update/'.$uid);
//            echo "true";
        }
        else
        {
           $this->load->view('template', $data);
//            echo validation_errors();
        }
    }
    
}

?>