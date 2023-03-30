<?php

namespace App\Models;

use CodeIgniter\Model;

class InstallModel extends Model
{
    protected $table      = 'tb_install';
    protected $primaryKey = 'id_install';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['token_fcm', 'uuid', 'token_forgot', 'flag', 'status', 'os_platform', 'created_at', 'updated_at'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $skipValidation  = true;

    public function getAllDataPanel() {

        $return_array = array();

        $query   = $this->query(" SELECT count(id_install) as total FROM tb_install ");
        $results = $query->getResultArray()[0];
        $query->freeResult();

        $return_array['install_total'] = $results['total'];

        $query   = $this->query(" SELECT count(id_user) as total FROM tb_user ");
        $results = $query->getResultArray()[0];
        $query->freeResult();

        $return_array['user_total'] = $results['total'];

        $query   = $this->query(" SELECT count(id_user_package) as total FROM tb_user_package ");
        $results = $query->getResultArray()[0];
        $query->freeResult();
        $return_array['user_package_total'] = $results['total'];

        $query   = $this->query(" SELECT sum(price-fee_api) as total FROM tb_payment_package WHERE status=1 ");
        $results = $query->getResultArray()[0];
        $query->freeResult();
        $return_array['payment_package_sum'] = $results['total'];

        $query   = $this->query(" SELECT a.*, 
            (SELECT x.email FROM tb_user x WHERE x.id_user=a.id_user) as email, 
            (SELECT x.display_name FROM tb_user x WHERE x.id_user=a.id_user) as display_name, 
            (SELECT x.expired_at FROM tb_user x WHERE x.id_user=a.id_user) as expired_at,
            (SELECT x.counter_max FROM tb_user x WHERE x.id_user=a.id_user) as counter_max, 
            (SELECT x.type_account FROM tb_user x WHERE x.id_user=a.id_user) as type_account,
            (SELECT x.profile_pic FROM tb_user x WHERE x.id_user=a.id_user) as profile_pic, 
            (SELECT x.os_platform FROM tb_install x, tb_user b WHERE x.id_install=b.id_install AND b.id_user=a.id_user LIMIT 1) as os_platform       
            FROM tb_user_package a ORDER BY a.created_at DESC LIMIT 10");
        $results = $query->getResultArray();
        $query->freeResult();
        $return_array['last_payment'] = $results;

        $counter = array();    
        
        $query   = $this->query(" SELECT count(a.id_user_package) as count_package, sum(a.price) as sum_package FROM tb_user_package a "); 
        $results = $query->getResultArray()[0];
        $query->freeResult();
        $counter['count_package'] = $results['count_package'];
        $counter['sum_package'] = $results['sum_package'];

        $query   = $this->query(" SELECT count(a.id_user_package) as count_trial, sum(a.price) as sum_trial FROM tb_user_package a WHERE a.code_package='TRIAL' "); 
        $results = $query->getResultArray()[0];
        $query->freeResult();
        $counter['count_trial'] = $results['count_trial'];
        $counter['sum_trial'] = $results['sum_trial'];

        $query   = $this->query(" SELECT count(a.id_user_package) as count_limited, sum(a.price) as sum_limited FROM tb_user_package a WHERE a.code_package='LIMITED' "); 
        $results = $query->getResultArray()[0];
        $query->freeResult();
        $counter['count_limited'] = $results['count_limited'];
        $counter['sum_limited'] = $results['sum_limited'];

        $query   = $this->query(" SELECT count(a.id_user_package) as count_max, sum(a.price) as sum_max FROM tb_user_package a WHERE a.code_package='MAX' "); 
        $results = $query->getResultArray()[0];
        $query->freeResult();
        $counter['count_max'] = $results['count_max'];
        $counter['sum_max'] = $results['sum_max'];

        //print_r($counter);
        //die();
        $return_array['counter'] = $counter;
        
        return $return_array;
    }

    public function getTotal() {
        $query   = $this->query(" SELECT count(id_install) as total FROM tb_install ");
        $results = $query->getResultArray();
        $query->freeResult();
        
        return $results;
    }

    public function allByLimit($limit=100, $offset=0) {
        return $this->where('status','1')
                    ->orderBy('id_install','desc')
                    ->findAll($limit, $offset);
    }

    public function getByToken($token) {
        return $this->where('token_fcm', $token)
                    ->first();
    }

    public function getById($id) {
        return $this->where('id_install', $id)
                    ->first();
    }

    public function saveUpdate($array) {
        $data = [
            'id_install'   => $array['id'],
            'token_fcm'    => $array['tk'],
            'uuid'         => $array['uuid'],
            'os_platform'  => $array['os']
        ];

        $check = $this->getByToken($array['tk']);

        if ($check['id_install'] != '' && $check['id_install'] != '0') { 
            $data['id_install'] = $check['id_install'];
        }
        $this->save($data);

        return $this->getByToken($array['tk']);
    }
}

/*
	id_install, token_fcm, uuid, os_platform, 
    token_forgot, timestamp, flag, status, created_at, updated_at
*/