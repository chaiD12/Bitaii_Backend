<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\UserModel;
use App\Models\InstallModel;

class Userlogin extends BaseController
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
            return view('userlogin_view', $datas);
        }

        return view('login_view');
        
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

        $sql = $this->db->query("SELECT a.id_userlogin FROM tb_userlogin a  "); 
        $sql_count = $sql->getNumRows($sql); 
        $sql->freeResult();

        $query = "SELECT a.*
            FROM tb_userlogin a
            WHERE ( a.id_userlogin LIKE '%".$search."%' OR a.fullname LIKE '%".$search."%' 
            OR a.username LIKE '%".$search."%' OR a.email LIKE '%".$search."%' 
            OR a.flag LIKE '%".$search."%' ) ";

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