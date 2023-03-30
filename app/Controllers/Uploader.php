<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\UserModel;

class Uploader extends BaseController
{
	protected $postBody; 
    protected $authModel;
    protected $userModel;

    private   $URL_BASE = 'https://xchatbot.erhacorp.id/';
    private   $PATH = '/home/erhacorp/xchatbot.erhacorp.id/uploaded/'; // echo getcwd() php script
    private   $TOPIC_FCM = '/topics/topicxchatbot';

    public function __construct()
    {
        $this->authModel = new AuthHeaderModel(); 
        $this->userModel = new UserModel();

    }

    public function index()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        
        $limit = 0;
        $offset = 10;

        $getLimit = $this->request->getVar('lt');
        if ($getLimit != '') {
            $exp = explode(",", $getLimit);
            $limit = (int) $exp[1];
            $offset = (int) $exp[0];
        }
        
        //master user
        $dataUser = $this->userModel->allByLimit($limit, $offset);
        
        $json = array(
            "result" => $dataUser ,
            "code" => "200",
            "message" => "Success",
        );

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }
    
    public function upload_image_temp()
    {
        $this->postBody = $this->authModel->authHeader($this->request);

        $filename = $this->postBody['filename'];
        $baseEncodeImage = $this->postBody['image'];
        
        $id = $this->postBody['id'];
        $dataUser = $this->userModel->getById($id);
        
        $binary = base64_decode($baseEncodeImage);
        $namefile = $filename;
        $ext = pathinfo($namefile, PATHINFO_EXTENSION);
        

        if ($namefile != '') {
            $target_dir = $this->PATH;
            
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $url_path = $this->URL_BASE . "uploaded/";
            
            $target_path = $target_dir;
            $now = date('YmdHis');
            $rand = rand(1111, 9999);
            $generatefile = $id . "_" . $now . "_" .$rand;
            $namefile = $generatefile . "." . $ext;

            $target_path = $target_path . $namefile;
            
            //chmod($target_path, 0777);
            //print($target_path);
            //die();
            //file_put_contents($target_path, $binary);
            
            $fh = fopen($target_path, 'w') or die("can't open file " . getcwd());
            chmod($target_path, 0777);
            fwrite($fh, $binary);
            fclose($fh);

            sleep(1);

            $foto = $url_path . $namefile;
            
            $json = array(
                "result" => array("file" => $foto),
                "code" => "200",
                "file" => $foto,
                "message" => "Upload share successful..."
            );
        }
        else {

            $json = array(
                "result" => array(),
                "code" => "209",
                "message" => "Upload failed",
            );
        }
        
        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function upload_image_user()
    {
        $this->postBody = $this->authModel->authHeader($this->request);

        $filename = $this->postBody['filename'];
        $baseEncodeImage = $this->postBody['image'];
        
        $id = $this->postBody['id'];
        $dataUser = $this->userModel->getById($id);
        
        $binary = base64_decode($baseEncodeImage);
        $namefile = $filename;
        $ext = pathinfo($namefile, PATHINFO_EXTENSION);
        
        if ($namefile != '') {
            $target_dir = $this->PATH;
            
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $url_path = $this->URL_BASE . "upload/";
            

            $target_path = $target_dir;
            $now = date('YmdHis');
            $rand = rand(1111, 9999);
            $generatefile = $id . "_photo_" . $now . "_" .$rand;
            $namefile = $generatefile . "." . $ext;
            $target_path = $target_path . $namefile;
            
            $fh = fopen($target_path, 'w') or die("can't open file " . getcwd());
            chmod($target_path, 0777);
            fwrite($fh, $binary);
            fclose($fh);

            sleep(1);

            //delete old data photo member file
            $filenm_deleted = basename($dataUser['image']);
            if ($filenm_deleted != 'avatar.png') {
                $file_path_to_delete = $this->PATH . $filenm_deleted;
                unlink($file_path_to_delete);
            }

            $foto = $url_path . $namefile;
            //update photo member
            $dataUpdate = [
                "id_user" => $id,
                "image"   => $foto,
                "date_updated" => date('YmdHis'),
            ];

            $this->userModel->save($dataUpdate);
            
            $json = array(
                "result" => array("file" => $foto),
                "code" => "200",
                "file" => $foto,
                "message" => "Upload share successful..."
            );
        }
        else {

            $json = array(
                "result" => array(),
                "code" => "209",
                "message" => "Upload failed",
            );
        }
        
        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function delete_file()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        
        if ( $this->postBody['fl'] != '') {
            $file_path = $this->PATH . $this->postBody['fl'];
            unlink($file_path);
            $json = array(
                "result" => $file_path ,
                "code" => "200",
                "message" => "File $file_path Deleted",
            );
        }
        else {
            $json = array(
                "result" => $file_path ,
                "code" => "208",
                "message" => "Error File Error Unlink cannot be deleted due to an error",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

}