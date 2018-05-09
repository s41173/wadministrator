<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Prodesc extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Prodesc_model', '', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));

        $this->category = $this->load->library('categoryproduct');
        $this->load->helper('ckeditor');
        $this->load->helper('editor');
    }

    private $properti, $modul, $title;
    private $category;

    function index()
    { $this->get_description(null); }


    function get_description($pid=null)
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'].' PRO-0'.$pid;
        $data['main_view'] = 'prodesc_view';
	$data['form_action'] = site_url($this->title.'/add_process/'.$pid);
        $data['link'] = array('link_back' => anchor('product','<span>back</span>', array('class' => 'back')));
        $data['pid'] = $pid;
        $data['ckeditor'] = editor();

        $prodescs = $this->Prodesc_model->get_last_prodesc($pid)->result();

        $tmpl = array('table_open' => '<table class="tablemaster table table-bordered">');
        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('No', 'Product', 'Category', 'Action');

        $i = 0;
        foreach ($prodescs as $prodesc)
        {
            $this->table->add_row
            (
                ++$i, 'PRO-0'.$prodesc->product, $this->get_category($prodesc->category),
                anchor($this->title.'/update/'.$prodesc->id,'<span>details</span>',array('class' => 'edit', 'title' => '')).' '.
                anchor($this->title.'/delete/'.$prodesc->id,'<span>delete</span>',array('class'=> 'delete', 'title' => 'delete' ,'onclick'=>"return confirm('Are you sure you will delete this data?')"))
            );
        }

        $data['table'] = $this->table->generate();

	$this->load->view('template', $data);
    }

    private function get_category($val)
    {
        switch ($val)
        {
            case "a": $val = 'Feature & Benefit'; break;
            case "b": $val = 'Interior & Exterior Finish Option'; break;
            case "c": $val = 'Design Pattern & Grilles'; break;
            case "d": $val = 'Hardware & Accessoriess'; break;
        }
        return $val;
    }


    function delete($uid)
    {
        $this->acl->otentikasi_admin($this->title);
        $prodesc = $this->Prodesc_model->get_prodesc_by_id($uid)->row();

        $this->Prodesc_model->delete($uid);
        $this->session->set_flashdata('message', "1 $this->title successfully removed..!");
        redirect($this->title.'/get_description/'.$prodesc->product);
    }

    function add_process($pid=null)
    {
        $this->acl->otentikasi2($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'prodesc_view';
	$data['form_action'] = site_url($this->title.'/add_process');
	$data['link'] = array('link_back' => anchor($this->title.'/get_description/'.$pid,'<span>back</span>', array('class' => 'back')));

        $data['pid'] = $pid;
        $data['ckeditor'] = editor();

	// Form validation
        $this->form_validation->set_rules('ccategory', 'Category Person', 'required|callback_valid_prodesc['.$pid.']');
        $this->form_validation->set_rules('tdesc', 'Description', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {                
            $prodesc = array('product' => $pid, 'category' => $this->input->post('ccategory'), 'desc' => $this->input->post('tdesc'));

            $this->Prodesc_model->add($prodesc);
            $this->session->set_flashdata('message', "One $this->title data successfully saved!");
            redirect($this->title.'/get_description/'.$pid);
        }
        else
        { 
           $this->load->view('template', $data);
        }

    }

    
    function update($uid)
    {
        $this->acl->otentikasi2($this->title);
        $prodesc = $this->Prodesc_model->get_prodesc_by_id($uid)->row();

        $data['h2title'] = $this->modul['title'].' PRO-0'.$prodesc->product;
        $data['form_action'] = site_url($this->title.'/update_process/');
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['main_view'] = 'prodesc_view';
        $data['link'] = array('link_back' => anchor($this->title.'/get_description/'.$prodesc->product,'<span>back</span>', array('class' => 'back')));
        $data['ckeditor'] = editor();

        $data['pid'] = $prodesc->product;
        $data['default']['category'] = $prodesc->category;
        $data['default']['desc'] = $prodesc->desc;

        $this->session->set_userdata('curid', $prodesc->id);
	$this->load->view('template', $data);
    }

        // Fungsi update untuk mengupdate db
    function update_process()
    {
        $this->acl->otentikasi3($this->title);
        $prodescs = $this->Prodesc_model->get_prodesc_by_id($this->session->userdata('curid'))->row();

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'prodesc_view';
	$data['form_action'] = site_url($this->title.'/update_process');
	$data['link'] = array('link_back' => anchor('prodesc/update/'.$prodesc->product,'<span>back</span>', array('class' => 'back')));

        $data['pid']      = $prodesc->product;
        $data['ckeditor'] = editor();

	// Form validation
        $this->form_validation->set_rules('ccategory', 'Category Person', 'required|callback_validation_prodesc['.$prodesc->product.']');
        $this->form_validation->set_rules('tdesc', 'Description', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $prodesc = array('category' => $this->input->post('ccategory'), 'desc' => $this->input->post('tdesc'));
            
	    $this->Prodesc_model->update($this->session->userdata('curid'), $prodesc);
            $this->session->set_flashdata('message', "One $this->title data successfully saved!");
            redirect($this->title.'/get_description/'.$prodescs->product);
            $this->session->unset_userdata('curid');
        }
        else
        { $this->load->view('template', $data); }
    }

    public function valid_prodesc($category,$pid)
    {
        if ($pid != null)
        {
            if ($this->Prodesc_model->valid_prodesc($category,$pid) == FALSE)
            {
                $this->form_validation->set_message('valid_prodesc', "This $this->title is already registered.!");
                return FALSE;
            }
            else { return TRUE; }
        }
        else { $this->form_validation->set_message('valid_prodesc', "Invalid product Id..!"); }  
    }

    function validation_prodesc($category,$pid)
    {
	$id = $this->session->userdata('curid');
	if ($this->Prodesc_model->validating_prodesc($category,$pid,$id) == FALSE)
        {
            $this->form_validation->set_message('validation_prodesc', 'This prodesc is already registered!');
            return FALSE;
        }
        else{ return TRUE; }
    }

}

?>