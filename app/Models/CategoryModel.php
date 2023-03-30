<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table      = 'tb_category';
    protected $primaryKey = 'id_category';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['title', 'description', 'total_post', 'image', 'flag', 'status'];
    
    protected $useTimestamps = true;
    protected $createdField  = 'date_created';
    protected $updatedField  = 'date_updated';
    protected $skipValidation     = true;

    public function getTotal() {
        $query   = $this->query(" SELECT count(id_category) as total FROM tb_category ");
        $results = $query->getResultArray();
        return $results;
    }

    public function allByLimit($limit=100, $offset=0, $status=1) {
        $getlimit = "$offset,$limit";
        
        $query   = $this->query(" SELECT a.* FROM tb_category a 
            WHERE a.status='".$status."' 
            ORDER BY a.title ASC 
            LIMIT ".$getlimit." ");

        $results = $query->getResultArray();
        
        return $results;
    }

    public function allByLimitPanel($limit=100, $offset=0) {
        $getlimit = "$offset,$limit";
        
        $query   = $this->query(" SELECT a.* FROM tb_category a 
            ORDER BY a.id_category ASC 
            LIMIT ".$getlimit." ");

        $results = $query->getResultArray();
        $return_array = array();

        $i = 0;
        foreach ($results as $row) {
            $return_array[] = $row;
        }
        
        return $return_array;
    }

    public function getById($id) {
        return $this->where('id_category', $id)
                    ->first();
    }

    public function getByIdArray($id) {
        return $this->where('id_category', $id)
                    ->findAll(0,1);
    }
}