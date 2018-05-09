<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'definer.php';

class Commision extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Commision_model', 'model', TRUE);

        $this->properti = $this->property->get();
//        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->role = new Role_lib();
        $this->currency = new Currency_lib();
        $this->sales = new Sales_lib();
        $this->customer = new Customer_lib();
        $this->payment = new Payment_lib();
        $this->bank = new Bank_lib();
        $this->agent = new Agent_lib();
        $this->sales_payment = new Sales_payment_lib();
        $this->sms = new Sms_lib();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token'); 
    }

    private $properti, $modul, $title, $sales, $bank, $sms;
    private $role, $currency, $customer, $payment, $agent, $sales_payment;
    
    function index()
    {
       redirect('sales');
    }
    
//    json process
    function get_commision_json(){
        
        $datax = (array)json_decode(file_get_contents('php://input')); 
        
        $output = null;
        $result = $this->model->search_json($datax['agent_id'])->result();
        
        foreach ($result as $res){
            
           $sales = $this->sales->get_detail_sales($res->sales_id);  
	   $output[] = array ("id" => $res->id, "sales_id" => $res->sales_id, "sales_code" => $sales->code, "code" => $res->code,
                              "dates" => tglin($res->dates), "phase" => $res->phase,  "amount" => idr_format($res->amount),
                              "payment" => $this->payment->get_name($res->payment_id), "bank" => $this->bank->get_details($res->bank_id, 'acc_name').' - '.$this->bank->get_details($res->bank_id, 'acc_no'), 
                              "confirmation" => $res->confirmation, "agent" => $this->agent->get_name($sales->agent_id)
                             );
            
        }
                
        $response['content'] = $output;
        
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response, 128))
        ->_display();
        exit;
        
    }
    
     
    public function getdatatable($search=null,$agent='null')
    {
        if(!$search){ $result = $this->model->get_last($this->session->userdata('sid'))->result();  }
        else {$result = $this->model->search($agent)->result(); }
	
        $output = null;
        if ($result){
                
         foreach($result as $res)
	 {
           $sales = $this->sales->get_detail_sales($res->sales_id);  
	   $output[] = array ($res->id, $res->sales_id, $sales->code, $res->code, tglin($res->dates), $res->phase,  idr_format($res->amount),
                              $this->payment->get_name($res->payment_id), $this->bank->get_details($res->bank_id, 'acc_name').' - '.$this->bank->get_details($res->bank_id, 'acc_no'), $res->confirmation,
                              $this->agent->get_name($sales->agent_id)
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

    function get_last($sid=0)
    {
        $this->acl->otentikasi1($this->title);
        
        if ($sid != 0){ $this->session->set_userdata('sid', $sid); }else{ redirect('sales'); }
        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Sales Order');
        $data['h2title'] = 'Commision Details';
        $data['main_view'] = 'commision_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all/hard');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['form_action_import'] = site_url($this->title.'/import');
        $data['form_action_confirmation'] = site_url($this->title.'/payment_confirmation');
        $data['link'] = array('link_back' => anchor('sales/','Back', array('class' => 'btn btn-danger')));

        $sales = $this->sales->get_detail_sales($sid);
        $data['agent'] = $this->agent->combo_name();
        $data['bank'] = $this->bank->combo();
        $data['payment'] = $this->payment->combo();
        $data['month'] = combo_month();
        $data['salescode'] = $sales->code;
        $data['sid'] = $sid;
        
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
        $this->table->set_heading('#','No', 'Code', 'Sales', 'Agent', 'Date', 'Phase', 'Balance', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable/');
        $data['graph'] = site_url()."/sales/chart/".$this->input->post('cmonth').'/'.$this->input->post('tyear');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    function publish($uid = null)
    {
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
       $val = $this->model->get_by_id($uid)->row();

       
       if ($val->confirmation == 0){ 
           
          if ($this->send_confirmation_sms($uid) == TRUE){
            $lng = array('confirmation' => 1);
            $mess = 'true|Sales Order Confirmed..!';
          }else{ $lng = array('confirmation' => 0); $mess = 'error|Failed to send confirmation..!'; }
       }    
       else { $lng = array('confirmation' => 0); $mess = 'true|Sales Order Unconfirmed..!'; }
       
       $this->model->update($uid,$lng);
       echo $mess;
       }else{ echo "error|Sorry, you do not have the right to change publish status..!"; }
    }
    
     private function send_confirmation_sms($sid){
       
        $val = $this->model->get_by_id($sid)->row();
        $sales = $this->sales->get_detail_sales($val->sales_id);
        $agent = $this->agent->get_by_id($sales->agent_id)->row();
        
        $amount = idr_format($val->amount);
        $mess = "Komisi penjualan anda dengan no order : ".$sales->code." tahap ".$val->phase." sebesar ".$amount.",- telah dibayarkan. Mohon periksa rekening anda untuk informasi lebih lanjut.";
        return $this->sms->send($agent->phone1, $mess);
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
            

            $this->model->force_delete($uid);
            $this->session->set_flashdata('message', "1 $this->title successfully removed..!");

            echo "true|1 $this->title successfully removed..!";
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
        
    }

    function add_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tsid', 'Sales ID', 'required');
        $this->form_validation->set_rules('cphase', 'Phase', 'required|callback_valid_phase['.$this->input->post('tsid').']');
        $this->form_validation->set_rules('tcdates', 'Transaction Date', 'required');
        $this->form_validation->set_rules('tamount', 'Amount', 'required|numeric');
        $this->form_validation->set_rules('cbank', 'Source Bank', 'required');
        $this->form_validation->set_rules('cpayment', 'Payment Type', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $orderid = $this->model->counter().mt_rand(100,9999);
            $sales = array('sales_id' => $this->input->post('tsid'), 'code' => $orderid, 'phase' => $this->input->post('cphase'),
                           'dates' => $this->input->post('tcdates'), 'amount' => $this->input->post('tamount'), 'payment_id' => $this->input->post('cpayment'),
                           'bank_id' => $this->input->post('cbank'), 'created' => date('Y-m-d H:i:s'));

            $this->model->add($sales);
            echo "true|One $this->title data successfully saved!|";
        }
        else{ echo "error|".validation_errors(); }
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
    
    function valid_phase($phase,$sid)
    {
        if ($this->model->valid_phase($phase,$sid) == FALSE)
        {
            $this->form_validation->set_message('valid_phase','Payment Phase registered..!');
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
    
    function report_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $data['rundate'] = tglin(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');
        $period = $this->input->post('reservation');  
        $start = picker_between_split($period, 0);
        $end = picker_between_split($period, 1);

        $data['start'] = tglin($start);
        $data['end'] = tglin($end);
        $data['sid'] = $this->session->userdata('sid');
        
//        Property Details
        $data['company'] = $this->properti['name'];
        $data['reports'] = $this->model->report($start,$end)->result();
        
        $this->load->view('commision_report', $data);
    }   

}

?>