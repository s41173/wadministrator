<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Material extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Material_model', 'model', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->product = new Product_lib();
        $this->material = new Material_list_lib();
        $this->modellib = new Model_lib();
        $this->color = new Color_lib();
    }

    private $properti, $modul, $title;
    private $product,$material,$modellib,$color;

    function index()
    {
       $this->get_last(); 
    }
    
    public function getdatatable($search=null,$model='null',$material='null',$color='null',$type='null',$groups='null')
    {
        if(!$search){ $result = $this->model->get_last($this->modul['limit'])->result(); }
        else{ $result = $this->model->search($model,$material,$color,$type,$groups)->result(); }
        
        if ($result){
	foreach($result as $res)
	{
           if ($res->glass == 1){ $glass = 'Y'; }else{ $glass = 'N'; } 
	   $output[] = array ($res->id, $res->name, $this->modellib->get_name($res->model), $this->material->get_name($res->material_list), idr_format($res->price),
                              $this->color->get_name($res->color), $res->type, $glass, $res->weight, $res->groups);
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

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'material_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['link'] = array(
                              'material_list' => anchor('material_list','Material List', array('class' => 'btn btn-primary')),
                              'color_list' => anchor('color','Color Type', array('class' => 'btn btn-primary')),
                              'link_back' => anchor('main/','Back', array('class' => 'btn btn-danger'))
                              );
        
        $data['model'] = $this->modellib->combo();
        $data['material'] = $this->material->combo();
        $data['color'] = $this->color->combo();
        
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
        $this->table->set_heading('#','No', 'Model', 'Material List', 'Name', 'Color', 'Type', 'Price', 'Thickness', 'Glass', 'Group', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    function delete_all()
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
           if ( $this->cek_relation($cek[$i]) == TRUE ) 
           {
              $img = $this->Category_model->get_by_id($cek[$i])->row();
              $img = $img->image;
              if ($img){ $img = "./images/category/".$img; unlink("$img"); }

              $this->Category_model->delete($cek[$i]); 
           }
           else { $x=$x+1; }
           
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
      }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }

    function delete($uid,$type='soft')
    {
        if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
        if ($type == 'soft'){
           $this->model->delete($uid);
           $this->session->set_flashdata('message', "1 $this->title successfully removed..!");
           
           echo "true|1 $this->title successfully soft removed..!";
       }
       else
       {
        if ( $this->cek_relation($uid) == TRUE )
        {
           $this->model->force_delete($uid);
           $this->session->set_flashdata('message', "1 $this->title successfully removed..!");  
           echo "true|1 $this->title successfully removed..!";
        }
        else { $this->session->set_flashdata('message', "$this->title related to another component..!"); 
        echo  "invalid|$this->title related to another component..!";} 
       }
       }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }

    private function cek_relation($id)
    {
        $product = $this->product->cek_relation($id, $this->title);
        if ($product == TRUE) { return TRUE; } else { return FALSE; }
    }

    function add_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tname', 'Name', 'required|callback_valid_model');
        $this->form_validation->set_rules('cmodel', 'Model', 'required');
        $this->form_validation->set_rules('cmaterial', 'Material', 'required');
        $this->form_validation->set_rules('tprice', 'Price', 'required|numeric');
        $this->form_validation->set_rules('tweight', 'Weight', 'required|numeric');
        $this->form_validation->set_rules('cgroup', 'Group', 'required|numeric');

        if ($this->form_validation->run($this) == TRUE)
        {
            $model = array('name' => strtoupper($this->input->post('tname')),
                           'model' => $this->input->post('cmodel'),
                           'material_list' => $this->input->post('cmaterial'),
                           'price' => $this->input->post('tprice'),
                           'color' => setnull($this->input->post('ccolor')),
                           'type' => setnull($this->input->post('ctype')),
                           'glass' => $this->input->post('cglass'),
                           'weight' => $this->input->post('tweight'),
                           'groups' => $this->input->post('cgroup'),
                           'created' => date('Y-m-d H:i:s'));

            $this->model->add($model);
            $this->session->set_flashdata('message', "One $this->title data successfully saved!");
            
            echo 'true|'.$this->title.' successfully saved..!|';
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }

    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($uid=null)
    {        
        $category = $this->model->get_by_id($uid)->row();
	$this->session->set_userdata('langid', $category->id);        
        echo $uid.'|'.$category->name.'|'.$category->model.'|'.$category->material_list.'|'.$category->price.'|'.$category->color.'|'.$category->type.'|'.$category->glass.'|'.$category->weight.'|'.$category->groups;
    }

    public function valid_model($name)
    {
        if ($this->model->valid_material($this->input->post('ctype'), $this->input->post('ccolor'), $this->input->post('cmodel'), $this->input->post('cmaterial'), $this->input->post('cgroup')) == FALSE)
        {
            $this->form_validation->set_message('valid_model', "This $this->title is already registered.!");
            return FALSE;
        }
        else{ return TRUE; }
    }

    function validation_model($name)
    {
	$id = $this->session->userdata('langid');
	if ($this->model->validating_material($this->input->post('ctype'), $this->input->post('ccolor'),$this->input->post('cmodel'), $this->input->post('cmaterial'),$this->input->post('cgroup'),$id) == FALSE)
        {
            $this->form_validation->set_message('validation_model', "This $this->title is already registered!");
            return FALSE;
        }
        else { return TRUE; }
    }

    // Fungsi update untuk mengupdate db
    function update_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tname', 'Name', 'required|max_length[100]|callback_validation_model');
        $this->form_validation->set_rules('cmodel', 'Model', 'required');
        $this->form_validation->set_rules('cmaterial', 'Material', 'required');
        $this->form_validation->set_rules('tprice', 'Price', 'required|numeric');
        $this->form_validation->set_rules('tweight', 'Weight', 'required|numeric');
        $this->form_validation->set_rules('cgroup', 'Group', 'required|numeric');

        if ($this->form_validation->run($this) == TRUE)
        {
            $model = array('name' => strtoupper($this->input->post('tname')),
                           'model' => $this->input->post('cmodel'),
                           'material_list' => $this->input->post('cmaterial'),
                           'price' => $this->input->post('tprice'),
                           'color' => setnull($this->input->post('ccolor')),
                           'type ' => setnull($this->input->post('ctype')),
                           'glass' => $this->input->post('cglass'),
                           'weight' => $this->input->post('tweight'),
                           'groups' => $this->input->post('cgroup'),
                           'updated' => date('Y-m-d H:i:s'));
            
	    $this->model->update($this->session->userdata('langid'), $model);
            $this->session->set_flashdata('message', "One $this->title has successfully updated!");
            
            echo "true|One $this->title has successfully updated!";
        }
        else{ echo 'error|'.validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function report_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $data['rundate'] = tglin(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');

        $model = $this->input->post('cmodel');
        $material = $this->input->post('cmaterial');
        
//        Property Details
        $data['company'] = $this->properti['name'];
        $data['reports'] = $this->model->report($model, $material)->result();
        
        if ($this->input->post('ctype') == 0){ $this->load->view('material_report', $data); }else{ $this->load->view('material_pivot', $data); }
    }

}

?>