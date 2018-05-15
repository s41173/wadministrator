<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'definer.php';

class Shipping extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Shipping_model', '', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->role = new Role_lib();
        $this->currency = new Currency_lib();
        $this->customer = new Customer_lib();
        $this->payment = new Payment_lib();
        $this->city = new City_lib();
        $this->product = new Product_lib();
        $this->sales = new Sales_lib();
        $this->shipping = new Shipping_lib();
        $this->shiprate = new Shiprate_lib();
    }

    private $properti, $modul, $title;
    private $role, $currency, $customer, $payment, $city, $product, $sales, $shipping, $shiprate;

    function index()
    {
       $this->get_last(); 
    }
     
    public function getdatatable($search=null,$cust='null',$payment='null',$ship='null')
    {
        if(!$search){ $result = $this->Shipping_model->get_last($this->modul['limit'])->result(); }
        else {$result = $this->Shipping_model->search($cust,$payment,$ship)->result(); }
	
        $output = null;
        if ($result){
                
         foreach($result as $res)
	 {
           if ($res->status == 1){ $paid = 'S'; }else{ $paid = 'C'; } 
           if ($res->shipdate){ $shipped = 'Y'; }else{ $shipped = 'N'; } 
           
           // sales date
           $sales = $this->sales->get_detail_sales($res->sales_id);
           
	   $output[] = array ($res->id, 
                              $sales->code, // sales no
                              tglin($sales->dates), // sales date
                              $this->customer->get_name($sales->cust_id), // customer
                              tglin($res->shipdate), // shipping date
                              strtoupper($res->courier), // courier
                              $res->awb, // awb
                              $res->dest, // destination
                              $res->package, // package
                              $res->rate, // rate
                              $res->weight, // weight
                              idr_format($res->amount),
                              $res->status, // paid status 
                              $shipped // shipped status
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
        $data['h2title'] = 'Shipping Order';
        $data['main_view'] = 'shipping_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all/hard');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['form_action_import'] = site_url($this->title.'/import');
        $data['form_action_confirmation'] = site_url($this->title.'/payment_confirmation');
        $data['link'] = array('link_back' => anchor('sales/','Back', array('class' => 'btn btn-danger')));

        $data['customer'] = $this->customer->combo();
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
        $this->table->set_heading('#', 'No', 'Sales', 'Cust', 'Ship-Date', 'Courier', 'Balance', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable/');
        $data['graph'] = site_url()."/shipping/chart/".$this->input->post('cmonth').'/'.$this->input->post('tyear');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    function chart($month=null,$year=null)
    {   
        $data = $this->category->get();
        $datax = array();
        foreach ($data as $res) 
        {  
           $tot = $this->Shipping_model->get_shipping_qty_based_category($res->id,$month,$year); 
           $point = array("label" => $res->name , "y" => $tot);
           array_push($datax, $point);      
        }
        echo json_encode($datax, JSON_NUMERIC_CHECK);
    }
    
    function publish($uid = null)
    {
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
//       $val = $this->Shipping_model->get_by_id($uid)->row();
//       if ($val->approved == 0){ $lng = array('approved' => 1); }else { $lng = array('approved' => 0); }
//       $this->Shipping_model->update($uid,$lng);
//       echo 'true|Status Changed...!';
         echo "error|Please make payment confirmation transaction, to change this status...!";
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
             if ($type == 'soft') { $this->Shipping_model->delete($cek[$i]); }
             else { $this->shipping->delete_by_shipping($cek[$i]);
                    $this->Shipping_model->force_delete($cek[$i]);  
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
            $this->Shipping_model->delete($uid);
            
            $this->session->set_flashdata('message', "1 $this->title successfully removed..!");

            echo "true|1 $this->title successfully removed..!";
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
        
    }
    
    function add($param=0)
    {

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Create New '.$this->modul['title'];
        $data['main_view'] = 'shipping_form';
        if ($param == 0){$data['form_action'] = site_url($this->title.'/add_process'); $data['counter'] = $this->Shipping_model->counter(); }
        else { $data['form_action'] = site_url($this->title.'/update_process'); $data['counter'] = $param; }
	
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));
        $data['form_action_trans'] = site_url($this->title.'/add_item/0'); 
        $data['form_action_shipping'] = site_url($this->title.'/shipping/0'); 

        $data['customer'] = $this->customer->combo();
        $data['payment'] = $this->payment->combo();
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['graph'] = site_url()."/shipping/chart/";
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
        $this->form_validation->set_rules('tduedates', 'Transaction Due Date', 'required');
        $this->form_validation->set_rules('cpayment', 'Payment Type', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $shipping = array('cust_id' => $this->input->post('ccustomer'), 'dates' => date("Y-m-d H:i:s"),
                           'due_date' => $this->input->post('tduedates'), 'payment_id' => $this->input->post('cpayment'), 
                           'created' => date('Y-m-d H:i:s'));

            $this->Shipping_model->add($shipping);
            echo "true|One $this->title data successfully saved!|".$this->Shipping_model->counter(1);
            $this->session->set_flashdata('message', "One $this->title data successfully saved!");
//            redirect($this->title.'/update/'.$this->Shipping_model->counter(1));
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
                $shipping = array('product_id' => $this->input->post('cproduct'), 'shipping_id' => $sid,
                               'qty' => $this->input->post('tqty'), 'tax' => $tax, 'weight' => $this->product->get_weight($this->input->post('cproduct')),
                               'price' => $this->input->post('tprice'), 'amount' => intval($amt_price+$tax));

                $this->sitem->add($shipping);
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
        $price = intval($totals['qty']*$totals['price']);
        
        // shipping total        
        $transaction = array('tax' => $totals['tax'], 'total' => $price, 'amount' => intval($totals['tax']+$price), 'shipping' => $this->shipping->total($sid));
	$this->Shipping_model->update($sid, $transaction);
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
       
        $shipping = $this->Shipping_model->get_by_id($sid)->row();
           
         // Form validation
        $this->form_validation->set_rules('ccity', 'City', 'required');
        $this->form_validation->set_rules('tshipaddkurir', 'Shipping Address', 'required');
        $this->form_validation->set_rules('ccourier', 'Courier Service', 'required');
        $this->form_validation->set_rules('cpackage', 'Package Type', '');
        $this->form_validation->set_rules('tweight', 'Weight', 'required|numeric');

            if ($this->form_validation->run($this) == TRUE && $this->valid_confirm($sid) == TRUE)
            {
                $city = explode('|', $this->input->post('ccity'));
                $package = explode('|', $this->input->post('cpackage'));
                $param = array('shipping_id' => $sid, 'shipdate' => null,
                               'courier' => $this->input->post('ccourier'), 'dest' => $city[1], 'dest_id' => $city[0],
                               'dest_desc' => $this->input->post('tshipaddkurir'), 'package' => $package[0],
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
    function update($sid)
    {
        $shipping = $this->Shipping_model->get_by_id($sid)->row();
	$this->session->set_userdata('langid', $shipping->id);
        
        $sales = $this->sales->get_detail_sales($shipping->sales_id);
        
        echo $sid.'|'.$shipping->sales_id.'|'.tglin($sales->dates).'|'.strtoupper($shipping->courier).'|'.$shipping->package.'|'.$shipping->awb.'|'.
             $this->shiprate->get_city_name($shipping->dest).'|'.$shipping->dest_desc.'|'.$sales->code.'|'.$shipping->district;
    }
    
    function send_invoice_email($param)
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
       
       $shipping = $this->Shipping_model->get_by_id($param)->row();
       $sales = $this->sales->get_detail_sales($shipping->sales_id);
       $customer = $this->customer->get_details($sales->cust_id)->row();
         
        // email send
        $this->load->library('email');
        $config['charset']  = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';

        $this->email->initialize($config);
        $this->email->from($this->properti['email'], $this->properti['name']);
        $this->email->to($customer->email);
        $this->email->cc($this->properti['cc_email']); 
        
//        $html = $this->load->view('agent_confirmation',$data,true); 
        $html = $this->invoice($param,'html');
        
        $this->email->subject('Shipping Confirmation - '.strtoupper($sales->code));
        $this->email->message($html);

        if (!$this->email->send()){ echo 'error|Failed to sent invoice..!!'; }else{ echo 'true|Invoice sent..!!';  }
    }
    
        // Fungsi update untuk menset texfield dengan nilai dari database
    function invoice($param=0,$type='invoice')
    {
        $shipping = $this->Shipping_model->get_by_id($param)->row();
        $sales = $this->sales->get_detail_sales($shipping->sales_id);
        
        $data['title'] = $this->properti['name'].' | Invoice '.ucwords($this->modul['title']).' | SO-0'.$shipping->sales_id;
        
        if ($shipping){
                
            // property
            $data['p_name'] = $this->properti['sitename'];
            $data['p_address'] = $this->properti['address'];
            $data['p_city'] = $this->properti['city'];
            $data['p_zip']  = $this->properti['zip'];
            $data['p_phone']  = $this->properti['phone1'];
            $data['p_email']  = $this->properti['email'];
            $data['p_logo']  = $this->properti['logo'];

            // customer details
            $customer = $this->customer->get_details($sales->cust_id)->row();
            $data['c_name'] = strtoupper($customer->first_name.' '.$customer->last_name);
            $data['c_email'] = $customer->email;
            $data['c_address'] = $customer->shipping_address;
            $data['c_phone'] = $customer->phone1.' / '.$customer->phone2;
            $data['c_city'] = $this->city->get_name($customer->city);
            $data['c_zip'] = $customer->zip;

            // shipping
            $data['so'] = $sales->code;
            $data['dates'] = tglin($shipping->shipdate);

            if ($shipping->paid_date){ $data['paid'] = 'Paid'; }else { $data['paid'] = 'Unpaid'; }
            $data['total'] = $shipping->amount;

            // weight total
            $total = $this->sales->total($shipping->sales_id);
            $data['weight'] = $shipping->weight;

            // shipping details
            $shipping = $this->Shipping_model->get_by_id($param)->row();
            
            $data['ship_date'] = tglin($shipping->shipdate);
            $data['ship_time'] = timein($shipping->shipdate);
            $data['courier'] = strtoupper($shipping->courier);
            $data['package'] = $shipping->package;
            $data['awb'] = strtoupper($shipping->awb);
            $data['rate'] = $shipping->rate;
            $data['dest_desc'] = $shipping->dest_desc;
            $data['dest'] = $this->shiprate->get_city_name($shipping->dest);

            if (!$shipping->shipdate){ $data['ship_status'] = 'Not Shipped'; }else { $data['ship_status'] = 'Shipped'; } 

            // transaction table
            $data['items'] = $this->sales->get_transaction_sales($shipping->sales_id)->result();
            
             if ($type == 'invoice'){ $this->load->view('shipping_invoice', $data); }
             else{
                $html = $this->load->view('shipping_invoice', $data, true); // render the view into HTML
                return $html;
            }
        }
    }
    
    function update_process($param=null)
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'shipping_form';
        $data['form_action'] = site_url($this->title.'/update_process/'.$param); 
	$data['link'] = array('link_back' => anchor('category/','<span>back</span>', array('class' => 'back')));

	// Form validation
        $this->form_validation->set_rules('taddress', 'Address', 'required|callback_valid_shipdate');

        if ($this->form_validation->run($this) == TRUE)
        {
            $shipping = array('dest_desc' => $this->input->post('taddress'),'updated' => date('Y-m-d H:i:s'));

            $this->Shipping_model->update($this->session->userdata('langid'), $shipping);
            $this->session->set_flashdata('message', "One $this->title data successfully saved!");
            echo "true|One $this->title data successfully saved!|".$param;
        }
        else{ echo "error|". validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
        //redirect($this->title.'/update/'.$param);
    }
    
    function confirmation($sid)
    {
        $shipping = $this->Shipping_model->get_by_id($sid)->row();
	$this->session->set_userdata('langid', $shipping->id);
        
        echo $sid.'|'.$shipping->status.'|'.$shipping->shipdate.'|'.$shipping->awb;
    }
    
    function paid_confirmation($sid)
    {
        $shipping = $this->Shipping_model->get_by_id($sid)->row();
	$this->session->set_userdata('langid', $shipping->id);
        
        echo $sid.'|'.$shipping->paid_date;
    }
    
    function paid_confirmation_process()
    {
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tpdates', 'Paid Date', '');
        
        $shipdate = $this->Shipping_model->get_by_id($this->session->userdata('langid'))->row();
        $shipdate = $shipdate->shipdate;

        if ($this->form_validation->run($this) == TRUE)
        {  
            if ($this->input->post('tpdates') && $shipdate != null){
                $shipping = array('paid_date' => $this->input->post('tpdates'), 'status' => 1, 'updated' => date('Y-m-d H:i:s'));
                $stts = 'confirmed!';
            }
            else { $shipping = array('paid_date' => null, 'status' => 0, 'updated' => date('Y-m-d H:i:s')); 
                $stts = 'unconfirmed!';   
            }   
                
            $this->Shipping_model->update($this->session->userdata('langid'), $shipping);
            echo "true|One $this->title shipping status successfully ".$stts;
        }
        else{ echo "error|One $this->title shipping status successfully ".$stts; }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; } 
    }
    
    function payment_confirmation()
    {
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'shipping_form';
	$data['link'] = array('link_back' => anchor('category/','<span>back</span>', array('class' => 'back')));

	// Form validation
        $this->form_validation->set_rules('tcdates', 'Confirmation Date', 'required');

        if ($this->form_validation->run($this) == TRUE && $this->valid_payment_confirm($this->session->userdata('langid')) == TRUE)
        {  
            if ($this->input->post('cstts') == '1'){
                $shipping = array('shipdate' => $this->input->post('tcdates'), 'awb' => $this->input->post('tairway'), 'updated' => date('Y-m-d H:i:s'));
                $stts = 'confirmed!';
            }
            else { $shipping = array('shipdate' => null, 'updated' => date('Y-m-d H:i:s')); 
                $stts = 'unconfirmed!';   
            }   
                
            // lakukan action email ke customer
            $this->Shipping_model->update($this->session->userdata('langid'), $shipping);
            echo "true|One $this->title shipping status successfully ".$stts;
        }
        else{ echo "error|". validation_errors(). '- Shipping Status Already Confirmed'; }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; } 
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
        if ($this->Shipping_model->valid('name',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_name','Name registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_payment_confirm($id)
    {
        if ($this->Shipping_model->valid_payment_confirm($id) == FALSE)
        {
            $this->form_validation->set_message('valid_payment_confirm','Shipping Payment Already Confirmed..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_shipdate($val=null,$type=null)
    {
        if (!$type){ $param = $this->session->userdata('langid'); }else { $param = $val; }
        if ($this->Shipping_model->valid_shipdate($param) == FALSE)
        {
            $this->form_validation->set_message('valid_shipdate','Shipping Date Already Confirmed..!');
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
        $salesperiod = $this->input->post('salesperiod');  
        $shippingperiod = $this->input->post('shippingperiod');  
        
        $sales_start = picker_between_split($salesperiod, 0);
        $sales_end = picker_between_split($salesperiod, 1);
        
        $shipping_start = picker_between_split($shippingperiod, 0);
        $shipping_end = picker_between_split($shippingperiod, 1);
        
        $paid = $this->input->post('cpaid');

        $data['sales_start'] = tglin($sales_start);
        $data['sales_end'] = tglin($sales_end);
        
        $data['shipping_start'] = tglin($shipping_start);
        $data['shipping_end'] = tglin($shipping_end);
        
        if (!$paid){ $data['paid'] = ''; }elseif ($paid == 1){ $data['paid'] = 'Paid'; }else { $data['paid'] = 'Unpaid'; }
        
//        Property Details
        $data['company'] = $this->properti['name'];
        $data['reports'] = $this->Shipping_model->report($sales_start, $sales_end, $shipping_start, $shipping_end, $paid)->result();
        
        if ($this->input->post('ctype') == 0){ $this->load->view('shipping_report', $data); }
        else { $this->load->view('shipping_pivot', $data); }
    }   

}

?>