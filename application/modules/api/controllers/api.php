<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends MX_Controller {


   public function __construct()
   {
        parent::__construct();

        $this->load->helper('date');
        $this->log = new Log_lib();
        $this->load->library('email');
        $this->login = new Login_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('login');

        $this->properti = $this->property->get();   
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token'); 

        // Your own constructor code
   }

   private $date,$time,$log,$login;
   private $properti,$com;

   function index()
   {
       redirect('login');
   }
   
   public function get_type(){
       
      $datas = (array)json_decode(file_get_contents('php://input')); 
      $series = $datas['series'];
      
      $lib = new Material_lib();
      $result = $lib->get_type($series)->result();
      
      if ($result){
	foreach($result as $res)
	{
	   $output[] = array ("type" => $res->type);
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


   public function category(){
        
        $datas = (array)json_decode(file_get_contents('php://input'));
        $parent = $datas['id'];
        
        $lib = new Categoryproduct_lib();
        $result = $lib->get_based_parent($parent);
        
        if ($result){
	foreach($result as $res)
	{
	   $output[] = array ("id" => $res->id, "name" => $res->name, "permalink" => $res->permalink, "image" => base_url().'images/category/'.$res->image);
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

   public function category_detail(){
        
        $datas = (array)json_decode(file_get_contents('php://input'));
        $parent = $datas['id'];
        
        $lib = new Categoryproduct_lib();
        $res = $lib->get_by_id($parent)->row();
        
        if ($res){
	$output[] = array ("id" => $res->id, "name" => $res->name, "permalink" => $res->permalink, "image" => base_url().'images/category/'.$res->image);
        
        $response['content'] = $output;
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit; 
        }
    }
    
    public function series(){
        
        $datas = (array)json_decode(file_get_contents('php://input'));
        $category = $datas['category'];
        
        $lib = new Product_lib();
        $model = new Model_lib();
        $result = $lib->get_series_based_cat($category);
        
        foreach ($result as $value) { $output[] = array ("id" => $value->model, "name" => $model->get_name($value->model)); }
        $response['content'] = $output;
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit; 
    }
    
    public function product(){
        
        $datas = (array)json_decode(file_get_contents('php://input'));
        $cat   = $datas['category'];
        $model = $datas['model'];
        
        $lib = new Product_lib();
        $result = $lib->get_poduct_based_cat_model($cat,$model);
        
        foreach($result as $res){
            $output[] = array ("id" => $res->id, "sku" => $res->sku, "name" => $res->name, "image" => base_url().'images/product/'.$res->image);
        }
        $response['content'] = $output;
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit; 
    }
    
    public function color(){
        
        $datas = (array)json_decode(file_get_contents('php://input'));
        
        $pid = $datas['pid'];
        
        $lib = new Product_lib();
        $colorlib = new Color_lib();
        $res = $lib->get_detail_based_id($pid);
        $color = explode(',', $res->color);
        
        for($i=0; $i<count($color); $i++){
            $output[] = array ("id" => $color[$i], "name" => strtoupper($colorlib->get_name($color[$i])));
        }
        
        $response['content'] = $output;
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit; 
    }
    
     public function product_detail(){
        
        $datas = (array)json_decode(file_get_contents('php://input'));
        $pid   = $datas['pid'];
        
        $lib = new Product_lib();
        $cat = new Categoryproduct_lib();
        $model = new Model_lib();
        
        $res = $lib->get_detail_based_id($pid);
        $url1 = null; $url2 = null; $url3 = null; $url4 = null; $url5 = null; $url6 = null;
        if ($res->url1){ $url1 = base_url().'images/product/'.$res->url1; }
        if ($res->url2){ $url2 = base_url().'images/product/'.$res->url2; }
        if ($res->url3){ $url3 = base_url().'images/product/'.$res->url3; }
        if ($res->url4){ $url4 = base_url().'images/product/'.$res->url4; }
        if ($res->url5){ $url5 = base_url().'images/product/'.$res->url5; }
        if ($res->url6){ $url6 = base_url().'images/product/'.$res->url6; }
        
        $output[] = array ("id" => $res->id, "sku" => $res->sku, "name" => $res->name,
                           "category" => $cat->get_name($res->category), "model" => $model->get_name($res->model), 
                           "model_id" => $res->model,
                           "image" => base_url().'images/product/'.$res->image,  
                           "url1" => $url1, "url2" => $url2, "url3" => $url3, "url4" => $url4, 
                           "url5" => $url5, "url6" => $url6 
                          );
         
        $response['content'] = $output;
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit; 
    }
    
    public function calculator(){
       
        $datas = (array)json_decode(file_get_contents('php://input'));
        
        $pid = $datas['pid'];
        $width = $datas['width'];
        $height = $datas['height'];
        $heightkm = $datas['heightkmtop'];
        $heightkm1 = $datas['heightkmbot'];
        $color = $datas['color'];
        $type = $datas['type'];
        $kusen = $datas['kusen'];
        $glass = $datas['glass'];
//        $group = $datas['group'];
        
        if (isset($datas['group'])){ $group = $datas['group']; }else{ $group = 1; }
        
        $material = new Material_lib();
        $formula = new Formula_lib();
        $assembly = new Assembly_lib();
        $materiallist = new Material_list_lib();
        $model = new Model_lib();
        $product = new Product_lib();
        
        $matlist = $assembly->get_details($pid)->result();
        $total = 0;
        $i=1;
        $datax = "";
        
        foreach ($matlist as $res){
            
            $nama = $materiallist->get_name($res->material);
            $harga = $material->get_price($pid, $res->material, $color, $type, $glass, $group);
            $size = $formula->calculate($model->get_name($product->get_model($pid)),$nama, $width, $height, $pid, $heightkm, $heightkm1, $kusen);
            $brutto = round(floatval($size*$harga));
            
            $total = $total+$brutto;
            $output[] = array ("no" => $i, "name" => $nama, "size" => $size, "amount" => idr_format($brutto));
            $i++;
        }
        
         $response['content'] = $output;
         $response['total'] = idr_format(round(floatval(1.1*$total)));
         $response['total_unformat'] = round(floatval(1.1*$total));
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit;  
    }
    
    function get_color($pid=null, $action='hide_button_cart'){
        
        $lib = new Product_lib();
        $colorlib = new Color_lib();
        $res = $lib->get_detail_based_id($pid);
        $color = explode(',', $res->color);
        $data=null;
        
        for($i=0; $i<count($color); $i++){ $data['options'][$color[$i]] = strtoupper($colorlib->get_name($color[$i]));}
        $js = "class='form-control' id='ccolor' onChange='".$action."();' tabindex='-1' style='width:100%;' "; 
        echo form_dropdown('ccolor', $data, isset($default['color']) ? $default['color'] : '', $js);
    }
    
    function get_glass($type=null,$class='form-control'){
        
        $material = new Material_lib();
        $result = $material->combo_glass($type);
        $js = "class='".$class."' id='cglass' tabindex='-1' style='width:100%;' "; 
        echo form_dropdown('cglass', $result, isset($default['glass']) ? $default['glass'] : '', $js);
    }
    
    function get_type_combo($model=null,$class='form-control'){
        
        $material = new Material_lib();
        $result = $material->combo_type($model);
        $js = "class='".$class."' id='cglasstype' tabindex='-1' style='width:100%;' onchange='get_glass();' "; 
        echo form_dropdown('cglass', $result, isset($default['glasstype']) ? $default['glasstype'] : '', $js);
    }
    
    function contact(){
        
       $datax = (array)json_decode(file_get_contents('php://input')); 
       
       $this->load->library('email');
       $this->email->from($datax['email'], $datax['name']);
       $this->email->to($this->properti['email']);  
       $this->email->subject('Contact Message : '.$datax['name']);
       $this->email->message($datax['message']);	

       if ($this->email->send()){ $stts = 'true'; }else{ $stts = (string)$this->email->print_debugger(); }

       $response = array('status' => $stts);
       $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response, 128))
        ->_display();
        exit;
   }
   
   // api  untuk keperluan shipping rate
    public function get_city(){
        
        $this->db->order_by('city', 'asc'); 
        $result = $this->db->get('shiprate')->result();
        
        foreach($result as $res){
            $output[] = array ("city_id" => $res->cityid, "city" => $res->city);
        }
        $response['content'] = $output;
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit; 
    }
    
    public function get_district(){
        
        $datas = (array)json_decode(file_get_contents('php://input'));
        $cityid = $datas['city'];
        
        if ($cityid){
            
            $this->db->where('cityid', $cityid);
            $this->db->order_by('district', 'asc'); 
            $result = $this->db->get('shiprate')->result();

            foreach($result as $res){
                $output[] = array ("city_id" => $res->cityid, "city" => $res->city, "district" => $res->district);
            }
            $status = array('status' => true);
            
        }else{ $status = array('status' => false); }
        
        $response['status'] = $status;
        $response['content'] = $output;

            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit; 
    }
    
     public function get_shipcost(){
        
        $datas = (array)json_decode(file_get_contents('php://input'));
        $source = $datas['source'];
        $cityid = $datas['city'];
        $district = $datas['district'];
        $type = $datas['type'];
        $kurir = $datas['courier'];
        
        if ($source != null && $cityid != null && $district != null && $type != null && $kurir != null){
            
            $this->db->where('source', $source);
            $this->db->where('courier', $kurir);
            $this->db->where('cityid', $cityid);
            $this->db->where('district', $district);
            $this->db->where('type', $type);
            
            $result = $this->db->get('shiprate')->row();
            $response['rate'] = intval($result->rate);
            
        }else{ $response['rate'] = 0; }
        
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit; 
    }
    
    // get bank list
    public function bank_list(){
        
        $datas = (array)json_decode(file_get_contents('php://input'));
        
        $lib = new Bank_lib();
        $result = $lib->get();
        
        if ($result){
	foreach($result as $res)
	{
	   $output[] = array ("id" => $res->id, "acc_name" => $res->acc_name, "acc_no" => $res->acc_no, "acc_bank" => $res->acc_bank,
                              "currency" => $res->currency);
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
    

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
