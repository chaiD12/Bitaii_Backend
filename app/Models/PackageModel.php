<?php

namespace App\Models;

use CodeIgniter\Model;

class PackageModel extends Model
{
    protected $table      = 'tb_package';
    protected $primaryKey = 'id_package';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['code_package', 'title', 'description', 'image', 'price', 'currency', 'exc_usd', 'duration', 'counter_try', 'flag', 'status', 'created_at', 'updated_at'];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $skipValidation     = true;

    public function getTotal() {
        $query   = $this->query(" SELECT count(id_package) as total FROM tb_package ");
        $results = $query->getResultArray();
        return $results;
    }

    public function allByLimit($limit=100, $offset=0, $status=1) {
        $getlimit = "$offset,$limit";

        $results = array();
        $query   = $this->query(" SELECT a.* FROM tb_package a 
            WHERE a.status='".$status."' 
            ORDER BY a.title ASC 
            LIMIT ".$getlimit." ");

        $datas = $query->getResultArray();
        $query->freeResult();

        foreach ($datas as $row) {

            // get detail
            $queryDt   = $this->query(" SELECT a.* FROM tb_package_detail a 
                WHERE a.status='".$status."' AND a.id_package='".$row['id_package']."' ");

            $details = $queryDt->getResultArray();
            $queryDt->freeResult();
            $row['details'] = $details;

            $results[] = $row;
        }
        
        
        return $results;
    }

    public function allByLimitPanel($limit=100, $offset=0) {
        $getlimit = "$offset,$limit";
        
        $query   = $this->query(" SELECT a.* FROM tb_package a 
            ORDER BY a.id_package ASC 
            LIMIT ".$getlimit." ");

        $results = $query->getResultArray();
        $query->freeResult();

        $return_array = array();

        $i = 0;
        foreach ($results as $row) {
            $return_array[] = $row;
        }
        
        return $return_array;
    }

    public function getById($id) {
        return $this->where('id_package', $id)
                    ->first();
    }

    public function getByCode($code) {
        return $this->where('code_package', $code)
                    ->first();
    }

    public function getByIdArray($id) {
        return $this->where('id_package', $id)
                    ->findAll(0,1);
    }
}

//code_package, title, description, image, price, currency, exc_usd, duration, counter_try, flag, status, created_at, updated_at