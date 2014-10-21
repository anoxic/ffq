<?php

class User {
    public $id;    // unique id
    public $uname; // username
    public $rank;  // see rank()
    public $mail;  // email address
    public $gen;   // time generated
    public $pw;    // password hash

    public function __construct(array $user) {
        $keys = array_keys(get_object_vars($this));

        foreach ($keys as $k) {
            if (array_key_exists($k, $user))
                $this->$k = $user[$k];
        }
    }

    public static function create($user) {
        $user['id']   = dechex(rand());
        $user['gen']  = dechex(time());
        $user['rank'] = isset($user['rank']) ? $user['rank'] : 10;
        $user['pw']   = crypt($user['pw'], 'xy');

        return new self($user);
    }

    public static function store($user) {
        // creates two files:
        // users/<uid>.id which contains the properties
        // users/<uname>.uname which points to <uid>.id by name

        $user = (array) $user;

        $uid  = filename($user['id'],'users/').'.id';
        $name = filename($user['uname'],'users/').'.name';

        $contents = http_build_query($user);

        $aput = @file_put_contents($uid, $contents);
        $bput = @file_put_contents($name, $uid);

        if ($aput && $bput)
            return true;
    }

    public static function rank($r = 0) {
        $rank = intval($rank);

        $ranks = [
            10000 => 'Superadmin',
            1000  => 'Admin',
            100   => 'Moderator',
            10    => 'User',
            1     => 'Pending',
            0     => 'Locked',
        ];

        foreach ($ranks as $k=>$i) {
            if ($rank >= $k)
                return $i;
        }
    }
}
