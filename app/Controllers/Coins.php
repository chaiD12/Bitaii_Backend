<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\UserModel;

class Coins extends BaseController
{
    protected $userModel;
    protected $postBody;
    protected $authModel;
    protected $db;

    private $sessLogin;

    public function __construct()
    {
        $this->authModel = new AuthHeaderModel();
        $this->userModel = new UserModel();
        $this->sessLogin = session();
        $this->db = db_connect();
    }

    public function index()
    {

        try {
            $sql = $this->db->query('SELECT * from tb_user');
            $results = $sql->getResult();
            $json = array(
                "result" => array($results),
                "code" => "200",
                "message" => "Success",
            );
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            echo json_encode($json);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            $json = array(
                "result" => array($e->getMessage()),
                "code" => $e->getCode(),
                "message" => "Failed",
            );
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            http_response_code(500);
            echo json_encode($json);


        }

        die();
    }

    public function get_coins($userId)
    {

        try {
            $userRecord = $this->userModel->find($userId);

            if (!$userRecord) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException("User with ID {$userId} not found.");
            }
            $coins = $userRecord['coins'];
            $json = array(
                "result" => array(
                    "coins" => $coins,
                ),
                "code" => "200",
                "message" => "Success",
            );
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            echo json_encode($json);
        } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
            $json = array(
                "result" => array($e->getMessage()),
                "code" => $e->getCode(),
                "message" => "Failed",
            );
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET');
            http_response_code(404);
            echo json_encode($json);
        } catch (\Exception $e) {
            $json = array(
                "result" => array($e->getMessage()),
                "code" => $e->getCode(),
                "message" => "Failed",
            );
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET');
            http_response_code(500);
            echo json_encode($json);
        }
    }
    public function update_coins($userID)
    {
        $coins = $this->request->getVar('coins');

        try {
            $this->userModel->updateCoins($userID, $coins);
            $json = array(
                "result" => array(),
                "code" => "200",
                "message" => "Coins updated successfully",
            );
            echo json_encode($json);
        } catch (\Exception $e) {
            $json = array(
                "result" => array($e->getMessage()),
                "code" => $e->getCode(),
                "message" => "Failed",
            );
            echo json_encode($json);
        }

        // return $this->response->setContentType('application/json')->setOutput(json_encode($json));
    }
// public function update_coins($userID)
// {
//     try {
//         $coins = $this->request->getVar('coins');
//         $this->userModel->updateCoins($userID, $coins);
//         $json = array(
//             "result" => array(),
//             "code" => "200",
//             "message" => "Coins updated successfully",
//         );
//         echo json_encode($json);
//     } catch (\Exception $e) {
//         $json = array(
//             "result" => array($e->getMessage()),
//             "code" => $e->getCode(),
//             "message" => "Failed",
//         );
//         echo json_encode($json);
//     }
// }







// public function update_coins($userID)
// {

//     try {
//         $coins = $this->request->getVar('coins');
//         $this->userModel->updateCoins($userID, $coins);
//         $json = array(
//             "result" => array(),
//             "code" => "200",
//             "message" => "Coins updated successfully",
//         );
//         echo json_encode($json);
//     } catch (\Exception $e) {
//         $json = array(
//             "result" => array($e->getMessage()),
//             "code" => $e->getCode(),
//             "message" => "Failed",
//         );
//         echo json_encode($json);
//     }
// }
}