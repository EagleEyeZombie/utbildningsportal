<?php
session_start(); 

$host = '127.0.0.1';
$db   = 'utbildningsportal';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
$pdo = new PDO($dsn, $user, $pass, $options);

// Ladda klassfiler
require_once "include/class_user.php";
require_once "include/class_task.php";
require_once "include/class_school.php"; // <--- NYTT

$user_obj = new User($pdo);
$task_obj = new Task($pdo);
$school_obj = new School($pdo); // <--- NYTT

?>
