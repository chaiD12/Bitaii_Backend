<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'tb_user';
    protected $primaryKey = 'id_user';
    protected $useAutoIncrement = true;

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_install',
        'email',
        'phone',
        'user_id_auth',
        'display_name',
        'user_name',
        'password',
        'website',
        'profile_pic',
        'banner_profile_pic',
        'bio',
        'location',
        'country',
        'latitude',
        'dob',
        'timestamp',
        'created_at',
        'updated_at',
        'expired_at',
        'type_package',
        'flag',
        'status',
        'type_account',
        'is_verified',
        'counter_max',
        'fcm_token',
        'followers',
        'following',
        'followers_list',
        'following_list',
        'coins',
        'referred_by',
        'code'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $skipValidation = true;
    private $keyServerFCM = 'get from data table setting';

    private $defProfilePic = 'https://xchatbot.erhacorp.id/uploaded/def_profile.jpeg';

    public function getTotal($os = '', $group = '')
    {
        $sql = " SELECT count(id_user) as total FROM tb_user ";
        if ($os != '') {
            $sql = " SELECT count(a.id_user) as total FROM tb_user a, tb_install b
                WHERE a.id_install=b.id_install
                AND b.os_platform='" . $os . "' ";
        } else if ($group != '') {
            $sql = " SELECT count(id_user) as total FROM tb_user 
                GROUP BY country ";
        }

        $query = $this->query($sql);
        $results = $query->getResultArray();
        $query->freeResult();

        return $results;
    }

    public function getTotalExc($defUser)
    {
        $sql = " SELECT count(id_user) as total FROM tb_user WHERE id_user != 0 AND id_user !='" . $defUser . "' ";
        $query = $this->query($sql);
        $results = $query->getResultArray();
        $query->freeResult();

        return $results;
    }

    //check exist user_name
    public function checkExistUsername($user_name)
    {
        $sql = " SELECT count(id_user) as total FROM tb_user WHERE LOWER(user_name)='" . strtolower($user_name) . "' ";
        $query = $this->query($sql);
        $results = $query->getResultArray()[0];
        $query->freeResult();

        return $results['total'] > 0;
    }
    //check exist user_name

    public function allByLimitPanel($limit = 100, $offset = 0)
    {
        $getlimit = "$offset,$limit";

        $query = $this->query(" SELECT a.*, b.os_platform FROM tb_user a, tb_install b 
            WHERE a.id_install=b.id_install
            ORDER BY a.total_post DESC, a.total_comment DESC, a.display_name ASC 
            LIMIT " . $getlimit . " ");

        $results = $query->getResultArray();
        $query->freeResult();

        return $results;
    }

    public function allByLimitPanelExcl($defUser, $limit = 100, $offset = 0)
    {
        $getlimit = "$offset,$limit";

        $query = $this->query(" SELECT a.*, b.os_platform FROM tb_user a, tb_install b 
            WHERE a.id_install=b.id_install
            AND a.id_user != 0  AND a.id_user !='" . $defUser . "' 
            ORDER BY a.total_post DESC, a.total_comment DESC, a.display_name ASC 
            LIMIT " . $getlimit . " ");

        $results = $query->getResultArray();
        $query->freeResult();

        return $results;
    }

    // 123456    *6BB4837EB74329105EE4568DDA7DC67ED2CA2AD9
    public function loginByUsername($user_name, $password)
    {
        return $this->where('status', '1')
            ->where('user_name', $user_name)
            ->where('password', $password)
            ->findAll();
    }

    public function loginByEmail($email, $password)
    {
        return $this->where('status', '1')
            ->where('email', $email)
            ->where('password', $password)
            ->findAll();
    }

    public function loginByPhone($phone, $password)
    {
        return $this->where('status', '1')
            ->where('phone', $phone)
            ->where('password', $password)
            ->findAll();
    }

    public function getByUserAll($id)
    {

        $query = $this->query(" SELECT a.*, b.os_platform, b.token_fcm, b.token_forgot 
            FROM tb_user a, tb_install b 
            WHERE a.id_install=b.id_install
            AND a.id_user='" . $id . "' ");

        $results = $query->getResultArray();
        $query->freeResult();

        return $results;
    }

    public function allByLimit($limit = 100, $offset = 0)
    {
        return $this->where('status', '1')
            ->orderBy('total_post', 'desc')
            ->orderBy('total_comment', 'desc')
            ->orderBy('display_name', 'asc')
            ->findAll($limit, $offset);
    }

    public function getLastId()
    {
        return $this->orderBy('id_user', 'desc')
            ->first();
    }

    public function updateUser($array)
    {
        if ($array['id'] != '') {
            $data = [
                'id_user' => $array['id'],
                'user_id_auth' => $array['uf'],
                'id_install' => $array['is'],
                'latitude' => $array['lat'],
                'location' => $array['loc'],
                'country' => $array['cc'],
            ];

            $this->save($data);
        }

        return $this->getById($array['id']);
    }

    public function payPackage($array)
    {
        if ($array['id'] != '') {
            //checkin
            $queryCheck = $this->query("SELECT * FROM tb_user_package WHERE id_user='" . $array['id'] . "' AND id_package=1 AND price=0  ");
            $resultCheck = $queryCheck->getResultArray()[0];
            $queryCheck->freeResult();

            if ($resultCheck['id_user_package'] != '' && $array['tp'] == 'TRIAL') {
                return;
            }

            $queryPackage = $this->query("SELECT * FROM tb_package WHERE code_package='" . $array['tp'] . "' ");
            $resultPacage = $queryPackage->getResultArray()[0];
            $queryPackage->freeResult();

            //payment method
            $queryPayment = $this->query("SELECT * FROM tb_payment_method WHERE code_method='" . $array['cd'] . "' ");
            $resultPayment = $queryPayment->getResultArray()[0];
            $queryPayment->freeResult();

            $datenow = date('YmdHis');
            $fl = $array['fl'] ?? '1';

            $str = "INSERT INTO tb_user_package (id_user, id_package, id_payment_method, code_method, code_package,  price, 
                currency, exc_usd, is_auto, flag, status, created_at,updated_at) 
                VALUES ('" . $array['id'] . "', '" . $resultPacage['id_package'] . "', '" . $resultPayment['id_payment_method'] . "', 
                '" . $array['cd'] . "', '" . $array['tp'] . "', '" . $resultPacage['price'] . "', '" . $resultPacage['currency'] . "', '" . $resultPacage['exc_usd'] . "',
                '0', '" . $fl . "', '1', '" . $datenow . "', '" . $datenow . "') ";
            $query = $this->query($str);
            //die($str);

        }

        return $this->getById($array['id']);
    }

    public function updatePackage($array)
    {

        //checkin
        $queryCheck = $this->query("SELECT * FROM tb_user_package WHERE id_user='" . $array['id'] . "' 
            AND code_package='" . $array['tp'] . "' AND code_method='" . $array['cd'] . "' ORDER BY id_user_package DESC LIMIT 1 ");
        $resultCheck = $queryCheck->getResultArray()[0];
        $queryCheck->freeResult();

        $datenow = date('YmdHis');
        $idPayment = $array['ipy'];
        $flag = $array['fl'] ?? '1';

        if ($resultCheck['id_user_package'] != '' && $idPayment != '') {
            //return null;
            $this->query("UPDATE tb_payment_package SET id_user_package='" . $resultCheck['id_user_package'] . "',
                updated_at='" . $datenow . "', response_api='" . $array['resp'] . "',  
                url_api='" . $array['ua'] . "', flag='" . $flag . "'    
                WHERE id_payment_package='" . $idPayment . "' ");
        }

        if ($array['id'] != '') {
            $data = $this->getById($array['id']);
            $data['type_package'] = $array['tp'];
            $data['counter_max'] = $array['cm'];
            $data['updated_at'] = date('YmdHis');
            $data['expired_at'] = date('Y-m-d H:i:s', strtotime("+30 days"));

            $this->save($data);
        }

        return $this->getById($array['id']);
    }

    public function updateCounter($array)
    {

        if ($array['id'] != '') {
            $data = $this->getById($array['id']);
            $data['counter_max'] = $array['cm'];
            $max = (int) $array['cm'];
            if ($max < 1) {
                $data['type_package'] = '';
            }

            $data['updated_at'] = date('YmdHis');

            $this->save($data);
        }

        return $this->getById($array['id']);
    }

    public function register($array)
    {

        if ($array['em'] == '' || $array['fn'] == '' || $array['is'] == '') {
            return null;
        }

        $user_name = $array['us'];
        if ($array['id'] == '' && $user_name == '') {
            $splitname = explode(" ", strtolower($array['fn']));
            $lastRow = $this->getLastId();


            $plusOne = 0;
            if ($lastRow['id_user'] != '') {
                $plusOne = (int) $lastRow['id_user'];
            }

            $plusOne = $plusOne + 1;
            $user_name = $this->generate_unique_user_name($splitname[0], $splitname[1], "$plusOne");
            //print_r($user_name);
            //die();
        }

        //$datenow = date('YmdHis');
        $data = [
            'id_user' => $array['id'],
            'id_install' => $array['is'],
            'email' => $array['em'],
            'phone' => $array['ph'],
            'display_name' => $array['fn'],
            'profile_pic' => $this->defProfilePic,
            'user_name' => $user_name,
            'user_id_auth' => $array['uf'],
            'password' => $array['ps'],
            'latitude' => $array['lat'],
            'location' => $array['loc'],
            'country' => $array['cc'],
            'code' => $array['code'],
            'coins' => 100
        ];

        $check = $this->getByEmail($array['em']);
        if ($check['id_user'] != '' && $check['id_user'] != '0') {
            $data['id_user'] = $check['id_user'];
        }

        //print_r($data);
        //die();

        $this->save($data);

        return $this->getByEmail($array['em']);
    }
    public function generate_referral_code()
    {
        $referral_code = uniqid();
        // Add any additional logic to format or modify the referral code as needed
        return $referral_code;
    }
    public function signIn3Party($array)
    {

        if ($array['em'] == '' || $array['fn'] == '' || $array['is'] == '') {
            return null;
        }

        $user_name = $array['us'];
        if ($array['id'] == '' && $user_name == '') {
            $splitname = explode(" ", strtolower($array['fn']));
            $lastRow = $this->getLastId();


            $plusOne = 0;
            if ($lastRow['id_user'] != '') {
                $plusOne = (int) $lastRow['id_user'];
            }

            $plusOne = $plusOne + 1;
            $user_name = $this->generate_unique_user_name($splitname[0], $splitname[1], "$plusOne");
        }
        $ref = $array['code'];
        //$datenow = date('YmdHis');
        $data = [
            'id_user' => $array['id'],
            'id_install' => $array['is'],
            'email' => $array['em'],
            'phone' => $array['ph'],
            'display_name' => $array['fn'],
            'type_account' => $array['ta'] ?? '',
            'profile_pic' => $array['pic'] ?? $this->defProfilePic,
            'user_name' => $array['us'] ?? $user_name,
            'user_id_auth' => $array['uf'],
            'password' => $array['ps'],
            'latitude' => $array['lat'],
            'location' => $array['loc'],
            'country' => $array['cc'],
            'code' => $this->generate_referral_code(),
            'coins' => 100
        ];

        $check = $this->getByEmail($array['em']);
        if ($check['id_user'] != '' && $check['id_user'] != '0') {
            $data['id_user'] = $check['id_user'];
        }

        //print_r($data);
        //die();

        $this->save($data);
        $id = $this->getByEmail($array['em']);

        $checkExistRef = $this->getByRef($ref);
        $query = "UPDATE tb_user SET referred_by = ? WHERE id_user = ?";
        $this->db->query($query, array($checkExistRef['id_user'], $id['id_user']));

        $query = "UPDATE tb_user SET coins = coins + 150 WHERE id_user = ?";
        $this->db->query($query, array($checkExistRef['id_user']));

        return $this->getByEmail($array['em']);
    }

    public function registerByPhone($array)
    {

        if ($array['ph'] == '' || $array['fn'] == '') {
            return null;
        }

        $user_name = $array['us'];
        if ($array['id'] == '' && $user_name == '') {
            $splitname = explode(" ", strtolower($array['fn']));
            $lastRow = $this->getLastId();

            $plusOne = 0;
            if ($lastRow['id_user'] != '') {
                $plusOne = (int) $lastRow['id_user'];
            }

            $plusOne = $plusOne + 1;
            $user_name = $this->generate_unique_user_name($splitname[0], $splitname[1], "$plusOne");
        }

        $data = [
            'id_user' => $array['id'],
            'id_install' => $array['is'],
            'email' => $array['em'],
            'phone' => $array['ph'],
            'display_name' => $array['fn'],
            'profile_pic' => $array['img'] != '' ? $array['img'] : 'https://plantrip.theaterify.id/public/profile_pics/def_profile.png',
            'user_name' => $user_name,
            'user_id_auth' => $array['uf'],
            'password' => $array['ps'],
            'latitude' => $array['lat'],
            'location' => $array['loc'],
            'country' => $array['cc'],
        ];

        //print_r($data);
        //die();

        $check = $this->getByPhone($array['ph']);
        if ($check['id_user'] != '' && $check['id_user'] != '0') {
            $data['id_user'] = $check['id_user'];
        }

        $this->save($data);

        return $this->getByPhone($array['ph']);
    }

    public function getByEmail($email)
    {
        return $this->where('email', $email)
            ->first();
    }

    public function getByPhone($phone)
    {
        return $this->where('phone', $phone)
            ->first();
    }

    public function getById($id)
    {
        return $this->where('id_user', $id)
            ->first();
    }

    public function getTokenById($id)
    {
        $query1 = $this->query(" SELECT b.*, c.token_fcm FROM tb_user b, tb_install c 
            WHERE b.id_install=c.id_install 
            AND b.id_user='" . $id . "' ");
        $result1 = $query1->getResultArray();
        $query1->freeResult();

        return $result1[0];
    }

    public function isAvailable($userName)
    {
        $check = $this->where('user_name', $userName)->first();

        if ($check['id_user'] != '' || strlen(trim($userName)) < 8) {
            //echo 'User with this user_name already exists!';
            return false;
        } else {
            return true;
        }
    }

    public function generate_unique_user_name($firstname, $lastname, $userId)
    {
        $userNamesList = array();
        $firstChar = str_split($firstname, 1)[0];
        $firstTwoChar = str_split($firstname, 2)[0];
        /**
         * an array of numbers that may be used as suffix for the user names index 0 would be the year
         * and index 1, 2 and 3 would be month, day and hour respectively.
         */
        $numSufix = explode('-', date('Y-m-d-H'));

        // create an array of nice possible user names from the first name and last name
        array_push(
            $userNamesList,
            $firstname,
            //james
            $lastname, // oduro
            $firstname . $lastname, //jamesoduro
            $firstname . '.' . $lastname, //james.oduro
            $firstname . '-' . $lastname, //james-oduro
            $firstChar . $lastname, //joduro
            $firstTwoChar . $lastname, //jaoduro,
            $firstname . $numSufix[0], //james2019
            $firstname . $numSufix[1], //james12 i.e the month of reg
            $firstname . $numSufix[2], //james28 i.e the day of reg
            $firstname . $numSufix[3] //james13 i.e the hour of day of reg
        );


        $isAvailable = false; //initialize available with false
        $index = 0;
        $maxIndex = count($userNamesList) - 1;

        // loop through all the userNameList and find the one that is available
        do {
            $availableUserName = $userNamesList[$index];
            $isAvailable = $this->isAvailable($availableUserName);
            $limit = $index >= $maxIndex;
            $index += 1;
            if ($limit) {
                break;
            }

        } while (!$isAvailable);

        // if all of them is not available concatenate the first name with the user unique id from the database
        // Since no two rows can have the same id. this will sure give a unique user_name
        if (!$isAvailable) {
            return $firstname . $userId;
        }
        return $availableUserName;
    }

    //send FCM notif
    public function sendFCMMessage($token, $data_array)
    {
        //$keyServerFCM = 'AAAAInjYsHU:APA91bEirGDQHM1Vdp64CH45KCIEzPXh871At1mOibQpE4hB3uXXWwq7iWPDg-fC9RcKSq0d52LnYH9reILWokvDsqzjL6dFEuzm7MTOgFJ-movuUgcp1p3pQbzTUaKnx9hf3X_xEOg-';

        $url = 'https://fcm.googleapis.com/fcm/send';
        $data = array(
            'notification' => array(
                "title" => $data_array['title'],
                "body" => $data_array['body'],
                'profile_pic' => $data_array['profile_pic'],
                'profile_picUrl' => $data_array['profile_pic'],
                "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                'priority' => 'high',
                'sound' => 'default'
            ),
            'data' => $data_array['payload'],
            // Set Android priority to "high"
            'android' => array(
                'priority' => "high",
                'profile_pic' => $data_array['profile_pic'],
            ),
            // Add APNS (Apple) config
            'apns' => array(
                'payload' => array(
                    'aps' => array(
                        'contentAvailable' => true,
                    ),
                ),
                'headers' => array(
                    "apns-push-type" => "background",
                    "apns-priority" => "5",
                    // Must be `5` when `contentAvailable` is set to true.
                    "apns-topic" => "io.flutter.plugins.firebase.messaging",
                    // bundle identifier
                ),
            ),
            'priority' => 'high',
            "to" => $token
        );

        $options = array(
            'http' => array(
                'method' => 'POST',
                'content' => json_encode($data),
                'header' => "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n" .
                "Authorization: key=" . $this->keyServerFCM
            )
        );

        $context = stream_context_create($options);

        try {
            $result = file_get_contents($url, false, $context);
            return json_decode($result, true);
            //send notif fcm to topics
        } catch (Exception $e) {
            // exception is raised and it'll be handled here
            // $e->getMessage() contains the error message
            //print("Error " . $e->getMessage());
        }

        return array();
    }

    public function getByRef($Ref)
    {
        return $this->where('code', $Ref)
            ->first();
    }

    // public function updateCoins($userId, $coins)
    // {
    //     $data = [
    //         'coins' => $coins,
    //     ];
    //     $builder = $this->db->table('tb_user');
    //     $builder->where('id_user', $userId);
    //     $builder->update($data);
    // }
    public function updateCoins($userId, $coins)
    {
        // Get the current number of coins
        $currentCoins = $this->db->table('tb_user')->select('coins')->where('id_user', $userId)->get()->getRow()->coins;

        // Calculate the new number of coins
        $newCoins = $currentCoins + $coins;

        // Check if the new number of coins is less than zero
        if ($newCoins < 0) {
            throw new \Exception('Coins cannot be less than zero.');
        }

        $data = [
            'coins' => $newCoins,
        ];
        $builder = $this->db->table('tb_user');
        $builder->where('id_user', $userId);
        $builder->update($data);
    }
}

//type_account	varchar(50)	utf8_general_ci		No	EMAIL_SIGIN	PHONE_SIGNIN, EMAIL_SIGNIN, GOOGLE_SIGNIN, APPLE_SIGNIN, FACEBOOK_SIGNIN

/* id_user, email, phone, user_id_auth, display_name, user_name, 
website, profile_pic, banner_profile_pic, bio, location, latitude, dob, 
timestamp, created_at, updated_at, expired_at, flag, status, type_account, 
is_verified, fcm_token, followers, following, followers_list, following_list
*/