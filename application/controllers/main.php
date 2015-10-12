<?php if( ! defined('BASEPATH')) exit('No direct script access alowed');
ini_set('display_errors', 'Off');
class Main extends CI_Controller 
{
		
    var $inbox = 'INBOX';
    var $outgoing = '[Gmail]/&BB4EQgQ,BEAEMAQyBDsENQQ9BD0ESwQ1-';

    function __construct()
    {
    	parent::__construct();
    	$this->load->library('session');
    	$this->load->helper('url');
    	$this->load->library('imap');

    }

	public function index()
	{
		if(!$this->session->userdata('logged_in'))
		{ 
				$this->load->library('form_validation');		
				$this->load->view('header');
				if(isset($_POST['email']))
				{
					
					$this->load->model('rules_model');
					$this->form_validation->set_rules($this->rules_model->auth_rules);
					$flag = $this->form_validation->run();
						if($flag == true)
							{


								if($this->imap->cimap_open($_POST['email'], $_POST['pass'], $this->inbox))
								{

									$userdata = ['email' => $_POST['email'],
											 'password' => $_POST['pass'],
											 'username' => $_POST['uname'],
											 'logged_in' => TRUE
											 ];

									$this->session->set_userdata($userdata);
									redirect('main/inbox', 'refresh');

								}

								else 
								{
									$data['issue'] = 'Unvalid data entered!';
									$this->load->view('log', $data);
									return false;

								}

							} else 
								{
									$this->load->view('log');
								}
				} 
				else 
				{
					$this->load->view('log');

				}
			}
			else 
			{
				redirect('/main/inbox', 'refresh');
			}

	}

	public function inbox()
	{
		if($this->session->userdata('logged_in'))
		{		
				
		    	$inbox = $this->imap->cimap_open($this->session->userdata('email'), $this->session->userdata('password'), $this->inbox) or die(imap_last_error());
				$data['path'] = 'inbox';
				//view
				if ($this->uri->segment(3) === 'view' && preg_match('/^[0-9]+$/', $this->uri->segment(4))) 
					{
						$data['mid'] = $this->uri->segment(4);
    			   		$this->load->view('header');
    			   		$this->load->view('viewmail');
    			   		$this->load->view('single_view', $data);
    			   	

					} else
				 {
				//list_view

				    	$data['output'] = $this->imap->cimap_search($inbox);
		    		  	if($data['output'][0])
			    		  	{
			    		  		$this->load->view('header');
				    		  	$this->load->view('viewmail');
				    		  	$this->load->view('list_theme', $data);
			    		 	}
	    		 }
    		  		
    		  		
		} else
		{
			redirect('/main/', 'refresh');
		}
	}
	public function outgoing()
	{
		if($this->session->userdata('logged_in'))
		{		

			
			$inbox = $this->imap->cimap_open($this->session->userdata('email'), $this->session->userdata('password'), $this->outgoing) or die(imap_last_error());
			$data['path'] = 'outgoing';

			//view
				if ($this->uri->segment(3) == 'view' && preg_match('/^[0-9]+$/', $this->uri->segment(4))) 
					{
						$data['mid'] = $this->uri->segment(4,'');
    			   		$this->load->view('header');
    			   		$this->load->view('viewmail');
    			   		$this->load->view('single_view', $data);
    			   	

					} else {
			//endblock



				$data['output'] = $this->imap->cimap_search($inbox);
		
				if($data['output'][0])
				{
					$this->load->view('header');
					$this->load->view('viewmail');
					$this->load->view('list_theme', $data);
				}
			}

		} else
		{
			redirect('/main/', 'refresh');
		}

	}
	public function logout()
	{
		if($this->session->userdata('logged_in'))
		{
				$this->session->sess_destroy();
				$this->load->helper('url');
				redirect('/main/', 'refresh');
		} else 
		{
			redirect('/main/', 'refresh');
		}
	}


	public function send()
	{
		if($this->session->userdata('logged_in'))
		{
						//Подклюение header-a и класса валидации форм
						$this->load->library('form_validation');
						$this->load->view('header');
					
						//Валидация формы
						if(isset($_POST['theme']))
						{
						
							$this->load->model('rules_model');
							$this->form_validation->set_rules($this->rules_model->sending_rules);
							$flag = $this->form_validation->run();
								if($flag == TRUE)
								{
									//Отправка сообщения
											$config['protocol'] = 'smtp';
											$config['smtp_host'] = 'ssl://smtp.gmail.com';
											$config['smtp_port'] = '465';
											$config['smtp_user'] = $this->session->userdata('email');
											$config['smtp_pass'] = $this->session->userdata('password');
											$config['newline'] = "\r\n";
											$config['mailtype'] = 'text';
											$config['charset'] = 'utf-8';
				
											$this->load->library('email');
											$this->email->initialize($config);
				
											$this->email->from($this->session->userdata('email'), $this->session->userdata('name'));
											$this->email->to($_POST['to']);
											$this->email->subject($_POST['theme']);
											$this->email->message($_POST['text']);
				
											
											$data['status'] = (!$this->email->send()) ? 'Отправка не удалась! Сообщите нам об ошибке.' : 'Сообщение успешно отправлено!';
											$this->load->view('sendstatus', $data);
								} else
								{
									$this->load->view('sendmail');
								}
					} else {
						$this->load->view('sendmail');
					}		
		}
		else
			{
				redirect('/main/', 'refresh');
			}

	}
	

    public function singleview()
    {
		if($this->session->userdata('logged_in') && preg_match('/^[0-9]+$/',$this->uri->segment(4)))
		{
		    	
		    	$inbox = $this->imap->cimap_open($this->session->userdata('email'), $this->session->userdata('password'), $stream = ($this->uri->segment(3) == 'outgoing') ? $this->outgoing : $this->inbox) or die(imap_last_error());
		    	echo $this->imap->show_message($inbox, $this->uri->segment(4));
		} else
		{
			redirect('/main/', 'refresh');
		}
    }

    public function delete()
    {	
    	if($this->session->userdata('logged_in') && isset($_POST['mid']))
    	{
    	    	
    		    	
    		    	
    		    	$inbox = $this->imap->cimap_open($this->session->userdata('email'), $this->session->userdata('password'), $stream = ($_POST['path'] == 'outgoing') ? $this->outgoing : $this->inbox) or die(imap_last_error());
    		    	$data['error'] = $this->imap->cimap_delete($inbox, $_POST['mid']);
    		    	if(empty($data['error']))
    		    	{
    		    		echo '1';
    		    	}
    		    	else 
    		    	{
    		    		print_r($data['error']);
    		    		echo $_POST['path'];
    		    	}
    			
    	}
    }
    public function testing()
    {

    	
    	$inbox = $this->imap->cimap_open($this->session->userdata('email'), $this->session->userdata('password'), $this->outgoing);
    		    	
 		$emails = imap_search($inbox, 'ALL');

                if($emails)
                {

                   

	                    rsort($emails);
	                    $num = count($emails);
	                    if($num > 5)
	                    {
                        	$emails = array_slice($emails, 0, 5);
                       
                		}
                    
                    	print_r($emails);
                        
                }
                    

    }


}