<?php 
namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\InstallModel;
use App\Models\UserModel;
use App\Models\SettingModel;


class SendMail extends BaseController
{
    protected $postBody; 
    protected $authModel;
    protected $installModel;
    protected $userModel;

    private $webSite = 'https://xchatbot.erhacorp.id/';
    private $emailTo = 'erhacorpdotcom@gmail.com';
    private $titleApp = 'XChatBot Chat with AI';

    protected $settingModel;

    public function __construct()
    {
        $this->authModel = new AuthHeaderModel();
        $this->installModel = new InstallModel();
        $this->userModel = new UserModel();
        $this->settingModel = new SettingModel();
    }

    public function index() 
    {
        $arr = array();
        
        $randomCode = $this->request->getVar('rc');
        $to = $this->request->getVar('em');
        $ps = $this->request->getVar('ps');
       
        //print_r(array("em" => $to, "rc" => $randomCode, "ps" => $ps));
        //die();

        $checkExist = $this->userModel->getByEmail($to);
        $checkInstall = $this->installModel->getById($checkExist['id_install']);

        //print_r($checkExist);
        //die();

        $json = array(
            "result" => $checkExist,
            "code" => "200",
            "message" => "Success",
        );

         //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    function testMail() { 
        $this->postBody = $this->authModel->authHeader($this->request);

        $to = $this->emailTo;
        $subject = 'Forgot Password'; 
        $message = 'Test Body Message, How to Reset Password<br/><br/><strong>Forgot Password</strong>'
            .'<br/><br/>Best Regards, <br/>' . $this->titleApp
            .'<br/><br/>Website: ' . $this->webSite; 
        
        $email = \Config\Services::email();
        
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($message);

        if ($email->send()) 
		{
            echo 'Email successfully sent';
        } 
		else 
		{
            echo $email->printDebugger();

            $data = $email->printDebugger(['headers']);
            print_r($data);
        }
    }

    function send_mail($randomCode='', $email='') { 
        $randomCode = $this->request->getVar('rc');
        $this->postBody = $this->authModel->authHeader($this->request);

        $arr = array();
        $to = $this->postBody['em'];

        if ($email != '') {
            $to = $email;
        }
       
        if ($to != '') {

            //check email
            $checkExist = $this->userModel->getByEmail($to);
            $checkInstall = $this->installModel->getById($checkExist['id_install']);

            if ($checkInstall['id_install'] != '' && $checkExist['id_user'] != '') {
                $rand = rand(111111, 999999);
                if ($randomCode != '') {
                    $rand = $randomCode;
                }

                $subject = 'Reset Password'; 
                $message = 'Code verify for reset password.<br/><br/><strong><h2>'.$rand.'</h2></strong><br/><br/>Best Regards, <br/>' 
                    . $this->titleApp .'<br/><br/>Website: ' . $this->webSite; 
                
                $data = [
                    "id_install" => $checkInstall['id_install'],
                    "token_forgot" =>  $rand
                ];

                $this->installModel->save($data);
                
                $email = \Config\Services::email();

                $email->setTo($to); 
                $email->setSubject($subject);
                $email->setMessage($message);

                if ($email->send()) 
                {
                    $arr = $this->userModel->getByUserAll($checkExist['id_user']);
                    
                } 
                else 
                {
                    $data = $email->printDebugger(['headers']);
                    print_r($data);
                }
            }
        }

        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Data email not found, required parameter",
            );
        }
        else {
            $json = array(
                "result" => $arr,
                "code" => "200",
                "message" => "Success",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    function send_otp_code($email='') { 
        $randomCode = $this->request->getVar('rc');
        $this->postBody = $this->authModel->authHeader($this->request);

        $arr = array();
        $to = $this->postBody['em'];; //$this->request->getVar('em'); // $this->postBody['em'];

        if ($email != '') {
            $to = $email;
        }
       
        if ($to != '') {

            $rand = rand(111111, 999999);
            if ($randomCode != '') {
                $rand = $randomCode;
            }

            if ($to == 'rullyucok2021@gmail.com') {

                $dataSetting = $this->settingModel->getByTitle('OTP_TESTER');
                $rand = '123456';
                if ($dataSetting['val_setting'] != '') {
                    $rand = $dataSetting['val_setting'];
                }
            }

            $subject = 'OTP Code Verification'; 
            $message = 'Code verify for your email validation.<br/><strong><h2>'.$rand.'</h2></strong><br/><br/>Best Regards, <br/>' 
                . $this->titleApp .'<br/><br/>Website: ' . $this->webSite; 

            
            $email = \Config\Services::email();

            $email->setTo($to); 
            $email->setSubject($subject);
            $email->setMessage($message);

            if ($email->send()) 
            {   
                $getUser = $this->userModel->getByEmail($to);
                $arr = array("code" => $rand, "email" => $to, "user" => $getUser);
                
            } 
            else 
            {
                $data = $email->printDebugger(['headers']);
                print_r($data);
            }
            
            
        }

        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Process invalid, required parameter",
            );
        }
        else {
            $json = array(
                "result" => $arr,
                "code" => "200",
                "message" => "Success",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

}