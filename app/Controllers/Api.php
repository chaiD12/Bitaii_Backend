<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\SettingModel;
use App\Models\PackageModel;
use App\Models\UserModel;

class Api extends BaseController
{
    
    protected $postBody; 
    protected $authModel;

    protected $settingModel;
    protected $packageModel;

    protected $userModel;

    public function __construct()
    {
        $this->authModel = new AuthHeaderModel(); 
        $this->settingModel = new SettingModel(); 
        $this->packageModel = new PackageModel(); 
        $this->userModel = new UserModel(); 
    }

    public function index()
    {
        $json = array(
            "result" => array(),
            "code" => "200",
            "message" => "Success",
        );

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function get_data()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        
        $offset = 0;
        $limit = 10;

        $getLimit = $this->request->getVar('lt');
        if ($getLimit != '') {
            $exp = explode(",", $getLimit);
            $offset = (int) $exp[0];
            $limit = (int) $exp[1];
            
        }
        
        //master setting
        $dataSetting = $this->settingModel->allByLimit($limit, $offset);

        //master package detail
        $dataPackage = $this->packageModel->allByLimit($limit, $offset);
 
        $results = array();
        $results['settings'] = $dataSetting;
        $results['packages'] = $dataPackage;

        $json = array(
            "result" => $results,
            "code" => "200",
            "message" => "Success",
        );

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function check_email_phone() {
        $this->postBody = $this->authModel->authHeader($this->request);
        $arr = array();

        if ($this->postBody['em'] == '' && $this->postBody['ph'] == '') {
            
        }
        else {
            $checkExist = null;
            if ($this->postBody['ph'] != '') {
                $checkExist = $this->userModel->getByPhone($this->postBody['ph']);
            }
            else if ($this->postBody['em'] != '') {
                $checkExist = $this->userModel->getByEmail($this->postBody['em']);
            }

            if ($checkExist != null) {
                $arr = [$checkExist]; 
            }
        }

        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Data not found",
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

    public function login()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        
        $offset = 0;
        $limit = 10;

        $getLimit = $this->request->getVar('lt');
        if ($getLimit != '') {
            $exp = explode(",", $getLimit);
            $offset = (int) $exp[0];
            $limit = (int) $exp[1];
            
        }
        
        //123456    cfc5902918296762903710e9c9a65580
        $passwrd = $this->generatePassword($this->postBody['ps']);
        $dataUser = $this->userModel->loginByEmail($this->postBody['em'], $passwrd);

        if ($this->postBody['is'] != '' && $dataUser['id_user'] != '') {
            $this->postBody['id'] = $dataUser['id_user'];
            $this->userModel->updateUser($this->postBody);
        }
        
        $arr = $dataUser; 
        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Email/Username & Password invalid",
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

    public function register()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        $arr = array();

        if ($this->postBody['ps'] != '' && $this->postBody['em'] != '') {
            
            $checkExist = $this->userModel->getByEmail($this->postBody['em']);
            

            if ($checkExist['id_user'] == '') {
                $this->postBody['ps'] = $this->generatePassword($this->postBody['ps']);
                
                $dataUser = $this->userModel->register($this->postBody);

                $arr = [$dataUser]; 
            }
        }

        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Email/Username already exist",
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

    //handle register from third party (google-sign-in) (apple-id)
    public function register_3party()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        $arr = array();

        if ($this->postBody['ps'] != '' && $this->postBody['em'] != '') {
            
            $checkExist = $this->userModel->getByEmail($this->postBody['em']);

            if ($checkExist['id_user'] == '') {
                $this->postBody['ps'] = $this->generatePassword($this->postBody['ps']);
            }
            else {
                $this->postBody['id'] = $checkExist['id_user'];
                $this->postBody['ps'] =  $checkExist['password'];
                $this->postBody['us'] = $checkExist['user_name'];
            }    
            
            $dataUser = $this->userModel->signIn3Party($this->postBody);
            $arr = [$dataUser];     
        }

        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Required parameter",
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
    //handle register from third party (google-sign-in) (apple-id)

    public function register_phone()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        $arr = array();

        if ($this->postBody['ps'] != '' && $this->postBody['ph'] != '') {
            $checkExist = $this->userModel->getByPhone($this->postBody['ph']);
            
            if ($checkExist['id_user'] == '') {
                $this->postBody['ps'] = $this->generatePassword($this->postBody['ps']);
                $dataUser = $this->userModel->registerByPhone($this->postBody);
                
                $arr = [$dataUser]; 
            }
        }

        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Phone number already exist",
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

    public function pay_package()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        $dataUser = $this->userModel->getById($this->postBody['id']);

        
        if ($this->postBody['is'] != '' && $dataUser['id_user'] != '' && $this->postBody['tp'] != '') {
            $check = $this->userModel->payPackage($this->postBody);
            if ($check != null) {
                $dataUser = $this->userModel->getById($this->postBody['id']);
            }
            else {
                $json = array(
                    "result" => array(),
                    "code" => "201",
                    "message" => "Process failed",
                );
                header('Content-Type: application/json');
                echo json_encode($json);
                die();
            }
        }
        
        $arr = $dataUser; 
        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Process failed",
            );
        }
        else {
            $json = array(
                "result" => [$arr],
                "code" => "200",
                "message" => "Success",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function update_package()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        $dataUser = $this->userModel->getById($this->postBody['id']);

        if ($this->postBody['is'] != '' && $dataUser['id_user'] != '' && $this->postBody['tp'] != '') {
            $check = $this->userModel->updatePackage($this->postBody);
            if ($check != null) {
              $dataUser = $this->userModel->getById($this->postBody['id']);
            }
            else {
                $json = array(
                    "result" => array(),
                    "code" => "201",
                    "message" => "Process failed",
                );
                header('Content-Type: application/json');
                echo json_encode($json);
                die();
            }
        }
        
        $arr = $dataUser; 
        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Process failed",
            );
        }
        else {
            $json = array(
                "result" => [$arr],
                "code" => "200",
                "message" => "Success",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function update_counter()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        $dataUser = $this->userModel->getById($this->postBody['id']);

        if ($this->postBody['is'] != '' && $dataUser['id_user'] != '' && $this->postBody['cm'] != '') {
            $this->userModel->updateCounter($this->postBody);
            $dataUser = $this->userModel->getById($this->postBody['id']);
        }
        
        $arr = $dataUser; 
        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Email/Username & Password invalid",
            );
        }
        else {
            $json = array(
                "result" => [$arr],
                "code" => "200",
                "message" => "Success",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function get_user()
    {
        $this->postBody = $this->authModel->authHeader($this->request);

        if ($this->postBody['is'] != '' &&  $this->postBody['iu'] != '') {
            $this->postBody['id'] = $this->postBody['iu'];
            $this->userModel->updateUser($this->postBody);
        }
        
        $dataUser = $this->userModel->getById($this->postBody['iu']);

        if ($dataUser['id_user'] == '') {
            $json = array(
                "result" => array(),
                "code" => "201",
                "message" => "Data not found",
            );
        }
        else {
            $json = array(
                "result" => [$dataUser],
                "code" => "200",
                "message" => "Success",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function update_user()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        $action = $this->postBody['act'];
        $idUser = $this->postBody['iu'];
        $image = $this->postBody['img'];

        if ($this->postBody['is'] != '' &&  $idUser != '' &&  $action != '') {
            $this->postBody['id'] = $idUser;

            $datenow = date('YmdHis'); 

            if ($action == 'update_fullname') {
                $sqlUpdate = "UPDATE tb_user SET display_name='".$this->postBody['fn']."', updated_at='".$datenow."' 
                    WHERE id_user='".$idUser."' ";
                    
                if ($image != '') {
                    $sqlUpdate = "UPDATE tb_user SET display_name='".$this->postBody['fn']."', profile_pic='".$image."',  
                     updated_at='".$datenow."'  WHERE id_user='".$idUser."' ";
                }

                $this->userModel->query($sqlUpdate);
            }
        }
        
        $dataUser = $this->userModel->getById($this->postBody['iu']);

        if ($dataUser['id_user'] == '') {
            $json = array(
                "result" => array(),
                "code" => "201",
                "message" => "Data not found",
            );
        }
        else {
            $json = array(
                "result" => [$dataUser],
                "code" => "200",
                "message" => "Success",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }
    
    public function hash_password() {
        $this->postBody = $this->authModel->authHeader($this->request);
        print_r($this->generatePassword($this->postBody['ps']));
    }

    private function generatePassword($password) {
        return md5(sha1(hash("sha256", $password)));
    }
}
