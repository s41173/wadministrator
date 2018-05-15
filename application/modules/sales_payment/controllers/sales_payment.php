<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'definer.php';

class Sales_payment extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Sales_payment_model', 'model', TRUE);

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
        $this->sms = new Sms_lib();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token'); 
        
    }

    private $properti, $modul, $title, $sales, $bank;
    private $role, $currency, $payment, $sms;
    
    function index()
    {
       $this->get_last(); 
    }
            
    // json function
    
    private function valid_json($param){
        
      if (isset($param['phase']) && isset($param['date']) && isset($param['acc_name']) && isset($param['acc_no']) 
          && isset($param['acc_bank']) && isset($param['amount']) && isset($param['sales_order']) && isset($param['bank']))
      { return TRUE; }else{ return FALSE; }
    }
    
    function add_json_process()
    {
        $datax = (array)json_decode(file_get_contents('php://input')); 
        
        $result = true;
        $error = null;

        $sid = $this->sales->get_id_based_order($datax['sales_order']);
        if ($this->valid_json($datax) == TRUE && $sid != FALSE)
        {
            if ($this->valid_confirm($sid) == TRUE){
             
                $orderid = $this->model->counter().mt_rand(100,9999);
                
                $sales = array('phase' => $datax['phase'], 'sales_id' => $sid,
                               'code' => $orderid, 'dates' => $datax['date'],
                               'sender_name' => $datax['acc_name'], 'sender_acc' => $datax['acc_no'],
                               'sender_bank' => $datax['acc_bank'], 'sender_amount' => floatval($datax['amount']),
                               'amount' => floatval($datax['amount']), 'bank_id' => $datax['bank'],
                               'created' => date('Y-m-d H:i:s'));
            
                $this->model->add($sales);
                
                $id = $this->model->counter(1);
                $this->send_notif_backend($id);
                $error = "Payment Confirmation Submitted..!!";

            }else{ $result = false; $error = 'Sales Order Not Confirmed..!';  }   
        }
        else{ $result = false; $error = 'Invalid JSON Format & Invalid Sales Order..!'; }
        
        $status = array('result' => $result, 'error' => $error);
        $response['status'] = $status;
            
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,128))
            ->_display();
            exit;  
        
    }
    
    private function send_notif_backend($uid=0){
        
        $val = $this->model->get_by_id($uid)->row();
        $sales = $this->sales->get_by_id($val->sales_id)->row();
        
        // email send
        $this->load->library('email');
        $config['charset']  = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';

        $this->email->initialize($config);
        $this->email->from($this->properti['email'], $this->properti['name']);
        $this->email->to($this->properti['billing_email']);
        $this->email->cc($this->properti['cc_email']); 
        
//        $html = $this->load->view('agent_confirmation',$data,true); 
        $html = "Payment Confirmation - <strong> Sales Code : ".$sales->code." </strong> <br> Transcode : ".$val->code." <br> Transaction Date : ".tglincomplete($val->dates)." <br> Amount : Rp ". idr_format($val->amount);
        
        $this->email->subject('Notification - Payment Confirmation - #'.$sales->code);
        $this->email->message($html);

        if (!$this->email->send()){ return FALSE; }else{ return TRUE; }
        
    }
    
    // json process
    
    function get_last($sid=null)
    {
        $this->acl->otentikasi1($this->title);

        if ($sid){ $this->session->set_userdata('sid', $sid); }
        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'product_list';

        $salespayment = $this->model->get_last($sid)->result();   
        $sales = $this->sales->get_detail_sales($sid);
        $data['code'] = $sales->code;   
        $data['bank'] = $this->bank->combo();
        
        $tmpl = array('table_open' => '<table id="example" width="100%" cellspacing="0" class="table table-striped table-bordered">');

            $this->table->set_template($tmpl);
            $this->table->set_empty("&nbsp;");

            //Set heading untuk table
            $this->table->set_heading('No', 'Code', 'Sales', 'Date', 'Phase', 'Bank', 'Amount', 'Action');

            $i = 0;
            if ($salespayment){

                foreach ($salespayment as $res)
                {
                   if ($res->confirmation == 1){ $stts = 'success'; $text = 'Unconfirmed'; }else{ $stts = 'danger'; $text = 'Confirmed'; } 
                   $datax = array('name' => 'button', 'type' => 'button', 'id' => $res->id, 'class' => 'btn btn-'.$stts.' btn-xs text-confirmation', 'content' => $text);
                   $atts = array('name' => 'link', 'class' => 'btn btn-danger btn-xs');
                   $bank = $this->bank->get_details($res->bank_id, 'acc_no').' : '.$this->bank->get_details($res->bank_id, 'acc_name');
                   
                    $this->table->add_row
                    (
                        ++$i, strtoupper($res->code), $sales->code, tglin($res->dates), $res->phase,
                        $bank, idr_format($res->amount),
                        form_button($datax).'  '.anchor($this->title.'/delete/'.$res->id.'/'.$res->sales_id, 'Remove', $atts)
                    );
                }            
            }

            $data['table'] = $this->table->generate();
            $this->load->view('sales_payment_list', $data);
    }

    function delete($uid,$sid)
    {
        if ($this->acl->otentikasi_admin($this->title) == TRUE){
            
            $confirm = $this->model->get_by_id($uid)->row();
            
            if ($confirm->confirmation == 0){
               $this->model->force_delete($uid);    
               $this->session->set_flashdata('message', "1 $this->title successfully removed..!");
            }else{ $this->session->set_flashdata('message', "rollback transaction..!"); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
        redirect($this->title.'/get_last/'.$sid);
    }
    
    function add_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tcdates', 'Confirmation Date', 'required');
        $this->form_validation->set_rules('taccname', 'Account Name', 'required');
        $this->form_validation->set_rules('taccno', 'Account No', 'required');
        $this->form_validation->set_rules('taccbank', 'Account Bank', 'required');
        $this->form_validation->set_rules('tamount', 'Amount', 'required|numeric');
        $this->form_validation->set_rules('cbank', 'Merchant Bank', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            if ($this->valid_confirm($this->session->userdata('sid')) == TRUE){
                
                $orderid = $this->model->counter().mt_rand(100,9999);
                
                $sales = array('phase' => $this->input->post('cphase'), 'sales_id' => $this->session->userdata('sid'), 
                               'code' => $orderid, 'dates' => $this->input->post('tcdates'),
                               'sender_name' => $this->input->post('taccname'), 'sender_acc' => $this->input->post('taccno'),
                               'sender_bank' => $this->input->post('taccbank'), 'sender_amount' => $this->input->post('tamount'),
                               'amount' => $this->input->post('tamount'),
                               'bank_id' => $this->input->post('cbank'), 'confirmation' => $this->input->post('cstts'),
                               'updated' => date('Y-m-d H:i:s'));
            
                $this->model->add($sales);
                $id = $this->model->counter(1);
                $this->send_notif_backend($id);
                echo "true|One $this->title data payment successfully";
            }else{ echo "error|Sales Order Not Confirmed..!"; }   
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }

    function confirmation()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tcdates', 'Confirmation Date', 'required');
        $this->form_validation->set_rules('taccname', 'Account Name', 'required');
        $this->form_validation->set_rules('taccno', 'Account No', 'required');
        $this->form_validation->set_rules('taccbank', 'Account Bank', 'required');
        $this->form_validation->set_rules('tamount', 'Amount', 'required|numeric');
        $this->form_validation->set_rules('cbank', 'Merchant Bank', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $sales = $this->model->get_by_id($this->session->userdata('langid'))->row();
            $sid = $sales->sales_id;
            
            if ($this->valid_confirm($sid) == TRUE){
                
                $sales = array('phase' => $this->input->post('cphase'), 'dates' => $this->input->post('tcdates'),
                           'sender_name' => $this->input->post('taccname'), 'sender_acc' => $this->input->post('taccno'),
                           'sender_bank' => $this->input->post('taccbank'), 'sender_amount' => $this->input->post('tamount'),
                           'amount' => $this->input->post('tamount'),
                           'bank_id' => $this->input->post('cbank'), 'confirmation' => $this->input->post('cstts'),
                           'updated' => date('Y-m-d H:i:s'));
                
                if ($this->input->post('cstts') == 1){ $this->send_invoice_email($this->session->userdata('langid')); }
                $this->model->update($this->session->userdata('langid'), $sales);
                echo "true|One $this->title data payment successfully";
            }else{ echo "error|Sales Order Not Confirmed..!"; }   
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
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
       
       $salespayment = $this->model->get_by_id($param)->row();
       $sales = $this->sales->get_by_id($salespayment->sales_id)->row();
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
        
        $this->email->subject('Payment Confirmation - #'.strtoupper($sales->code));
        $this->email->message($html);

        if (!$this->email->send()){ echo 'error|Failed to sent invoice..!!'; }else{ $this->send_confirmation_sms($param); echo 'true|Invoice sent..!!';  }
    }
    
     private function send_confirmation_sms($param){
       
       $salespayment = $this->model->get_by_id($param)->row();
       $sales = $this->sales->get_by_id($salespayment->sales_id)->row();
       $customer = $this->customer->get_details($sales->cust_id)->row();
        
        $amount = idr_format($salespayment->amount);
        $mess = "Konfirmasi pembayaran anda untuk tagihan #".$sales->code." sebesar ".$amount.",- sudah diterima. Mohon periksa email anda untuk informasi lebih lanjut.";
        return $this->sms->send($customer->phone1, $mess);
    }
    
    function invoice($param=0,$type='invoice')
    {
        $data['title'] = $this->properti['name'].' | Invoice '.ucwords($this->modul['title']).' | SO-0'.$param;
        $salespayment = $this->model->get_by_id($param)->row();
        $sales = $this->sales->get_by_id($salespayment->sales_id)->row();
        
        if ($sales){
                
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

            // sales
            $data['so'] = $sales->code.'/'.get_month_romawi(date('m', strtotime($sales->dates))).'/'.date('Y', strtotime($sales->dates));
            $data['socode'] = $sales->code;
            $data['code'] = $salespayment->code;
            $data['dates'] = tglincomplete($salespayment->dates);
            $data['phase'] = $salespayment->phase;
            $data['amount'] = idr_format($salespayment->amount);
            $data['sender_name'] = strtoupper($salespayment->sender_name);
            $data['sender_acc'] = $salespayment->sender_acc;
            $data['sender_bank'] = $salespayment->sender_bank;
            $data['bank'] = $this->bank->get_details($salespayment->bank_id,'acc_bank').' - '.$this->bank->get_details($salespayment->bank_id,'acc_no').' : '.$this->bank->get_details($salespayment->bank_id,'acc_name');


            if ($type == 'invoice'){ $this->load->view('sales_confirmation_email', $data); }
            else{
                $html = $this->load->view('sales_confirmation_email', $data, true); // render the view into HTML
                return $html;
            }
        }
    }
    
    function update($uid){
        
        $val = $this->model->get_by_id($uid)->row();
        $this->session->set_userdata('langid', $uid);
        echo $val->dates."|".$val->sender_name.'|'.$val->sender_acc.'|'.$val->sender_bank.'|'.$val->sender_amount.'|'.$val->confirmation.'|'.$val->bank_id.'|'.$val->phase;
    }
    
    private function update_trans($sid)
    {
        $totals = $this->sitem->total($sid);
        $price = intval($totals['amount']);
        
        // shipping total        
        $transaction = array('tax' => $totals['tax'], 'total' => $price, 'amount' => intval($totals['tax']+$price), 'shipping' => $this->shipping->total($sid));
	$this->Sales_model->update($sid, $transaction);
    }
    
    
    private function mail_invoice($pid)
    {   
        // property display
       $data['p_logo'] = $this->properti['logo'];
       $data['p_name'] = $this->properti['name'];
       $data['p_site_name'] = $this->properti['sitename'];
       $data['p_address'] = $this->properti['address'];
       $data['p_zip'] = $this->properti['zip'];
       $data['p_city'] = $this->properti['city'];
       $data['sites_url'] = constant("BASE_URL");
       
       $sales = $this->Sales_model->get_by_id($pid)->row();
       $cust = $this->customer->get_details($sales->cust_id)->row();
       $shipping = $this->shipping->get_detail_based_sales($pid);
      
       $data['so_no']   = 'DISO-0'.$pid;
       $data['so_date'] = tglin($sales->dates).' '. timein($sales->dates);
       $data['c_name'] = ucfirst($cust->first_name.' '.$cust->last_name);
       $data['c_phone'] = $cust->phone1.' / '.$cust->phone2;
       $data['payment'] = $this->payment->get_name($sales->payment_id);
       $data['courier'] = strtoupper($shipping->courier);
       $data['package'] = strtoupper($shipping->package);
       $data['ship_address'] = $shipping->dest_desc;
       $data['sub_total'] = num_format($sales->amount);
       $data['shipping_amt'] = num_format($sales->shipping);
       $data['total'] = num_format(floatval($sales->amount+$sales->shipping));
       
       $data['item'] = $this->sitem->get_last_item($pid)->result();
       
       if($sales->confirmation == 0){ 
          $data['status'] = 'Pending'; 
          $html = $this->load->view('sales_order_credit',$data,true);
          $subject = 'Konfirmasi Pesanan - '.$data['so_no'].' - '.$data['p_name'];
       }else{ $data['status'] = 'Lunas'; 
         $html = $this->load->view('sales_order_lunas',$data,true); 
         $subject = 'Pembayaran Sukses - '.$data['so_no'].' - '.$data['p_name'];
       }
         
        // email send
        $this->load->library('email');
        $config['charset']  = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';

        $this->email->initialize($config);
        $this->email->from($this->properti['billing_email'], $this->properti['name']);
        $this->email->to($cust->email);
        $this->email->cc($this->properti['cc_email']); 

        $this->email->subject($subject);
        $this->email->message($html);
//        $pdfFilePath = FCPATH."/downloads/".$no.".pdf";

        if (!$this->email->send()){ return false; }else{ return true;  }
    }
    
    function valid_confirm($sid)
    {
        $approved = $this->sales->get_detail_sales($sid);
        
        if ($approved->approved == 0)
        {
            $this->form_validation->set_message('valid_confirm','Sales Order Not Confirmed..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
}

?>