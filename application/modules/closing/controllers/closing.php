<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Closing extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Closing_model', '', TRUE);
        
        $this->properti = $this->property->get();
        $this->acl->otentikasi();
        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));

        $this->user = $this->load->library('admin_lib');
        $this->component = new Components();
        $this->period = new Period_lib();
        $this->period = $this->period->get();
        $this->balance = new Balance_lib();
        $this->customer = new Customer_lib();
        $this->ledger = new Wallet_ledger_lib();
    }

    private $atts = array('width'=> '800','height'=> '600',
                      'scrollbars' => 'yes','status'=> 'yes',
                      'resizable'=> 'yes','screenx'=> '0','screenx' => '\'+((parseInt(screen.width) - 800)/2)+\'',
                      'screeny'=> '0','class'=> 'print','title'=> 'print', 'screeny' => '\'+((parseInt(screen.height) - 600)/2)+\'');

    private $properti, $modul, $title, $component,$balance;
    private $user,$customer,$period, $ledger;

    function index()
    { 
        $this->monthly(); 
    }
    
    function calculate($page='main')
    {
        $customer = $this->customer->get()->result();
        
        $res = null;
        foreach ($customer as $res)
        {    
            $next = $this->next_period();  

            $beginning = $this->balance->get($res->id, $this->period->month, $this->period->year);
            if ($beginning){ $beginning = floatval($beginning->beginning); }else{ $beginning = 0; }          
            $trans = $this->ledger->get_sum_transaction_monthly($res->id, $this->period->month, $this->period->year);
            $trans = floatval($trans['vamount']);
            
            $this->balance->create($res->id, $this->period->month, $this->period->year, 0, $beginning+$trans);
            $this->balance->create($res->id, $next[0], $next[1], $beginning+$trans);         
        }
        
        $this->session->set_flashdata('message', "Calculating Ending Balance Sucessed..!");
        redirect($page);    
    }
    
    function monthly()
    {   
        $customer = $this->customer->get()->result();
        
        $res = null;
        foreach ($customer as $res)
        {    
            $next = $this->next_period();  

            $beginning = $this->balance->get($res->id, $this->period->month, $this->period->year);
            if ($beginning){ $beginning = floatval($beginning->beginning); }else{ $beginning = 0; }          
            $trans = $this->ledger->get_sum_transaction_monthly($res->id, $this->period->month, $this->period->year);
            $trans = floatval($trans['vamount']);
            
            $this->balance->create($res->id, $this->period->month, $this->period->year, 0, $beginning+$trans);
            $this->balance->create($res->id, $next[0], $next[1], $beginning+$trans);         
        }

        //  update periode akuntansi
        $ps = new Period_lib();
        $ps->update_period($next[0], $next[1]);
        $this->session->set_flashdata('message', "Monthly End Sucessed..!");
        redirect('main');    
    }
    
    private function next_period()
    {
        $month = $this->period->month;
        $year = $this->period->year;
        
        if ($month == 12){$nmonth = 1;}else { $nmonth = $month +1; }
        if ($month == 12){ $nyear = $year+1; }else{ $nyear = $year; }
        $res[0] = $nmonth; $res[1] = $nyear;
        return $res;
    }
    
}

?>
