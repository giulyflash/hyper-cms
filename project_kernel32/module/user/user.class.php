<?php
class user_config extends module_config{
	protected $default_method = 'form';
	public $registration = false;
	protected $output_config = true;
	protected $include = array(
		'*'=>'<link href="/module/user/user.css" rel="stylesheet" type="text/css">',
		'form'=>'<link href="/module/user/user.css" rel="stylesheet" type="text/css">',
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
			$this->redirect();
			//TODO redirect to proper place, not admin.php
		}
		else
			$this->_message('Неверное имя пользователя или пароль.');
			///FIXME убрать русские слова
	}
	
	public function logout(){
		$this->redirect(true);
	}
	
	private function redirect($unset=NULL){
		if(isset($_SESSION['call'][1]))
			$redirect = $_SESSION['call'][1];
		else
			$redirect = $this->admin_mode?'/admin.php':'/';
		if($unset)
			session_unset();
		$this->parent->redirect($redirect);
	}
	
	public function _admin(){
		
	}
}
?>