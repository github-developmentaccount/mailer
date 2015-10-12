<?php if( !defined('BASEPATH')) exit('Error');

class Rules_model extends CI_Model
{
	public $sending_rules = [ ['field'=>'to',
						   'label' => 'Получатель',
						   'rules' => 'required|valid_email|trim'
						   ],
						   ['field'=>'theme',
						   'label' => 'Тема',
						   'rules' => 'required|xss_clean|trim'
						   ],
						   ['field'=>'text',
						   'label' => 'Текст сообщения',
						   'rules' => 'required|xss_clean|min_length[5]|trim'
						   ]
	                    ];

	public $auth_rules = [
						  ['field' => 'email', 
						   'label' => 'Email',
						   'rules' => 'required|valid_email|trim'
						   ],
						   ['field' => 'pass',
						    'label' => 'Пароль',
						    'rules' => 'required|min_length[3]'
						   ],
						   ['field' => 'uname',
						    'label' => 'Ваше имя',
						    'rules' => 'required|min_length[3]'
						   ]
						 ];

}