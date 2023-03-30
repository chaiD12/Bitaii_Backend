<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\UserModel;
use App\Models\InstallModel;

class Install extends BaseController
{
	protected $postBody; 
    protected $authModel;
    protected $installModel;
    protected $userModel;

    protected $db;
    private $sessLogin;

    public function __construct()
    {
        $this->authModel = new AuthHeaderModel(); //Consider using CI's way of initialising models
        $this->installModel = new InstallModel();
        $this->userModel = new UserModel();

        $this->sessLogin = session();
        $this->db = db_connect();
    }


    public function index()
    {
        
		$this->sessLogin = session();	
		$check = $this->authModel->checkSession($this->sessLogin);
		if ($check) {

			$dataPanel = $this->installModel->getAllDataPanel();
			$datas['panel'] = $dataPanel; 
            return view('install_view', $datas);
        }

        return view('login_view');
        
    }

    public function getByToken()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        
        $dataInstall = $this->installModel->getByToken($this->postBody['tk']);
        
        if (count($dataInstall) < 1) {
            $json = array(
                "result" => $dataInstall,
                "code" => "201",
                "message" => "Data not found",
            );
        }
        else {
            $json = array(
                "result" => $dataInstall,
                "code" => "200",
                "message" => "Success",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function save_update()
    {   
        $this->postBody = $this->authModel->authHeader($this->request);

        $arr = array();
        
        if ($this->postBody['tk'] != '') {
            $dataInstall = $this->installModel->saveUpdate($this->postBody);
            $arr = [$dataInstall];
        }

        if (count( $arr) < 1) {
            $json = array(
                "result" =>  $arr,
                "code" => "201",
                "message" => "Data not found",
            );
        }
        else {
            $json = array(
                "result" =>  $arr,
                "code" => "200",
                "message" => "Success",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    function get_data() {
        $this->sessLogin = session();	
        $check = $this->authModel->checkSession($this->sessLogin);
        
    
        if (!$check) {
            return redirect()->to(base_url()); 
        }

        $dtsession = $this->authModel->getDataSession($this->sessLogin);

        $search = $_POST['search']['value']; 
        $limit = $_POST['length']; 
        $start = $_POST['start']; 

        $sql = $this->db->query("SELECT a.id_install FROM tb_install a  "); 
        $sql_count = $sql->getNumRows($sql); 
        $sql->freeResult();

        $query = "SELECT a.*
            FROM tb_install a
            WHERE ( a.id_install LIKE '%".$search."%' OR a.uuid LIKE '%".$search."%' 
            OR a.os_platform LIKE '%".$search."%'  ) ";

        $order_index = $_POST['order'][0]['column']; // Untuk mengambil index yg menjadi acuan untuk sorting
        $order_field = $_POST['columns'][$order_index]['data']; // Untuk mengambil nama field yg menjadi acuan untuk sorting
        $order_ascdesc = $_POST['order'][0]['dir']; // Untuk menentukan order by "ASC" atau "DESC"
        $order = " ORDER BY ".$order_field." ".$order_ascdesc;

        $sql_data = $this->db->query($query.$order." LIMIT ".$limit." OFFSET ".$start); // Query untuk data yang akan di tampilkan
        $sql_filter = $this->db->query($query); // Query untuk count jumlah data sesuai dengan filter pada textbox pencarian
        $sql_filter_count = $sql_filter->getNumRows($sql_filter);
        $sql_filter->freeResult(); // Hitung data yg ada pada query $sql_filter

        $data = $sql_data->getResultArray(); // Untuk mengambil data hasil query menjadi array
        $sql_data->freeResult();
        
        $callback = array(
            'draw'=>$_POST['draw'], // Ini dari datatablenya
            'recordsTotal'=>$sql_count,
            'recordsFiltered'=>$sql_filter_count,
            'data'=>$data
        );

        header('Content-Type: application/json');
        echo json_encode($callback); 
        die();
    }
}