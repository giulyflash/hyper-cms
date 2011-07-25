<?php
class user_config extends module_config{
	protected $default_method = 'form';
	public $registration = false;
	protected $output_config = true;
	protected $include = array(
		'*'=>'<link href="module/user/user.css" rel="stylesheet" type="text/css">',
		'form'=>'<link href="module/user/user.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="/module/user/form.js"></script>',
	);
	
	protected $callable_method=array(
		'form'=>array(
			'__access__' => array(
				__CLASS__ => self::role_read,
			),
		),
		'login'=>array(
			'__access__' => array(
				__CLASS__ => self::role_read,
			),
		),
		'logout'=>array(
			'__access__' => array(
				__CLASS__ => self::role_read,
			),
		),
	);
}

class user extends module{
	//const config_class_name = 'article_config';
	protected $config_class_name = 'user_config';
	
	public function form(){
	}
	
	public function login($login=NULL,$password=NULL){
		if($user_info = $this->_query->select('id,login,status,language')->from(__CLASS__)->where('login',$login)->_and('password',md5($password))->query1()){
			$_SESSION['user_info'] = $user_info;
			$this->parent->redirect($this->parent->admin_mode?'admin.php':'/');
			//TODO redirect to proper place, not admin.php
		}
		else
			$this->_message('Неверное имя пользователя или пароль.');
			///FIXME убрать русские слова
	}
	
	public function logout(){
		session_unset();
		//TODO redirect in the same place, not to root
		$this->parent->redirect($this->parent->admin_mode?'admin.php':'/');
	}
	
	public function _admin(){
		
	}
}
?>