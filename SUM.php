<?php

function get_pdo() {

    static $pdo;
    if ($pdo) return $pdo; 
    /*
     *    travis-ci 默认
     *        host    localhost
     *        dbname  test
     *        user    root
     *        pass    
     */

    $dsn = 'mysql:host=localhost;dbname=test';

    $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    );

    //dns user pass options
    return $pdo = new PDO($dsn, 'root', '', $options);
}

//创建用户表
function create_user_table() {
    $SQL = "
    CREATE TALBE IF NOT EXISTS `user` (
        id INT PRIMARY KEY,
        name VARCHAR(50) NOT NULL DEFULAT ''
    )
    ";

    $pdo = get_pdo();
    $pdo->query($SQL);
}

function create_user($name) {
    $SQL = "INSERT INTO `user` (`name`) VALUES ($name)";
    $pdo = get_pdo();
    $pdo->query($SQL);
}

function create_eq_table() {
    $SQL = "
    CREATE TALBE IF NOT EXISTS `eq` (
        id INT PRIMARY KEY,
        name VARCHAR(50) NOT NULL DEFULAT ''
    )
    ";

    $pdo = get_pdo();
    $pdo->query($SQL);
}

function create_eq($name) {
    $SQL = "INSERT INTO `eq` (`name`) VALUES ($name)";
    $pdo = get_pdo();
    $pdo->query($SQL);
}

function create_relation_table() {
    $SQL = "
    CREATE TALBE IF NOT EXISTS `_r_user_eq` (
        uid INT,
        eid INT
    )
    ";

    $pdo = get_pdo();
    $pdo->query($SQL);
}

function connect_user_equipment($uid, $eqid) {
    $SQL = "INSERT INTO `_r_user_eq` (`uid`, `eid`) VALUES ($uid, $eid)";

    $pdo = get_pdo();
    $pdo->query($SQL);
}

create_user_table();

for($i = 0; $i < 10000; $i ++) {
    create_user($i);
    echo '.';
}

create_eq_table();

for($i = 0; $i < 10000; $i ++) {
    create_eq($i);
    echo '.';
}

create_relation_table();

//创建关联关系
//10000个仪器随机关联3个user
for($i = 0; $i < 10000; $i ++) {
    connect_user_equipment(rand(1, 10000), $i);
    connect_user_equipment(rand(1, 10000), $i);
    connect_user_equipment(rand(1, 10000), $i);
}
