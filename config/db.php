
<?php

$host='localhost';
$login='root';
$password='root';
$db_name='isc_store';

$conn= new mysqli($host, $login, $password, $db_name);

if($conn->connect_error) {
    die("Ошибка подключения к БД");
}


?>