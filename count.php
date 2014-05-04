#!/usr/bin/env php
<?php

function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

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
    CREATE TABLE IF NOT EXISTS `user` (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(50) NOT NULL DEFAULT ''
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

function create_lab_table() {
    $SQL = "
    CREATE TABLE IF NOT EXISTS `lab` (
        id INT PRIMARY KEY AUTO_INCREMENT,
        owner_id INT DEFAULT 0,
        name VARCHAR(50) NOT NULL DEFAULT ''
    )
    ";

    $pdo = get_pdo();
    $pdo->query($SQL);
}

function create_lab($name) {
    $SQL = "INSERT INTO `lab` (`name`) VALUES ($name)";
    $pdo = get_pdo();
    $pdo->query($SQL);
}

function set_lab_owner($lab_id, $owner_id) {
    $SQL = "UPDATE `lab` SET `owner_id` = $owner_id WHERE `id` = $lab_id";

    $pdo = get_pdo();
    $pdo->query($SQL);
}

create_user_table();

for($i = 1; $i <= 1000000; $i ++) {
    create_user($i);
}

create_lab_table();

for($i = 1; $i <= 1000000; $i ++) {
    create_lab($i);
}

//100000个lab随机设定owner
for($i = 0; $i <= 1000000; $i ++) {
    set_lab_owner($i, rand(1, 1000000));
}

$pdo = get_pdo();


$SQL = "SELECT COUNT(`id`) FROM (SELECT `user`.`id`, `user`.`name` FROM `user` JOIN `lab` ON `lab`.`owner_id` = `user`.`id` GROUP BY `user`.`id`) count_table";
echo $SQL;
echo "\n";
$now = microtime_float();
$pdo->query($SQL);

echo microtime_float() - $now;
echo "\n";

$SQL = "SELECT COUNT(DISTINCT `user`.`id`) FROM `user` JOIN `lab` ON (`lab`.`owner_id` = `user`. `id`)";
echo $SQL;
echo "\n";
$now = microtime_float();
$pdo->query($SQL);

echo microtime_float() - $now;

echo "\n";
