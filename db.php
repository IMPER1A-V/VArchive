<?php
// Подключение к серверу MySQL
$db = new mysqli('localhost', 'root', '');
if ($db->connect_error) {
    die("Ошибка подключения: " . $db->connect_error);
}

// Создание базы данных vk
$sql = "CREATE DATABASE IF NOT EXISTS vk";
if ($db->query($sql) !== TRUE) {
    die("Ошибка при создании базы данных: " . $db->error);
}

// Закрытие соединения с сервером MySQL
$db->close();

// Подключение к базе данных vk
$db = new mysqli('localhost', 'root', '', 'vk');
if ($db->connect_error) {
    die("Ошибка подключения: " . $db->connect_error);
}

// Оставшийся код для работы с базой данных vk
?>

