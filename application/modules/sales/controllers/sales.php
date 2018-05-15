<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'definer.php';

class Sales extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Sales_model', '', TRUE);
        $this->load->model('Sales_item_model', 'sitem', TRUE);

        $this->properti = $this->property->get();
//        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->role = new Role_lib();
        $this->currency = new Currency_lib();
        $this->sales = new Product_lib();
        $this->customer = new Customer_lib();
        $this->payment = new Payment_lib();
        $this->city = new City_lib();
        $this->product = new Product_lib();
        $this->shipping = new Shipping_lib();
        $this->bank = new Bank_lib();
        $this->category = new Categoryproduct_lib();
        $this->agent = new Agent_lib();
        $this->pmodel = new Model_lib();
        $this->shiprate = new Shiprate_lib();
        $this->sales_payment = new Sales_payment_lib();
        $this->sms = new Sms_lib();
        $this->material = new Material_lib();
        $this->discount = new Discount_lib();
        $this->color = new Color_lib();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token'); 
    }

    private $properti, $modul, $title, $sales, $wt ,$shipping, $bank, $pmodel, $shiprate, $material, $discount;
    private $role, $currency, $customer, $payment, $city, $product ,$category,$agent,$sales_payment, $color;
    
    function index()
    {
//         echo constant("RADIUS_API");
       $this->session->unset_userdata('start'); 
       $this->session->unset_userdata('end');
       $this->get_last(); 
    }
    
    private function test_sms(){
        $result = $this->sms->sending_messabot('082277014410',"Hello From System Baru Lagi");
        if ($result == TRUE){ echo 'berhasil'; }else{ echo 'gagal'; }
    }
        
    // function untuk memeriksa input user dari form sebagai admin
    
    function add_order()
    {
        $datax = (array)json_decode(file_get_contents('php://input')); 

        $dates = $datax['date'];
        $agent = $datax['agent'];
        $cust  = $datax['cust'];

        if ($dates != null && $agent != null && $cust != null)
        {
            $orderid = $this->Sales_model->counter().mt_rand(100,9999);
            
            $sales = array('cust_id' => $cust, 'code' => $orderid, 'agent_id' => $agent, 'dates' => $dates,
                           'created' => date('Y-m-d H:i:s'));

            $this->Sales_model->add($sales);
            $response = array('Status' => true, 'Orderid' => $orderid); 
        }
        else{ $response = array('Status' => false, 'Orderid' => 0);  }
            
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;
    }
    
    // add item json
    
    private function valid_json($param){
        
      if (isset($param->qty) && isset($param->tax) && isset($param->price) && isset($param->width) && isset($param->height) && isset($param->fixed_top) &&
          isset($param->fixed_bot) && isset($param->color) && isset($param->glasstype) && isset($param->glass_id) && isset($param->frame) &&  isset($param->description))
      { return TRUE; }else{ return FALSE; }
    }
    
    // get weight based shopping cart
    function get_weight()
    {  
        $datax = (array)json_decode(file_get_contents('php://input'));         
        
        $cart = new Cart_lib();
        $agent = $datax['agent_id'];
        $result = true;
        $error = null;
        $totweight = 0;
        
        $results = $cart->get_by_agent($agent)->result();
        
          foreach($results as $res){
              
              if ($this->product->valid_product($res->product_id)){
                  
                $qty = intval($res->qty);
                $dimension = explode('|', $res->attribute);
                                
                $model = $this->product->get_detail_based_id($res->product_id);
                
                // get weight
                $keliling = intval($dimension[0]*2) + intval($dimension[1]*2);
                $weight = intval(intval($model->weight)*$keliling);
                $weight = intval($qty*$weight);
                
                // get weight kaca
                $weightglass = $this->material->get_glass_weight($dimension[0],$dimension[1],$dimension[6]);
                $weightglass = intval($weightglass*$qty);
                $totweight = floatval($totweight+$weight+$weightglass);
                $margin = intval(0.1*$totweight);
                $totweight = $totweight+$margin;
                
              }else { $result = false; $error = 'Invalid Product'; break; }
          }
        
        $status = array('result' => $result, 'error' => $error, 'weight' => $totweight);
        $response['status'] = $status;
            
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit;  
    }
    
    // get weight based orderid
    function get_weight_order()
    {  
        $datax = (array)json_decode(file_get_contents('php://input'));         
        
        $uid = $this->Sales_model->get_id_based_order($datax['orderid']);
        $result = true;
        $error = null;
        $totweight = 0;
        
        $results = $this->sitem->get_last_item($uid)->result();
        
          foreach($results as $res){
              
              if ($this->product->valid_product($res->product_id)){
                  
                $qty = intval($res->qty);
                $dimension = explode('|', $res->attribute);
                                
                $model = $this->product->get_detail_based_id($res->product_id);
                
                // get weight
                $keliling = intval($dimension[0]*2) + intval($dimension[1]*2);
                $weight = intval(intval($model->weight)*$keliling);
                $weight = intval($qty*$weight);
                
                // get weight kaca
                $weightglass = $this->material->get_glass_weight($dimension[0],$dimension[1],$dimension[6]);
                $weightglass = intval($weightglass*$qty);
                $totweight = floatval($totweight+$weight+$weightglass);
                $margin = intval(0.1*$totweight);
                $totweight = $totweight+$margin;
                
              }else { $result = false; $error = 'Invalid Product'; break; }
          }
        
        $status = array('result' => $result, 'error' => $error, 'weight' => $totweight);
        $response['status'] = $status;
            
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit;  
    }
    
    private function set_discount($orderid){
        $sales = $this->Sales_model->get_sales_based_order($orderid)->row();
        
        $amount = intval($sales->amount-$sales->tax);
        $discount = $this->discount->get_discount($amount, $sales->agent_id, $sales->dates);
        $discount = intval($sales->total*$discount/100);
        $amount = intval($amount-$discount);
        $tax = intval($amount*0.1);
        $amount = intval($amount+$tax);
        
//        echo "amount : ".$amount.
//             '<br>'.'discount : '.$discount;
        
        $param = array('discount' => $discount, 'tax' => $tax, 'amount' => $amount);
        $this->Sales_model->update($sales->id, $param);
    }
    
    function add_item_json()
    {  
        $datax = (array)json_decode(file_get_contents('php://input'));         
 
        $content = $datax['content'];
        $orderid = $datax['status']->orderid;
        $result = true;
        $error = null;
        
        if ($this->cek_orderid($orderid) == TRUE && $this->valid_confirm($orderid,'code') == TRUE && $this->valid_shipping_json($datax['shipping']) == TRUE)
        {  
          for($i=0; $i<count($content); $i++){
              
              if ($this->product->valid_product($content[$i]->product_id) == TRUE && $this->valid_json($content[$i]) == TRUE){
                  
                $qty   = intval($content[$i]->qty);
                $price = floatval($content[$i]->price);
                $amt_price = floatval($qty*$price);
                $percenttax = floatval($content[$i]->tax);
                $tax = floatval($percenttax*$amt_price);
                                
                $model = $this->product->get_detail_based_id($content[$i]->product_id);
                
                // get weight
                $keliling = intval($content[$i]->width*2) + intval($content[$i]->height*2);
                $weight = intval($model->weight*$keliling);
                $weight = intval($qty*$weight);
                $vol = 0;
                
                // get weight kaca
                $weightglass = $this->material->get_glass_weight($content[$i]->width,$content[$i]->height,$content[$i]->glass_id);
                $weightglass = intval($qty*$weightglass);
                $totweight = floatval($weight+$weightglass);
                
                $margin = intval(0.1*$totweight);
                $totweight = $totweight+$margin;
                
                $attr = $content[$i]->width.'|'.$content[$i]->height.'|'.$content[$i]->fixed_top.'|'.$content[$i]->fixed_bot.'|'.$content[$i]->color.'|'.
                        $content[$i]->glasstype.'|'.$content[$i]->glass_id.'|'.$content[$i]->frame.'|'.$totweight.'|'.$vol;
                
                $sales = array('product_id' => $content[$i]->product_id, 'sales_id' => $this->Sales_model->get_id_based_order($orderid),
                               'qty' => $qty, 'tax' => $tax, 'attribute' => $attr, 'description' => $content[$i]->description,
                               'price' => $price, 'amount' => floatval($amt_price));
                
                $this->sitem->add($sales);
                $this->update_trans($this->Sales_model->get_id_based_order($orderid));
              }else { $result = false; $error = 'Invalid JSON Format'; break; }
          }
          
          // add shipping
          if ($result == true){ $this->shipping_json($datax); }
          
          // get discount status
          $this->set_discount($orderid);
          
        }
        else{ $result = false; $error = 'Invalid Orderid'; }
        
        $status = array('result' => $result, 'error' => $error);
        $response['status'] = $status;
            
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit;  
    }
    
    function revision_json()
    {  
        $datax = (array)json_decode(file_get_contents('php://input'));    
 
        $orderid = $datax['status']->orderid;
        $result = true;
        $error = null;
        
        if ($this->cek_orderid($orderid) == TRUE && $this->valid_confirm($orderid,'code') == TRUE )
        {  
            $uid = $this->Sales_model->get_id_based_order($orderid);
            $transaction = $this->sitem->get_last_item($uid)->result();
            
            foreach ($transaction as $res) {
              
              if ($this->product->valid_product($res->product_id) == TRUE){
                                
                $model = $this->product->get_detail_based_id($res->product_id);
                $attr = explode('|', $res->attribute);
                
                // get weight
                $keliling = intval($attr[0]*2) + intval($attr[1]*2);
                $weight = intval($model->weight*$keliling);
                $weight = intval($res->qty*$weight);
                $vol = 0;
                
                // get weight kaca
                $weightglass = $this->material->get_glass_weight($attr[0],$attr[1],$attr[6]);
                $weightglass = intval($res->qty*$weightglass);
                $totweight = floatval($weight+$weightglass);
                
                $margin = intval(0.1*$totweight);
                $totweight = $totweight+$margin;
                
                $attr = $attr[0].'|'.$attr[1].'|'.$attr[2].'|'.$attr[3].'|'.$attr[4].'|'.
                        $attr[5].'|'.$attr[6].'|'.$attr[7].'|'.$totweight.'|'.$vol;
                
                $item = array( 'attribute' => $attr);
                $this->sitem->update_trans($res->id,$item);
                
                $this->update_trans($this->Sales_model->get_id_based_order($orderid));
              }else { $result = false; $error = 'Invalid JSON Format'; break; }
          }
//          
          // add shipping
          if ($result == true){ $this->shipping_json($datax); }
          
          // get discount status
          $this->set_discount($orderid);
          
        }
        else{ $result = false; $error = 'Invalid Orderid'; }
        
        $status = array('result' => $result, 'error' => $error);
        $response['status'] = $status;
            
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit;  
    }
    
    function valid_shipping_json($shipping){
        
        if (isset($shipping->courier) && isset($shipping->type) && isset($shipping->package) && isset($shipping->dest) && isset($shipping->district) && 
            isset($shipping->dest_desc)){ return TRUE; }else{ return FALSE; }
    }
    
    private function shipping_json($datax){
        
        $result = TRUE;
        $error = null;
        
        $orderid = $datax['status']->orderid;
        $sid = $this->Sales_model->get_id_based_order($orderid);
        $shipping = $datax['shipping'];
        
        if ($this->valid_shipping_json($shipping) == TRUE){
           
            $rate = $this->shiprate->rate($shipping->dest, $shipping->district, $shipping->type, $shipping->courier);
            $weight = $this->sitem->total_vol($sid);
            
            $param = array('sales_id' => $sid, 'shipdate' => null,
                           'courier' => $shipping->courier, 'dest' => $shipping->dest,
                           'district' => $shipping->district,
                           'dest_desc' => $shipping->dest_desc, 'package' => $shipping->package,
                           'weight' => $weight, 'rate' => $rate,
                           'amount' => intval($weight*$rate));
            
            $this->shipping->create($sid, $param);
            $this->update_trans($sid);
            
        }else { $result = FALSE; $error = "Invalid JSON Format"; }
        return $result; 
    }
    
    function confirmation_json(){
        
        $datax = (array)json_decode(file_get_contents('php://input')); 
        
        $uid = $this->Sales_model->get_id_based_order($datax['orderid']);
        $val = $this->Sales_model->get_by_id($uid)->row();
        
        if ($val->approved == 0){
           
           if ($val->amount > 0 && $val->shipping > 0){ 
               $lng = array('approved' => 1); $this->Sales_model->update($uid,$lng); 
               
               if ( $this->pdf($uid) == TRUE ){ $result = true; $error = 'Sales Order : '.$datax['orderid'].' posted'; }
               else{ $result = false; $error = 'error|Sending Error..!'; }
           }
           else { $result = false; $error = 'Invalid Amount..!'; }
        }    
        else { $result = false; $error = 'Sales Already Confirmed..!'; }
       
        $status = array('result' => $result, 'error' => $error);
        $response['status'] = $status;
            
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit;  
    }
    
    function cek_orderid_json(){
        
        $datax = (array)json_decode(file_get_contents('php://input')); 
        if ($this->Sales_model->valid_orderid($datax['orderid']) == FALSE){ $status = false; }else{ $status = true; }
        
        $response = array('status' => $status);
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response, 128))
        ->_display();
        exit;
        
    }
    
    function get_sales_details_json(){
        
        $datax = (array)json_decode(file_get_contents('php://input')); 
        
        $res = $this->Sales_model->get_sales_based_order($datax['orderid'])->row();
        
        $output[] = array ("id" => $res->id, "code" => $res->code, "dates" => $res->dates, "agent_id" => $res->agent_id,
                           "cust_id" => $res->cust_id, 'customer' => $this->customer->get_name($res->cust_id), "amount" => $res->amount, "tax" => $res->tax, "cost" => $res->cost, "total" => $res->total,
                           "shipping" => $res->shipping, "discount" => $res->discount, "approved" => $res->approved );
        
        $response['content'] = $output;
        
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response, 128))
        ->_display();
        exit;
        
    }
    
    function get_sales_transaction_json(){
        
        $datax = (array)json_decode(file_get_contents('php://input')); 
        
        $res = $this->Sales_model->get_sales_based_order($datax['orderid'])->row();
        
        $output[] = array ("id" => $res->id, "code" => $res->code, "dates" => $res->dates, "agent_id" => $res->agent_id,
                           "cust_id" => $res->cust_id, 'customer' => $this->customer->get_name($res->cust_id), "amount" => $res->amount, "tax" => $res->tax, "cost" => $res->cost, "total" => $res->total,
                           "shipping" => $res->shipping, "discount" => $res->discount, "approved" => $res->approved );
        
        $response['content'] = $output;
        
        // get transaction item
        $trans = $this->sitem->get_last_item($res->id)->result();
        
        foreach ($trans as $res) {
          
          $attr = explode('|', $res->attribute);
          $product = $this->product->get_detail_based_id($res->product_id);
          $output1[] = array ("id" => $res->id, "sales_id" => $res->sales_id, "product" => $product->name, "product_id" => $res->product_id, "qty" => $res->qty,
                             "tax" => $res->tax, 'amount' => $res->amount, "price" => $res->price, "attribute" => $res->attribute, 
                             "image" => base_url().'images/product/'.$product->image, "model" => $this->pmodel->get_name($product->model),
                             "color" => $this->color->get_name($attr[4]), "description" => $res->description );  
        }
        
        $response['transaction'] = $output1;
        
        // get shipping transaction
        $shipping = $this->shipping->get_detail_based_sales($res->id);
        
        $output2[] = array ("id" => $shipping->id, "sales_id" => $shipping->sales_id, "shipdate" => $shipping->shipdate, "courier" => $shipping->courier,
                           "awb" => $shipping->awb, 'origin' => $shipping->origin, 'origin_id' => $shipping->origin_id, 'origin_desc' => $shipping->origin_desc,
                           "dest_id" => $shipping->dest, "dest" => $this->shiprate->get_city_name($shipping->dest), "district" => $shipping->district, "dest_desc" => $shipping->dest_desc, 
                           "package" => $shipping->package,
                           "rate" => $shipping->rate, "weight" => $shipping->weight, "amount" => $shipping->amount, "status" => $shipping->status );
        
        $response['shipping'] = $output2;
        
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response, 128))
        ->_display();
        exit;
        
    }
    
    function get_shipping_details_json(){
        
        $datax = (array)json_decode(file_get_contents('php://input')); 
        
        $uid = $this->Sales_model->get_id_based_order($datax['orderid']);
        $res = $this->shipping->get_detail_based_sales($uid);
        
        $output[] = array ("id" => $res->id, "sales_id" => $res->sales_id, "shipdate" => $res->shipdate, "courier" => $res->courier,
                           "awb" => $res->awb, 'origin' => $res->origin, 'origin_id' => $res->origin_id, 'origin_desc' => $res->origin_desc,
                           "dest_id" => $res->dest, "dest" => $this->shiprate->get_city_name($res->dest), "district" => $res->district, "dest_desc" => $res->dest_desc, "package" => $res->package,
                           "rate" => $res->rate, "weight" => $res->weight, "amount" => $res->amount, "status" => $res->status );
        
        $response['content'] = $output;
        
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response, 128))
        ->_display();
        exit;
        
    }
    
   //    fungsi untuk mendapatkan sales list berdasarkan agent dan status approved
    
    function get_sales_by_agent_json(){
        
        $datax = (array)json_decode(file_get_contents('php://input')); 
        
        $output = null;
        $result = $this->Sales_model->search_json($datax['agent_id'],$datax['confirm'],$datax['limit'])->result();
        
        foreach ($result as $res){
            
            $output[] = array ("id" => $res->id, "code" => $res->code, "dates" => $res->dates, "agent_id" => $res->agent_id,
                               "cust_id" => $res->cust_id, 'customer' => $this->customer->get_name($res->cust_id), "amount" => $res->amount, "tax" => $res->tax, "cost" => $res->cost, "total" => $res->total,
                               "shipping" => $res->shipping, "discount" => $res->discount, "approved" => $res->approved );
        }
                
        $response['content'] = $output;
        
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response, 128))
        ->_display();
        exit;
        
    }
    
    //    fungsi untuk mendapatkan sales list berdasarkan agent dan sudah lunas atau belum
    function get_sales_status_by_agent_json(){
        
        $datax = (array)json_decode(file_get_contents('php://input')); 
        
        $output = null;
        $result = $this->Sales_model->search_json($datax['agent_id'],1, $datax['limit'])->result();
        
        foreach ($result as $res){
            
           $total = intval($res->amount);  
           $payment = $this->sales_payment->total($res->id);
           
           if ($datax['status'] == 'C'){
               
               if (floatval($total+$res->shipping-$payment) > 0){ 
                   
                $output[] = array ("id" => $res->id, "code" => $res->code, "dates" => $res->dates, "agent_id" => $res->agent_id,
                "cust_id" => $res->cust_id, 'customer' => $this->customer->get_name($res->cust_id), "amount" => $res->amount, "tax" => $res->tax, "cost" => $res->cost, "total" => $res->total,
                "shipping" => $res->shipping, "discount" => $res->discount, "approved" => $res->approved, "payment_total" => $payment );
                   
               }
           }else{
               
               if (floatval($total+$res->shipping-$payment) <= 0){  
                   
                $output[] = array ("id" => $res->id, "code" => $res->code, "dates" => $res->dates, "agent_id" => $res->agent_id,
                "cust_id" => $res->cust_id, 'customer' => $this->customer->get_name($res->cust_id), "amount" => $res->amount, "tax" => $res->tax, "cost" => $res->cost, "total" => $res->total,
                "shipping" => $res->shipping, "discount" => $res->discount, "approved" => $res->approved, "payment_total" => $payment );
               }
               
           }
        }
                
        $response['content'] = $output;
        
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response, 128))
        ->_display();
        exit;
        
    }
    
    // edit transaction
     function edit_transaction(){
        
        $datax = (array)json_decode(file_get_contents('php://input')); 
        
        $price = floatval($datax['price']);
        $qty   = intval($datax['qty']);
        $amt_price = floatval($qty*$price);
        $percenttax = 0.1;
        $tax = floatval($percenttax*$amt_price);
        
        $sales = array( 'qty' => $qty, 'tax' => $tax, 'price' => $price, 'amount' => floatval($amt_price));
        $this->sitem->update_trans($datax['id'],$sales);
        
        $response = array('status' => true);
                
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response, 128))
        ->_display();
        exit;
    }
    
    // delete transaction
    function delete_transaction(){
        
        $datax = (array)json_decode(file_get_contents('php://input')); 
        $this->sitem->delete($datax['id']);
        
        $id = $this->Sales_model->get_id_based_order($datax['orderid']);
        
        $tot = $this->sitem->total($id);
        $tot = intval($tot['amount']);
        if ($tot == 0){ $this->Sales_model->force_delete($id); $this->shipping->delete_by_sales($id); }
        
        $response = array('status' => true, 'reload' => true);
                
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response, 128))
        ->_display();
        exit;
        
    }
    
    function json_process()
    {
        $datax = (array)json_decode(file_get_contents('php://input')); 

        $username = $datax['user'];
        $password = $datax['pass'];

            if ($username == 'admin' && $password == 'admin')
            {
                $this->mail_invoice(7);
                $response = array(
                  'Success' => true,
		  'User' => $datax['user'],
                  'Info' => 'Login Success Lah'); 
            }
            else
            {
                $response = array(
                'Success' => false,
                'Info' => 'Invalid Login..!!');
            }
            
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;

    }
    
//     ============== ajax ===========================
    
    function get_district($city=null)
    {
       echo $this->shiprate->combo_district($city); 
    }
    
    function ongkir_nilai()
    {
      $city = $this->input->post('city'); 
      $district = $this->input->post('district'); 
      $weight = $this->input->post('weight'); 
      
      $rate = $this->shiprate->rate($city, $district); 
      echo $rate.'|'.idr_format($rate).'|'. idr_format(floatval($rate*$weight));
    }
    
    function get_customer($id)
    {
        if ($id){
          $cust = $this->customer->get_details($id)->row();
          echo $cust->email.'|'.$cust->shipping_address;
        }else { echo "|"; }
    }
    
    function get_product($pid)
    {
        $res = $this->product->get_detail_based_id($pid);
        echo intval($res->price-$res->discount);
    }


//     ============== ajax ===========================
     
    public function getdatatable($search=null,$cust='null',$confirm='null')
    {
        if(!$search){ $result = $this->Sales_model->get_last($this->modul['limit'])->result(); }
        else {$result = $this->Sales_model->search($cust,$confirm)->result(); }
	
        $output = null;
        if ($result){
                
         foreach($result as $res)
	 {
           $total = intval($res->amount-$res->discount);  
           $payment = $this->sales_payment->total($res->id);
           if (floatval($total+$res->shipping-$payment) > 0){ $status = 'C'; }else{ $status = 'S'; }
           
           if ($this->shipping->cek_shiping_based_sales($res->id) == true){ $ship = 'Shipped'; }else{ $ship = '-'; } // shipping status
           
	   $output[] = array ($res->id, $res->code, tglin($res->dates), $this->customer->get_name($res->cust_id), idr_format(floatval($total+$res->shipping)),
                              idr_format(floatval($total+$res->shipping-$payment)), $status, $ship, $res->approved, $this->agent->get_name($res->agent_id)
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

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Sales Order');
        $data['h2title'] = 'Sales Order';
        $data['main_view'] = 'sales_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all/hard');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['form_action_import'] = site_url($this->title.'/import');
        $data['form_action_confirmation'] = site_url($this->title.'/payment_confirmation');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));

        $data['agent'] = $this->agent->combo();
        $data['bank'] = $this->bank->combo();
        $data['array'] = array('','');
        $data['month'] = combo_month();
        $data['year'] = date('Y');
        $data['default']['month'] = date('n');
        
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
        $this->table->set_heading('#','No', 'Code', 'Date', 'Agent', 'Customer', 'Total', 'Balance', 'Status', 'Ship-Status', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable/');
        $data['graph'] = site_url()."/sales/chart/".$this->input->post('cmonth').'/'.$this->input->post('tyear');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    function chart($month=null,$year=null)
    {   
        $data = $this->category->get();
        $datax = array();
        foreach ($data as $res) 
        {  
           $tot = $this->Sales_model->get_sales_qty_based_category($res->id,$month,$year); 
           $point = array("label" => $res->name , "y" => $tot);
           array_push($datax, $point);      
        }
        echo json_encode($datax, JSON_NUMERIC_CHECK);
    }
    
    function publish($uid = null)
    {
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
       $val = $this->Sales_model->get_by_id($uid)->row();
       if ($val->approved == 0){
           
           if ($val->amount > 0 && $val->shipping > 0){ 
              $lng = array('approved' => 1);
              if ( $this->pdf($uid) == TRUE ){ $mess = 'true|Sales Order Confirmed..!'; }
              else{ $mess = 'error|Sending Error..!'; }
           }
           else { $lng = array('approved' => 0); $mess = 'error|Error Validation Amount..!'; }
       }    
       else { $lng = array('approved' => 0); $pdf = "./downloads/".$val->code.'.pdf'; @unlink("$pdf"); $mess = 'true|Sales Order Unconfirmed..!'; }
       
       $this->Sales_model->update($uid,$lng);
       echo $mess;
       }else{ echo "error|Sorry, you do not have the right to change publish status..!"; }
    }
    
     private function send_confirmation_sms($sid){
       
        $sales = $this->Sales_model->get_by_id($sid)->row();
        $customer = $this->customer->get_details($sales->cust_id)->row();
        
        $amount = idr_format(floatval($sales->amount+$sales->cost+$sales->shipping));
        $mess = "Konfirmasi pesanan anda dengan no : ".$sales->code." sebesar ".$amount.",- Mohon periksa email anda untuk informasi tagihan lebih lanjut.";
        return $this->sms->send($customer->phone1, $mess);
    }
    
    private function send_confirmation_email($sid)
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
       
       $sales = $this->Sales_model->get_by_id($sid)->row();

       $data['code']    = strtoupper($sales->code);
       $data['date']    = tglincomplete($sales->dates);
       $data['amount']  = idr_format(floatval($sales->amount+$sales->cost+$sales->shipping));
       
       $customer = $this->customer->get_details($sales->cust_id)->row();
       $data['c_name'] = strtoupper($customer->first_name.' '.$customer->last_name);
       
        // email send
        $this->load->library('email');
        $config['charset']  = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';

        $this->email->initialize($config);
        $this->email->from($this->properti['email'], $this->properti['name']);
        $this->email->to($customer->email);
        $this->email->cc($this->properti['cc_email']);  
        
        $html = $this->load->view('sales_confirmation_mess',$data,true); 
        $this->email->subject('Sales Order - '.strtoupper($sales->code));
        $this->email->message($html);
        $this->email->attach(FCPATH."/downloads/".$sales->code.".pdf");

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
             if ($type == 'soft') { $this->Sales_model->delete($cek[$i]); }
             else { $this->shipping->delete_by_sales($cek[$i]);
                    $this->Sales_model->force_delete($cek[$i]);  
             }
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
            
            $val = $this->Sales_model->get_by_id($uid)->row();
            $pdf = "./downloads/".$val->code.'.pdf'; @unlink("$pdf");
            $this->shipping->delete_by_sales($uid);
            $this->sales_payment->delete_by_sales($uid);
            $this->Sales_model->delete($uid);
            $this->session->set_flashdata('message', "1 $this->title successfully removed..!");

            echo "true|1 $this->title successfully removed..!";
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
        
    }
    
    function add($param=0)
    {

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Create New '.$this->modul['title'];
        $data['main_view'] = 'sales_form';
        if ($param == 0){$data['form_action'] = site_url($this->title.'/add_process'); $data['counter'] = $this->Sales_model->counter(); }
        else { $data['form_action'] = site_url($this->title.'/update_process'); $data['counter'] = $param; }
	
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));
        $data['form_action_trans'] = site_url($this->title.'/add_item/0'); 
        $data['form_action_shipping'] = site_url($this->title.'/shipping/0'); 

        $data['customer'] = $this->customer->combo();
        $data['agent'] = $this->agent->combo();
        $data['payment'] = $this->payment->combo();
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['graph'] = site_url()."/sales/chart/";
        $data['city'] = $this->city->combo_city_combine();
        $data['default']['dates'] = date("Y/m/d");
        $data['product'] = $this->product->combo();
        
        $data['items'] = $this->sitem->get_last_item(0)->result();

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
        $this->form_validation->set_rules('ccustomer', 'Customer', 'required');
        $this->form_validation->set_rules('tdates', 'Transaction Date', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $sales = array('cust_id' => $this->input->post('ccustomer'), 'dates' => date("Y-m-d H:i:s"),
                           'created' => date('Y-m-d H:i:s'));

            $this->Sales_model->add($sales);
            echo "true|One $this->title data successfully saved!|".$this->Sales_model->counter(1);
            $this->session->set_flashdata('message', "One $this->title data successfully saved!");
//            redirect($this->title.'/update/'.$this->Sales_model->counter(1));
        }
        else{ $data['message'] = validation_errors(); echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }

    }
    
    function add_item($sid=0)
    { 
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
       if ($sid == 0){ echo 'error|Sales ID not saved'; }
       else {
       
         // Form validation
        $this->form_validation->set_rules('cproduct', 'Product', 'required|callback_valid_product['.$sid.']');
        $this->form_validation->set_rules('tqty', 'Qty', 'required|numeric');
        $this->form_validation->set_rules('tprice', 'Price', 'required|numeric');
        $this->form_validation->set_rules('ctax', 'Tax Type', 'required');

            if ($this->form_validation->run($this) == TRUE && $this->valid_confirm($sid) == TRUE)
            {
                $amt_price = intval($this->input->post('tqty')*$this->input->post('tprice'));
                $tax = intval($this->input->post('ctax')*$amt_price);
                $sales = array('product_id' => $this->input->post('cproduct'), 'sales_id' => $sid,
                               'qty' => $this->input->post('tqty'), 'tax' => $tax,
                               'price' => $this->input->post('tprice'), 'amount' => intval($amt_price));

                $this->sitem->add($sales);
                $this->update_trans($sid);
                echo "true|Sales Transaction data successfully saved!|";
            }
            else{ echo "error|".validation_errors(); }  
        }
       }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    private function update_trans($sid)
    {
        $totals = $this->sitem->total($sid);
        $price = intval($totals['amount']);
        
        // shipping total        
        $transaction = array('tax' => $totals['tax'], 'total' => intval($price-$totals['tax']), 'amount' => intval($price), 'shipping' => $this->shipping->total($sid));
	$this->Sales_model->update($sid, $transaction);
    }
    
    function delete_item($id,$sid)
    {
        if ($this->acl->otentikasi2($this->title) == TRUE && $this->valid_confirm($sid) == TRUE){ 
        
        $this->sitem->delete($id); // memanggil model untuk mendelete data
        $this->update_trans($sid);
        $this->session->set_flashdata('message', "1 item successfully removed..!"); // set flash data message dengan session
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
        redirect($this->title.'/update/'.$sid);
    }
    
    private function split_array($val)
    { return implode(",",$val); }
   
    function shipping($sid=0)
    { 
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
       if ($sid == 0){ echo 'error|Sales ID not saved'; }
       else {
       
        $sales = $this->Sales_model->get_by_id($sid)->row();
           
         // Form validation
        $this->form_validation->set_rules('ccity', 'City', 'required');
        $this->form_validation->set_rules('tshipaddkurir', 'Shipping Address', 'required');
        $this->form_validation->set_rules('tweight', 'Weight', 'required|numeric');

            if ($this->form_validation->run($this) == TRUE && $this->valid_confirm($sid) == TRUE)
            {
                $param = array('sales_id' => $sid, 'shipdate' => null,
                               'courier' => 'ESL', 'dest' => $this->input->post('ccity'),
                               'district' => $this->input->post('cdistrict'),
                               'dest_desc' => $this->input->post('tshipaddkurir'), 'package' => 'RDX',
                               'weight' => $this->input->post('tweight'), 'rate' => $this->input->post('rate'),
                               'amount' => intval($this->input->post('rate')*$this->input->post('tweight')));
                
                $this->shipping->create($sid, $param);
                $this->update_trans($sid);
                echo "true|Shipping Transaction data successfully saved!|";
            }
            else{ echo "error|".validation_errors(); }  
        }
       }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($param=0)
    {
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Update '.$this->modul['title'];
        $data['main_view'] = 'sales_form';
        $data['form_action'] = site_url($this->title.'/update_process/'.$param); 
        $data['form_action_trans'] = site_url($this->title.'/add_item/'.$param); 
        $data['form_action_shipping'] = site_url($this->title.'/shipping/'.$param); 
	
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $data['agent'] = $this->agent->combo();
        $data['customer'] = $this->customer->combo();
        $data['payment'] = $this->payment->combo();
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['graph'] = site_url()."/sales/chart/";
        $data['city'] = $this->shiprate->combo_city_id();
        $data['product'] = $this->product->combo();
        
        $sales = $this->Sales_model->get_by_id($param)->row();
        $customer = $this->customer->get_details($sales->cust_id)->row();
        
        $data['counter'] = $sales->code; 
        $data['default']['customer'] = $sales->cust_id;
        $data['default']['email'] = $customer->email;
        $data['default']['ship_address'] = $customer->shipping_address;
        $data['default']['dates'] = $sales->dates;
        $data['total'] = $sales->total;
        $data['shipping'] = $sales->shipping;
        $data['tot_amt'] = intval($sales->amount+$sales->shipping);
        
        // weight total
        $total = $this->sitem->total($param);

        $data['weight'] = round($this->sitem->total_vol($param,'weight'));
        $data['vol'] = round($this->sitem->total_vol($param,'vol'));
        $data['tax']    = $sales->tax;
        
        // shipping details
        $shipping = $this->shipping->get_detail_based_sales($param);
        if ($shipping){
           
           $data['default']['dest'] = $shipping->dest;
           $data['default']['district'] = $shipping->district;
           $data['default']['dest_desc'] = $shipping->dest_desc.' - '.$shipping->district; 
           $data['default']['package'] = idr_format($shipping->rate);
           $data['default']['rate'] = $shipping->rate;
        }
        
        // transaction table
        $data['items'] = $this->sitem->get_last_item($param)->result();
        $this->load->view('template', $data);
    }
    
        // Fungsi update untuk menset texfield dengan nilai dari database
    function invoice($param=0,$type='invoice')
    {
        $data['title'] = $this->properti['name'].' | Invoice '.ucwords($this->modul['title']).' | SO-0'.$param;
        $sales = $this->Sales_model->get_by_id($param)->row();
        
        if ($sales){
                
            // property
            $data['p_name'] = $this->properti['sitename'];
            $data['p_address'] = $this->properti['address'];
            $data['p_city'] = $this->properti['city'];
            $data['p_zip']  = $this->properti['zip'];
            $data['p_phone']  = $this->properti['phone1'];
            $data['p_email']  = $this->properti['email'];
            $data['p_logo']  = $this->properti['logo'];
            
            //agent details
            $agent = $this->agent->get_by_id($sales->agent_id)->row();
            $data['a_code'] = strtoupper($agent->code);
            $data['a_name'] = strtoupper($agent->name);
            $data['a_phone'] = $agent->phone1.' / '.$agent->phone2;
            $data['a_address'] = $agent->address;
            $data['a_city'] = $agent->city;
            $data['a_zip']  = $agent->zip;

            // customer details
            $customer = $this->customer->get_details($sales->cust_id)->row();
            $data['c_name'] = strtoupper($customer->first_name.' '.$customer->last_name);
            $data['c_email'] = $customer->email;
            $data['c_address'] = $customer->shipping_address;
            $data['c_phone'] = $customer->phone1.' / '.$customer->phone2;
            $data['c_city'] = $this->city->get_name($customer->city);
            $data['c_zip'] = $customer->zip;

            // sales
            $data['so'] = $sales->code.'/'.get_month_romawi(date('m', strtotime($sales->dates))).'/'.date('Y', strtotime($sales->dates));
            $data['dates'] = tglincomplete($sales->dates);

            $data['total'] = $sales->total;
            $data['shipping'] = idr_format(floatval($sales->shipping+$sales->cost));
            $data['tot_amt'] = idr_format(intval($sales->amount+$sales->cost+$sales->shipping));

            // weight total
            $total = $this->sitem->total($param);
            $data['tax'] = $sales->tax;
            $data['discount'] = $sales->discount;
            // bank
            $data['banks'] = $this->bank->get();

            // transaction table
            $data['items'] = $this->sitem->get_last_item($param)->result();
            if ($type == 'invoice'){ $this->load->view('sales_invoice', $data); }
            else{
                $html = $this->load->view('sales_invoice', $data, true); // render the view into HTML
                return $html;
            }
        }
    }
    
    function send_email_offer_json(){
        
        $datax = (array)json_decode(file_get_contents('php://input')); 
        
        $error = null;
        $sid = $this->Sales_model->get_id_based_order($datax['orderid']);
        $no = $datax['orderid'];
        
        $pdfFilePath = FCPATH."/downloads/".$no.".pdf";
        if (file_exists($pdfFilePath) == TRUE){ 
            
          if ($this->send_confirmation_email($sid) == TRUE && $this->send_confirmation_sms($sid) == TRUE)
          { $status = true; $error = 'Invoice Sent..!'; }
          else { $status = false; $error = 'Failed to sending invoice..!'; }
        }
        else{ $status = false; $error = 'File not existed..!'; }
        
        $status = array('status' => $status, 'error' => $error);
        $response['status'] = $status;
            
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit;  
    }
    
    private function pdf($sid=0)
    {
        $sales = $this->Sales_model->get_by_id($sid)->row();
        $no = $sales->code;
        // ===================== batas ================================
        // As PDF creation takes a bit of memory, we're saving the created file in /downloads/reports/
        $pdfFilePath = FCPATH."/downloads/".$no.".pdf";
        $data['page_title'] = 'Sales Order Invoice - '.$no; // pass data to the view

        if (file_exists($pdfFilePath) == FALSE)
        {
          //  ini_set('memory_limit','32M'); 
            $html = $this->invoice($sid,'html');

            $this->load->library('pdf');
            $pdf = $this->pdf->load();
//            $pdf->SetFooter($_SERVER['HTTP_HOST'].'|{PAGENO}|'.date(DATE_RFC822)); 
            $pdf->WriteHTML($html); // write the HTML into the PDF
            $pdf->Output($pdfFilePath, 'F'); // save to file because we can
        }
        
        if (file_exists($pdfFilePath) == TRUE){ 
            
          if ($this->send_confirmation_email($sid) == TRUE && $this->send_confirmation_sms($sid) == TRUE)
          { return TRUE; }
          else { return FALSE; }
        }
        else{ return FALSE; }
    }
    
    
    function update_process($param)
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'sales_form';
        $data['form_action'] = site_url($this->title.'/update_process/'.$param); 
	$data['link'] = array('link_back' => anchor('category/','<span>back</span>', array('class' => 'back')));

	// Form validation
        $this->form_validation->set_rules('ccustomer', 'Customer', 'required');
        $this->form_validation->set_rules('tdates', 'Transaction Date', 'required');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirm($param) == TRUE && $this->valid_items($param) == TRUE)
        {
            $sales = array('cust_id' => $this->input->post('ccustomer'),
                           'updated' => date('Y-m-d H:i:s'));

            $this->Sales_model->update($param, $sales);
            $this->update_trans($param);
            $this->session->set_flashdata('message', "One $this->title data successfully saved!");
            echo "true|One $this->title data successfully saved!|".$param;
        }
        else{ echo "error|". validation_errors(); $this->session->set_flashdata('message', validation_errors()); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
        //redirect($this->title.'/update/'.$param);
    }
    
    function confirmation($sid)
    {
        $sales = $this->Sales_model->get_by_id($sid)->row();
	$this->session->set_userdata('langid', $sales->id);
        
        echo $sid.'|'.$sales->sender_name.'|'.$sales->sender_acc.'|'.$sales->sender_bank.'|'.$sales->sender_amount.'|'.$sales->bank_id.'|'.$sales->confirmation.'|'.
             tglin($sales->paid_date).'|'.date("H:i:s", $sales->paid_date);
    }
    
    function payment_confirmation()
    {
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'sales_form';
	$data['link'] = array('link_back' => anchor('category/','<span>back</span>', array('class' => 'back')));

	// Form validation
        $this->form_validation->set_rules('tcdates', 'Confirmation Date', 'required');
        $this->form_validation->set_rules('taccname', 'Account Name', 'required');
        $this->form_validation->set_rules('taccno', 'Account No', 'required');
        $this->form_validation->set_rules('taccbank', 'Account Bank', 'required');
        $this->form_validation->set_rules('tamount', 'Amount', 'required|numeric');
        $this->form_validation->set_rules('cbank', 'Merchant Bank', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            if ($this->input->post('cstts') == '1'){
                $sales = array('confirmation' => 1, 'updated' => date('Y-m-d H:i:s'));
                $stts = 'confirmed!';
                // lakukan action pengurangan stock
                $this->change_product($this->session->userdata('langid'));
                
                $this->Sales_model->update($this->session->userdata('langid'), $sales);
                // lakukan action email ke customer
                $status = $this->mail_invoice($this->session->userdata('langid'));
            }
            else { $sales = array('confirmation' => 0, 'updated' => date('Y-m-d H:i:s')); 
                   $stts = 'unconfirmed!'; 
                   // lakukan action pengurangan stock
                   $this->change_product($this->session->userdata('langid'),0);
                $status = true;
                $this->Sales_model->update($this->session->userdata('langid'), $sales);
            }
            
            if ($status == true){
               echo "true|One $this->title data payment successfully ".$stts;  
            }else { echo "error|Error Sending Mail...!! ";   }
        }
        else{ echo "error|". validation_errors(); $this->session->set_flashdata('message', validation_errors()); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; } 
    }
    
    private function change_product($sid,$type=1)
    {
        $item = $this->sitem->get_last_item($sid)->result();
        if ($type==1){ foreach ($item as $res) { $this->product->min_qty($res->product_id,$res->qty); } }
        else{ foreach ($item as $res) { $this->product->add_qty($res->product_id,$res->qty); } }
    }
    
    function valid_product($id,$sid)
    {
        if ($this->sitem->valid_product($id,$sid) == FALSE)
        {
            $this->form_validation->set_message('valid_product','Product already listed..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_name($val)
    {
        if ($this->Sales_model->valid('name',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_name','Name registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_confirm($sid,$type='id')
    {
        if ($this->Sales_model->valid_confirm($sid,$type) == FALSE)
        {
            $this->form_validation->set_message('valid_confirm','Sales Already Confirmed..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function cek_orderid($orderid){
        
        if ($this->Sales_model->valid_orderid($orderid) == FALSE){ return FALSE; }else{ return TRUE; }
    }
    
    function valid_items($sid)
    {
        if ($this->sitem->valid_items($sid) == FALSE)
        {
            $this->form_validation->set_message('valid_items',"Empty Transaction..!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function report_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $data['rundate'] = tglin(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');
        $period = $this->input->post('reservation');  
        $start = picker_between_split($period, 0);
        $end = picker_between_split($period, 1);
        $shipped = $this->input->post('cshipped');

        $data['start'] = tglin($start);
        $data['end'] = tglin($end);
        
//        Property Details
        $data['company'] = $this->properti['name'];
        $data['reports'] = $this->Sales_model->report($start,$end)->result();
//        
        if ($this->input->post('ctype') == 0){ $this->load->view('sales_report', $data); }
        else { $this->load->view('sales_pivot', $data); }
    }   

}

?>