<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\SettingModel;
use App\Models\PackageModel;
use App\Models\UserModel;

class Coins extends BaseController
{
    
    protected $userModel;
    protected $postBody;
    protected $db;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
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
            echo json_encode($json);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            echo 'Error: ' . $e->getMessage();

            $json = array(
                "result" => array($e->getMessage()),
                "code" => $e->getCode(),
                "message" => "Failed",
            );
            header('Content-Type: application/json');
            echo json_encode($json);
        }
        die();
    }

    public function get_coins($userId)
    {
        try {
            // Retrieve the user's record from the database using the user ID
            $userRecord = $this->userModel->find($userId);
    
            if (!$userRecord) {
                // If the user record doesn't exist, return a 404 error
                throw new \CodeIgniter\Exceptions\PageNotFoundException("User with ID {$userId} not found.");
            }
    
            // Extract the coins field from the user's record
            $coins = $userRecord['coins'];
    
            // Return a JSON response with the user's coins
            $json = array(
                "result" => array(
                    "coins" => $coins,
                ),
                "code" => "200",
                "message" => "Success",
            );
            header('Content-Type: application/json');
            echo json_encode($json);
        } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
            // Return a JSON response with a 404 error message
            $json = array(
                "result" => array($e->getMessage()),
                "code" => $e->getCode(),
                "message" => "Failed",
            );
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode($json);
        } catch (\Exception $e) {
            // Return a JSON response with a generic error message
            $json = array(
                "result" => array($e->getMessage()),
                "code" => $e->getCode(),
                "message" => "Failed",
            );
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode($json);
        }
    }

    public function update_coins($userID)
    {
        $coins = $this->request->getVar('coins');
        $this->userModel->updateCoins($userID, $coins);
        $json = array(
            "result" => array(),
            "code" => "200",
            "message" => "Coins updated successfully",
        );
        header('Content-Type: application/json');
        echo json_encode($json);
    }

}
