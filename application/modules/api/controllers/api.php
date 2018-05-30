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

   public function category($parent=0){
        
        $lib = new Categoryproduct_lib();
        $result = $lib->get_based_parent($parent);
        
        if ($result){
	foreach($result as $res)
	{
	   $output[] = array ("id" => $res->id, "name" => $res->name, "permalink" => $res->permalink, "order" => $res->orders, "image" => base_url().'images/category/'.$res->image);
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

   public function category_detail($parent=0){
        
        $lib = new Categoryproduct_lib();
        $res = $lib->get_by_id($parent)->row();
        
        if ($res){
	$output[] = array ("id" => $res->id, "name" => $res->name, "permalink" => $res->permalink, "order" => $res->orders, "image" => base_url().'images/category/'.$res->image);
        
        $response['content'] = $output;
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit; 
        }
    }
        
    // get product list based category and limit
    public function product($cat,$type=null,$limit=100){
        
        $lib = new Product_lib();
        if ($type != 'recommend'){
           $result = $lib->get_product_based_category($cat,$limit);    
        }else{ $result = $lib->get_recommended($limit); }
        
        
        foreach($result as $res){
            
            $output[] = array ("id" => $res->id, "sku" => $res->sku, "name" => $res->name, "order" => $res->orders, "price" => $res->price, "restricted" => $res->restricted, "qty" => $res->qty,  
                               "image" => base_url().'images/product/'.$res->image);
        }
        $response['content'] = $output;
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit; 
    }
    
     public function product_detail($pid=0){
        
        $lib = new Product_lib();
        $cat = new Categoryproduct_lib();
        $model = new Model_lib();
        
        $res = $lib->get_detail_based_id($pid);
        $url1 = null; $url2 = null; $url3 = null; $url4 = null; $url5 = null; $url6 = null;
        if ($res->url_type == 'UPLOAD'){ $url = base_url().'images/product/'; 
        
            if ($res->url1){ $url1 = $url.$res->url1; }
            if ($res->url2){ $url2 = $url.$res->url2; }
            if ($res->url3){ $url3 = $url.$res->url3; }
            if ($res->url4){ $url4 = $url.$res->url4; }
            if ($res->url5){ $url5 = $url.$res->url5; }
            if ($res->url6){ $url6 = $url.$res->url6; }
        
        }else{
           $url1 = $res->url1; $url2 = $res->url2; $url3 = $res->url3;
           $url4 = $res->url4; $url5 = $res->url5; $url6 = $res->url6;
        }
        
        $output[] = array ("id" => $res->id, "sku" => $res->sku, "name" => $res->name,
                           "category" => $cat->get_name($res->category), "model" => $model->get_name($res->model), 
                           "model_id" => $res->model,
                           "image" => base_url().'images/product/'.$res->image,  
                           "url1" => $url1, "url2" => $url2, "url3" => $url3, "url4" => $url4, 
                           "url5" => $url5, "url6" => $url6, "price" => $res->price, "restricted" => $res->restricted,
                           "qty" => $res->qty, "start" => $res->start, "end" => $res->end, "recommended" => $res->recommended, "orders" => $res->orders, 
                          );
         
        $response['content'] = $output;
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit; 
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
