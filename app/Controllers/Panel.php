<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\InstallModel;
use App\Models\UserModel;

class Panel extends BaseController
{
    
    protected $postBody; 
    protected $authModel;
	private $sessLogin;
	protected $installModel;
	protected $userModel;

    public function __construct()
    {
        $this->sessLogin = session();
        $this->authModel = new AuthHeaderModel(); //Consider using CI's way of initialising models
		$this->installModel = new InstallModel();
		$this->userModel = new UserModel();
        
    }

    public function index()
    {
        $this->sessLogin = session();	
		$check = $this->authModel->checkSession($this->sessLogin);
		if ($check) {

			$dataPanel = $this->installModel->getAllDataPanel();
			$datas['panel'] = $dataPanel; 
            return view('panel_view', $datas);
        }

        return view('login_view');
    }

    public function login()
	{
		$this->sessLogin = session();
		$em = $this->request->getVar('email');
		$ps = $this->request->getVar('password');

		if ($em != '' && $ps != ''){
			$passwd = $this->generatePassword($ps);
			$userLogin = $this->authModel->loginByEmail($em, $passwd);
			//print_r($userLogin);
			//die();
			
			//for admin@gmail.com password:  adminhobb2021     demo@gmail.com   password: userdemo2021
			if ($userLogin['id_userlogin'] != '') {
				$newdata = [
					'fullname_ss'  => $userLogin['fullname'],
					'username_ss'  => $userLogin['username'],
					'email_ss'     => $userLogin['email'],
					'user'		   => $userLogin,
					'logged_in'    => TRUE
				];
			}

			$this->authModel->addSession($this->sessLogin, $newdata);
		}
		
		$check = $this->authModel->getDataSession($this->sessLogin);
		
		return redirect()->to(base_url() . '/panel'); 
	}

	public function logout()
	{
		$this->sessLogin = session();
		$this->authModel->removeSession($this->sessLogin);
		return redirect()->to(base_url() . '/panel'); 
	}

    private function generatePassword($password) {
        return md5(sha1(hash("sha256", $password)));
    }

    public function get_password() {
        $ps = $this->request->getVar('ps');
        return $this->generatePassword($ps);
    }
}
