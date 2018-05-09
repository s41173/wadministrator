<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cart extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Cart_model', 'model', TRUE);

        $this->properti = $this->property->get();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->product = new Product_lib();
        $this->color = new Color_lib();
        $this->series = new Model_lib();
        $this->material = new Material_lib();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token'); 
    }

    private $properti, $modul, $title;
    private $product, $color, $series, $material;

    function index()
    {
       $this->get(); 
    }
    
    public function get(){
        
        $datas = (array)json_decode(file_get_contents('php://input'));
        $agent = $datas['agent_id'];
        
        $result = $this->model->get_by_agent($agent)->result();
        
        if ($result){
	foreach($result as $res)
	{
           $product = $this->product->get_by_id($res->product_id)->row();
           $attr = explode('|', $res->attribute);
           $color = $attr[4];
           $color = $this->color->get_name($color);
	   $output[] = array ("id" => $res->id, "agent_id" => $res->agent_id, "product_id" => $res->product_id, "product" => $product->name,
                              "model" => $this->series->get_name($product->model),
                              "qty" => $res->qty, "tax" => $res->tax, "amount" => $res->amount, "price" => $res->price, "attribute" => $res->attribute, "color" => $color,
                              "description" => $res->description, "publish" => $res->publish, "glass" => $this->material->get_name($attr[6]),
                              "image" => base_url().'images/product/'.$product->image);
	}
        
        $total_p = $this->model->total($agent,1);
        $total_u = $this->model->total($agent,0);
        
        $total = array('amount_publish' => intval($total_p['amount']), 'amount_unpublish' => intval($total_u['amount']), 'qty_publish' => intval($total_p['qty']), 'qty_unpublish' => intval($total_u['qty']));
        $response['content'] = $output;
        $response['total'] = $total;
        
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit; 
        }
    }
    
    function delete_agent()
    {
        $datax = (array)json_decode(file_get_contents('php://input')); 
        $status = true;
        $error = null;
        
        if ( isset($datax['agent_id']) ){  $this->model->delete_by_agent($datax['agent_id']);  }
        else{ $status = false; $error = 'Invalid JSON Format';  }
        
        $response = array('status' => $status, 'error' => $error);
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;
    }
    
    function publish()
    {
        $datax = (array)json_decode(file_get_contents('php://input')); 
        $status = true;
        $error = null;
        
        if ( isset($datax['id']) ){ $error = $this->model->publish($datax['id']); }
        else{ $status = false; $error = 'Invalid JSON Format';  }
        
        $response = array('status' => $status, 'error' => $error);
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;
    }

    function delete()
    {
        $datax = (array)json_decode(file_get_contents('php://input')); 
        $status = true;
        $error = null;
        
        if ( isset($datax['id']) ){  $this->model->force_delete($datax['id']);  }
        else{ $status = false; $error = 'Invalid JSON Format';  }
        
        $response = array('status' => $status, 'error' => $error);
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;
    }

    function add()
    {
        $datax = (array)json_decode(file_get_contents('php://input')); 
        $status = true;
        $error = null;
        
        if ( isset($datax['agent_id']) && isset($datax['product_id']) && isset($datax['qty']) && isset($datax['price']) && isset($datax['attribute']) && isset($datax['description']) )
        {   
            $amount = intval($datax['price'])*intval($datax['qty']);
            $tax = intval($amount*0.1);
            
            $cart = array('agent_id' => $datax['agent_id'], 'product_id' => $datax['product_id'], 'qty' => $datax['qty'],
                          'price' => $datax['price'], 'attribute' => $datax['attribute'], 'description' => $datax['description'],  
                          'amount' => $amount, 'tax' => $tax,
                          'created' => date('Y-m-d H:i:s'));

            $this->model->add($cart);
        }
        else{ $status = false; $error = 'Invalid JSON Format';  }
        
        $response = array('status' => $status, 'error' => $error);
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;
    }

    function update()
    {
        $datax = (array)json_decode(file_get_contents('php://input')); 
        $status = true;
        $error = null;
        
        if ( isset($datax['id']) && isset($datax['qty']) )
        {   
            $price = $this->model->get_by_id($datax['id'])->row();
            $price = $price->price;
            
            $amount = intval($price)*intval($datax['qty']);
            $tax = intval($amount*0.1);
            $cart = array('qty' => $datax['qty'], 'amount' => $amount, 'tax' => $tax);

            $this->model->updateid($datax['id'],$cart);
        }
        else{ $status = false; $error = 'Invalid JSON Format';  }
        
        $response = array('status' => $status, 'error' => $error);
        
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;
    }

}

?>
