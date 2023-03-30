<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table      = 'tb_setting';
    protected $primaryKey = 'id_setting';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['title', 'description', 'val_setting', 'flag', 'status', 'created_at', 'updated_at'];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $skipValidation     = true;

    public function getTotal() {
        $query   = $this->query(" SELECT count(id_setting) as total FROM tb_setting ");
        $results = $query->getResultArray();
        $query->freeResult();
        return $results;
    }

    public function allByLimit($limit=100, $offset=0, $status=1) {
        $getlimit = "$offset,$limit";
        
        $query   = $this->query(" SELECT a.* FROM tb_setting a 
            WHERE a.status='".$status."' 
            ORDER BY a.title ASC 
            LIMIT ".$getlimit." ");

        $results = $query->getResultArray();
        $query->freeResult();
        
        return $results;
    }

    public function allByLimitPanel($limit=100, $offset=0) {
        $getlimit = "$offset,$limit";
        
        $query   = $this->query(" SELECT a.* FROM tb_setting a 
            ORDER BY a.id_setting ASC 
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
        return $this->where('id_setting', $id)
                    ->first();
    }

    public function getByIdArray($id) {
        return $this->where('id_setting', $id)
                    ->findAll(0,1);
    }

    public function getByTitle($title) {
        return $this->where('title', $title)
                    ->first();
    }
}

//title, description, val_setting, flag, status, created_at, updated_at