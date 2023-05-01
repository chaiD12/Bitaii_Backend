<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\SpinModel;

class Spins extends BaseController
{
    protected $spinsModel;
    protected $postBody;
    protected $authModel;
    protected $db;
    private $sessLogin;

    public function __construct()
    {
        $this->authModel = new AuthHeaderModel();
        $this->spinsModel = new SpinModel();
        $this->sessLogin = session();
        $this->db = db_connect();
    }

    public function index()
    {
        helper('session');

        $user_id = session('user_id');
        if (!$user_id) {
            // handle error, redirect to login page, etc.
        }

        $spin_model = new SpinModel();
        $spin_data = $spin_model->where('user_id', $user_id)->first();

        $remaining_spins = 0;
        if ($spin_data) {
            $remaining_spins = $spin_data['remaining_spins'];
            $last_spin_date = $spin_data['last_spin_date'];
            if ($last_spin_date != date('Y-m-d')) {
                $spin_model->resetSpins();
                $remaining_spins = 2;
            }
        } else {
            $spin_model->addSpins($user_id, 2);
            $remaining_spins = 2;
        }

        $data = [
            'user_id' => $user_id,
            'remaining_spins' => $remaining_spins,
        ];
        echo json_encode($data);
    }
// public function index()
// {
//     echo "base";
//     try {
//         $sql = $this->db->query('SELECT * from tb_spin');
//         $results = $sql->getResult();
//         $json = array(
//             "result" => array($results),
//             "code" => "200",
//             "message" => "Success",
//         );
//         header('Content-Type: application/json');
//         echo json_encode($json);
//     } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
//         $json = array(
//             "result" => array($e->getMessage()),
//             "code" => $e->getCode(),
//             "message" => "Failed"
//         );
//         header('Content-Type: application/json');
//         echo json_encode($json);
//     }
// }



}