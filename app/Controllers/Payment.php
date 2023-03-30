<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\UserModel;
use App\Models\InstallModel;

use App\Models\PackageModel;

class Payment extends BaseController
{
	protected $postBody; 
    protected $authModel;
    protected $installModel;
    protected $userModel;

    protected $packageModel;

    protected $db;
    private $sessLogin;

    public function __construct()
    {
        $this->authModel = new AuthHeaderModel(); //Consider using CI's way of initialising models
        $this->installModel = new InstallModel();
        $this->userModel = new UserModel();

        $this->packageModel = new PackageModel();

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
            return view('payment_view', $datas);
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

        $sql = $this->db->query("SELECT a.id_payment_package FROM tb_payment_package a  "); 
        $sql_count = $sql->getNumRows($sql); 
        $sql->freeResult();

        $query = "SELECT a.*
            FROM tb_payment_package a
            WHERE ( a.id_payment_package LIKE '%".$search."%' OR a.ref_no LIKE '%".$search."%' 
            OR a.price LIKE '%".$search."%' OR a.code_method LIKE '%".$search."%'
            OR a.name_onbehalf LIKE '%".$search."%' OR a.code_package LIKE '%".$search."%' ) ";

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

    public function insert_update() {
        $this->postBody = $this->authModel->authHeader($this->request);
        $dataUser = $this->userModel->getById($this->postBody['id']);

        $arr = array();

        // id package
        $tpPackage = $this->postBody['tp'];
        // code method
        $cdMethod = $this->postBody['cd'];

        $datenow = date('YmdHis');
        $refno = 'PY-' . $datenow . 'US' . rand(111111, 999999);

        if ($dataUser != '' && $tpPackage != '' && $cdMethod != '') {

            $dataPackage =  $this->packageModel->getByCode($tpPackage);
            if ($dataPackage['id_package'] != '') {

                // id payment
                $ipy = $this->postBody['ipy'];

                //flag   1 == debug , 2 == live
                $fl = $this->postBody['fl'] ?? '1';

                $desc = 'Payment Package '. $tpPackage. ' for ' . $dataUser['display_name'] . ' via ' . $cdMethod;

                $queryPay = "INSERT INTO tb_payment_package (id_user, ref_no, name_onbehalf, description, 
                    code_package, price, currency, exc_usd, code_method, flag, status, due_date_payment, created_at, updated_at)
                 VALUES ('".$dataUser['id_user']."', '".$refno."', '".$dataUser['display_name']."', '".$desc."',  '".$tpPackage."', 
                 '".$dataPackage['price']."', '".$dataPackage['currency']."', '".$dataPackage['exc_usd']."', '".$cdMethod."', 
                 '".$fl."', '0',  '".$datenow."', '".$datenow."', '".$datenow."' )    ";
                
                 if ($ipy != '') {
                    $queryPay = "UPDATE tb_payment_package 
                    SET status='".$this->postBody['st']."',
                    id_user_package='".$this->postBody['iup']."', 
                    url_api='".$this->postBody['ua']."', 
                    response_api='".$this->postBody['ra']."',
                    flag='".$fl."',  
                    date_payment='".$datenow."', 
                    updated_at='".$datenow."' 
                    WHERE id_payment_package='".$ipy."' ";
                }

                $this->db->query($queryPay);

                if ($ipy != '') {
                    $query = $this->db->query("SELECT * FROM tb_payment_package WHERE id_payment_package='".$ipy."' ");
                    $dataPay = $query->getResultArray()[0];
                    $dataPay['user'] = $dataUser;

                    $arr = [$dataPay];
                }
                else {
                    $query = $this->db->query("SELECT * FROM tb_payment_package WHERE ref_no='".$refno."' ");
                    $dataPay = $query->getResultArray()[0];
                    $dataPay['user'] = $dataUser;
                    $arr = [$dataPay];
                }
            }
        }

        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Payment failed",
            );
        }
        else {
            $json = array(
                "result" => $arr,
                "code" => "200",
                "message" => "Success",
            );
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }
}