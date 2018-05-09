<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Product extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Product_model', '', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->role = new Role_lib();
        $this->category = new Categoryproduct_lib();
        $this->attribute = new Attribute_lib();
        $this->attribute_product = new Attribute_product_lib();
        $this->attribute_list = new Attribute_list_lib();
        $this->product = new Product_lib();
        $this->model = new Model_lib();
        $this->color = new Color_lib();
        $this->materiallist = new Material_list_lib();
        $this->material = new Material_lib();
    }

    private $properti, $modul, $title, $product, $color, $materiallist, $material;
    private $role, $category, $model, $attribute, $attribute_product, $attribute_list, $currency;

    function index()
    {
       $this->session->unset_userdata('start'); 
       $this->session->unset_userdata('end');
       $this->session->unset_userdata('langid');
       $this->get_last(); 
    }
    
    // ============ ajax ==========================
    
    function get_pro_name($pid){
        echo strtoupper($this->product->get_name($pid)).' | '. $this->model->get_name($this->product->get_model($pid));
    }
    
    function get_glass($type=null){
        $result = $this->material->combo_glass($type);
        $js = "class='form-control' id='cglass' tabindex='-1' style='width:100%;' "; 
        echo form_dropdown('cglass', $result, isset($default['glass']) ? $default['glass'] : '', $js);
    }
    
    function get_color($pid=null){
        $res = $this->Product_model->get_by_id($pid)->row();
        $color = explode(',', $res->color);
        $data=null;
        
        for($i=0; $i<count($color); $i++){ $data['options'][$color[$i]] = strtoupper($this->color->get_name($color[$i]));}
        $js = "class='form-control' id='ccolor' tabindex='-1' style='width:100%;' "; 
        echo form_dropdown('ccolor', $data, isset($default['color']) ? $default['color'] : '', $js);
    }
    
    function calculator(){
       
        $pid = $this->input->post('tid');
        $width = $this->input->post('twidth');
        $height = $this->input->post('theight');
        $heightkm = $this->input->post('theightkm');
        $heightkm1 = $this->input->post('theightkm1');
        $color = setnull($this->input->post('ccolor'));
        $type = $this->input->post('ctype');
        $kusen = $this->input->post('ckusen');
        $glass = $this->input->post('cglass');
        
        $material = new Material_lib();
        $formula = new Formula_lib();
        $assembly = new Assembly_lib();
        
        $matlist = $assembly->get_details($pid)->result();
        $total = 0;
        $i=1;
        $datax = "";
        
        foreach ($matlist as $res){
            
            $nama = $this->materiallist->get_name($res->material);
            $harga = $material->get_price($pid, $res->material, $color, $type, $glass);
            $size = $formula->calculate($this->model->get_name($this->product->get_model($pid)),$nama, $width, $height, $pid, $heightkm, $heightkm1, $kusen);
            $brutto = round(floatval($size*$harga));
            
            $total = $total+$brutto;
            $datax = $datax." <tr> <td>".$i."</td> <td> ".$nama." </td> <td> ".$size." </td> <td> ". idr_format($brutto)." </td> </tr>";
            $i++;
//            echo "Nama : ". $nama.'<br> Ukuran : '.$size.'<br> Harga Unit : '.idr_format($harga).'<br> Harga : '. idr_format($brutto).'<hr>'; 
            
        }
        
        $total = round(floatval(1.1*$total));
//        echo 'Total : <b> '.idr_format($total).'</b>';
        echo $datax.'|'.idr_format($total);
    }
    
    function hitung($pid=0,$width=0,$height=0,$heightkm=0,$heightkm1=0,$color=3,$type='DOUBLE',$kusen="KUSEN"){
        
        $material = new Material_lib();
        $formula = new Formula_lib();
        $assembly = new Assembly_lib();
        
        $matlist = $assembly->get_details($pid)->result();
        $total = 0;
        $i=1;
        
        foreach ($matlist as $res){
            
            $nama = $this->materiallist->get_name($res->material);
            $harga = $material->get_price($pid, $res->material, $color, $type);
            $size = $formula->calculate($this->model->get_name($this->product->get_model($pid)),$nama, $width, $height, $pid, $heightkm, $heightkm1, $kusen);
            $brutto = round(floatval($size*$harga));
            
            $total = $total+$brutto;
            echo "Nama : ". $nama.'<br> Ukuran : '.$size.'<br> Harga Unit : '.idr_format($harga).'<br> Harga : '. idr_format($brutto).'<hr>'; 
        }
        
        $total = round(floatval(1.1*$total));
        echo 'Total : <b> '.idr_format($total).'</b>';
    }
     
    public function getdatatable($search=null,$cat='null',$model='null',$publish='null')
    {
        if(!$search){ $result = $this->Product_model->get_last($this->modul['limit'])->result(); }
        else {$result = $this->Product_model->search($cat,$model,$publish)->result(); }
	
        $output = null;
        if ($result){
                
         foreach($result as $res)
	 {   
	   $output[] = array ($res->id, $res->sku, $this->category->get_name($res->category), base_url().'images/product/'.$res->image,
                              strtoupper($res->name), $this->model->get_name($res->model), $res->publish
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

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Product Manager');
        $data['h2title'] = 'Product Manager';
        $data['main_view'] = 'product_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['form_action_assembly'] = site_url($this->title.'/assembly_process');
        $data['form_action_import'] = site_url($this->title.'/import');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));

        $data['category'] = $this->category->combo();
        $data['model'] = $this->model->combo();
        $data['color'] = $this->color->combo();
        $data['material'] = $this->materiallist->combo();
        $data['array'] = array('','');

        // library HTML table untuk membuat template table class zebra
        $tmpl = array('table_open' => '<table id="datatable-buttons" class="table table-striped table-bordered">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('#','No', 'Image', 'Category', 'SKU', 'Name', 'Series', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['graph'] = site_url()."/product/chart/";
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    function chart()
    {
        $data = $this->category->get();
        $datax = array();
        foreach ($data as $res) 
        {  
           $point = array("label" => $res->name , "y" => $this->product->get_product_based_category($res->id));
           array_push($datax, $point);      
        }
        echo json_encode($datax, JSON_NUMERIC_CHECK);
    }
    
    function publish($uid = null)
    {
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
       $val = $this->Product_model->get_by_id($uid)->row();
       if ($val->publish == 0){ $lng = array('publish' => 1); }else { $lng = array('publish' => 0); }
       $this->Product_model->update($uid,$lng);
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
             if ($type == 'soft') { $this->Product_model->delete($cek[$i]); }
             else { $this->remove_img($cek[$i],'force');
                    $this->attribute_product->force_delete_by_product($cek[$i]);
                    $this->Product_model->force_delete($cek[$i]);  }
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
            $this->Product_model->delete($uid);
            
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
        $this->form_validation->set_rules('tsku', 'SKU', 'required|callback_valid_sku');
        $this->form_validation->set_rules('tname', 'Name', 'required|callback_valid_name');
        $this->form_validation->set_rules('cmodel', 'Model', 'required');
        $this->form_validation->set_rules('ccategory', 'Category', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $config['upload_path'] = './images/product/';
            $config['file_name'] = split_space($this->input->post('tname'));
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
                $product = array('name' => strtolower($this->input->post('tname')),
                                 'sku' => $this->input->post('tsku'), 'model' => $this->input->post('cmodel'), 
                                 'category' => $this->input->post('ccategory'),
                                 'image' => null, 'created' => date('Y-m-d H:i:s'));
            }
            else
            {
                $info = $this->upload->data();
                
                $product = array('name' => strtolower($this->input->post('tname')),
                                 'sku' => $this->input->post('tsku'), 'model' => $this->input->post('cmodel'), 
                                 'category' => $this->input->post('ccategory'),
                                 'image' => $info['file_name'], 'created' => date('Y-m-d H:i:s'));
            }

            $this->Product_model->add($product);
            $this->session->set_flashdata('message', "One $this->title data successfully saved!");
//            redirect($this->title);
            
            if ($this->upload->display_errors()){ echo "warning|".$this->upload->display_errors(); }
            else { echo 'true|'.$this->title.' successfully saved..!|'.base_url().'images/product/'.$info['file_name']; }
            
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
    
    function remove_img($id,$type='primary')
    {
        $img = $this->Product_model->get_by_id($id)->row();
        
        if ($type == 'primary'){
            $img = $img->image;
            if ($img){ $img = "./images/product/".$img; @unlink("$img"); }
        }else{
            $image = "./images/product/".$img->image; @unlink("$image");
            $img1 = "./images/product/".$img->url1; @unlink("$img1"); 
            $img2 = "./images/product/".$img->url2; @unlink("$img2");
            $img3 = "./images/product/".$img->url3; @unlink("$img3");
            $img4 = "./images/product/".$img->url4; @unlink("$img4");
            $img5 = "./images/product/".$img->url5; @unlink("$img5");
        }
    }
    
    function assembly($uid){
        
        $assembly = new Assembly_lib();
        $result = $assembly->get_details($uid)->result();
        $hasil = array();
        $i=0;
        foreach ($result as $value) { 
            $hasil[$i] = $value->material; 
            $i++;
        }
        
        echo $uid.'|'.$this->product->get_name($uid).'|'.implode(',', $hasil);
    }
    
    function assembly_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){
        $assembly = new Assembly_lib();
        $this->form_validation->set_rules('cmaterial', 'Material', 'required');
        
        if ($this->form_validation->run($this) == TRUE){
            
           $assembly->cleaning($this->input->post('tid')); 
           foreach ($this->input->post('cmaterial') as $value) {
               $assembly->create($this->input->post('tid'),$value);
           }
           echo "true|Material items already added..!!";
            
        }else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }

    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($uid=null)
    {        
        $this->session->unset_userdata('langid');
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        $data['main_view'] = 'product_update';
	$data['form_action'] = site_url($this->title.'/update_process');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $data['model'] = $this->model->combo();
        $data['category'] = $this->category->combo();
        $data['color'] = $this->color->combo();
        
        
        $data['source'] = site_url($this->title.'/getdatatable');
        
        $product = $this->Product_model->get_by_id($uid)->row();
	$this->session->set_userdata('langid', $product->id);
        
        $data['array'] = explode(',', $product->color);
        $data['default']['sku'] = $product->sku;
        $data['default']['category'] = $product->category;
        $data['default']['name'] = $product->name;
        $data['default']['model'] = $product->model;
        $data['default']['description'] = $product->description;
        $data['default']['flat'] = $product->flat_price;
        $data['default']['bone'] = $product->bone;
        $data['default']['sash'] = $product->daun;
        $data['default']['activesash'] = $product->daunhidup;
        $data['default']['fixedglass'] = $product->kacamati;
        $data['default']['fixedglassbottom'] = $product->kacamati_bawah;
        $data['default']['tulangdaun'] = $product->tulang_daun;
        $data['default']['panel']      = $product->panel;
        $data['default']['weight']     = $product->weight;
        $data['default']['image']      = base_url().'images/product/'.$product->image;
         
        $this->load->helper('editor');
        editor();
        $this->load->view('template', $data);
    }
    
    function image_gallery($pid=null)
    {        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        $data['main_view'] = 'article_form';
	$data['form_action'] = site_url($this->title.'/add_image/'.$pid);
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $result = $this->Product_model->get_by_id($pid)->row();
        
        // library HTML table untuk membuat template table class zebra
        $tmpl = array('table_open' => '<table id="" class="table table-striped table-bordered">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No', 'Name', 'Image');
        
        for ($i=1; $i<=6; $i++)
        {   
            switch ($i) {
                case 1:$url = $result->url1; break;
                case 2:$url = $result->url2; break;
                case 3:$url = $result->url3; break;
                case 4:$url = $result->url4; break;
                case 5:$url = $result->url5; break;
                case 6:$url = $result->url6; break;
            }
            
            if ($url){ $url = base_url().'images/product/'.$url; }
            
            $image_properties = array('src' => $url, 'alt' => 'Image'.$i, 'class' => 'img_product', 'width' => '60', 'title' => 'Image'.$i,);
            $this->table->add_row
            (
               $i, 'Image'.$i, !empty($url) ? img($image_properties) : ''
            );
        }

        $data['table'] = $this->table->generate();
        
        $this->load->view('product_image', $data);
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

            $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Product Manager');
            $data['h2title'] = 'Product Manager';
            $data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

            // Form validation
            
            $this->form_validation->set_rules('cname', 'Image Attribute', 'required|');
            $this->form_validation->set_rules('userfile', 'Image Value', '');

            if ($this->form_validation->run($this) == TRUE)
            {  
                $result = $this->Product_model->get_by_id($pid)->row();
                
                switch ($this->input->post('cname')) {
                case 1:$img = "./images/product/".$result->url1; break;
                case 2:$img = "./images/product/".$result->url2; break;
                case 3:$img = "./images/product/".$result->url3; break;
                case 4:$img = "./images/product/".$result->url4; break;
                case 5:$img = "./images/product/".$result->url5; break;
                case 6:$img = "./images/product/".$result->url6; break;
               }
               @unlink("$img"); 
                
                
                    $config['upload_path'] = './images/product/';
                    $config['file_name'] = split_space($result->name.'_'.$this->input->post('cname'));
                    $config['allowed_types'] = 'jpg|gif|png|jpeg';
                    $config['overwrite']  = true;
                    $config['max_size']   = '50000';
                    $config['max_width']  = '30000';
                    $config['max_height'] = '30000';
                    $config['remove_spaces'] = TRUE;

                    $this->load->library('upload', $config);
                    
                    if ( !$this->upload->do_upload("userfile")) // if upload failure
                    {
                        $attr = array('url'.$this->input->post('cname') => null);
                    }
                    else {$info = $this->upload->data();
                         $attr = array('url'.$this->input->post('cname') => $info['file_name']); 
                    } 
                
                $this->Product_model->update($pid, $attr);
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

    function valid_role($val)
    {
        if(!$val)
        {
          $this->form_validation->set_message('valid_role', "role type required.");
          return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_sku($val)
    {
        if ($this->Product_model->valid('sku',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_sku','SKU registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }

    function validating_sku($val)
    {
	$id = $this->session->userdata('langid');
	if ($this->Product_model->validating('sku',$val,$id) == FALSE)
        {
            $this->form_validation->set_message('validating_sku', "SKU registered!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_name($val)
    {
        if ($this->Product_model->valid('name',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_name','Name registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }

    function validating_name($val)
    {
	$id = $this->session->userdata('langid');
	if ($this->Product_model->validating('name',$val,$id) == FALSE)
        {
            $this->form_validation->set_message('validating_name', "Name registered!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_model($val)
    {
        if ($this->Product_model->valid('model',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_model','Model registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }

    function validating_model($val)
    {
	$id = $this->session->userdata('langid');
	if ($this->Product_model->validating('model',$val,$id) == FALSE)
        {
            $this->form_validation->set_message('validating_model', "Model registered!");
            return FALSE;
        }
        else{ return TRUE; }
    }

    // Fungsi update untuk mengupdate db
    function update_process($param=0)
    {
        if ($this->acl->otentikasi_admin($this->title) == TRUE){

        $data['title'] = $this->properti['name'].' | Productistrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'product_update';
	$data['form_action'] = site_url($this->title.'/update_process');
	$data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

	// Form validation
        if ($param == 1)
        {
            $this->form_validation->set_rules('tsku', 'SKU', 'required|callback_validating_sku');
            $this->form_validation->set_rules('ccategory', 'Category', 'required');
            $this->form_validation->set_rules('tname', 'Product Name', 'required|callback_validating_name');
            $this->form_validation->set_rules('cmodel', 'Product Model', 'required');
            $this->form_validation->set_rules('tdesc', 'Description', '');
            $this->form_validation->set_rules('ccolor', 'Color', '');
            $this->form_validation->set_rules('cflat', 'Color', '');
            $this->form_validation->set_rules('tbone', 'Bone', 'required|numeric');
            $this->form_validation->set_rules('tsash', 'Sash', 'required|numeric');
            $this->form_validation->set_rules('tactivesash', 'Active Sash', 'required|numeric');
            $this->form_validation->set_rules('tkacamati', 'Fixed Glass', 'required|numeric');
            $this->form_validation->set_rules('tkacamatibawah', 'Fixed Glass Bottom', 'required|numeric');
            $this->form_validation->set_rules('ttulangdaun', 'Sash Bone', 'required|numeric');
            $this->form_validation->set_rules('tpanel', 'Panel', 'required|numeric');
            $this->form_validation->set_rules('tweight', 'Weight', 'required|numeric');
            
            if ($this->form_validation->run($this) == TRUE)
            {
                // start update 1
                $config['upload_path'] = './images/product/';
                $config['file_name'] = split_space($this->input->post('tname'));
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
                    $product = array('name' => strtolower($this->input->post('tname')), 'kacamati' => $this->input->post('tkacamati'),
                                     'kacamati_bawah' => $this->input->post('tkacamatibawah'),
                                     'daun' => $this->input->post('tsash'), 'daunhidup' => $this->input->post('tactivesash'),
                                     'tulang_daun' => $this->input->post('ttulangdaun'), 'panel' => $this->input->post('tpanel'),
                                     'sku' => $this->input->post('tsku'), 'model' => $this->input->post('cmodel'), 'bone' => $this->input->post('tbone'), 
                                     'color' => $this->split_array($this->input->post('ccolor')), 'flat_price' => $this->input->post('cflat'), 'weight' => $this->input->post('tweight'),
                                     'category' => $this->input->post('ccategory'), 'description' => $this->input->post('tdesc'));
                }
                else
                {
                    $info = $this->upload->data();

                    $product = array('name' => strtolower($this->input->post('tname')), 'kacamati' => $this->input->post('tkacamati'),
                                     'kacamati_bawah' => $this->input->post('tkacamatibawah'),
                                     'daun' => $this->input->post('tsash'), 'daunhidup' => $this->input->post('tactivesash'),
                                     'tulang_daun' => $this->input->post('ttulangdaun'), 'panel' => $this->input->post('tpanel'),
                                     'color' => $this->split_array($this->input->post('ccolor')),
                                     'sku' => $this->input->post('tsku'), 'model' => $this->input->post('cmodel'), 'weight' => $this->input->post('tweight'),
                                     'flat_price' => $this->input->post('cflat'), 'bone' => $this->input->post('tbone'), 
                                     'category' => $this->input->post('ccategory'), 'description' => $this->input->post('tdesc'),
                                     'image' => $info['file_name']);
                }
                
                $this->Product_model->update($this->session->userdata('langid'), $product);
                $this->session->set_flashdata('message', "One $this->title has successfully updated!");
                redirect($this->title.'/update/'.$this->session->userdata('langid'));
                
                // end update 1
            }
            else{ $this->session->set_flashdata('message', validation_errors());
                  redirect($this->title.'/update/'.$this->session->userdata('langid'));
                }
        }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
        
    function report_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $data['rundate'] = tglin(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');
        $data['category'] = $this->category->get_name($this->input->post('ccategory'));
        $data['model'] = $this->model->get_name($this->input->post('cmodel'));

//        Property Details
        $data['company'] = $this->properti['name'];
        $data['reports'] = $this->Product_model->report($this->input->post('ccategory'), $this->input->post('cmodel'))->result();
        
        if ($this->input->post('ctype') == 0){ $this->load->view('product_report', $data); }
        else { $this->load->view('product_pivot', $data); }
    }
   

}

?>