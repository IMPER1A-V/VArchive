<?php

// подключаем файл с параметрами подключения к базе данных
require_once "db.php";

$action = $_GET['action'];

if ($action === 'get_messages') {
    // определяем переменную для хранения количества элементов на одной странице
    $limit = 20;

    $user_id = $_GET['id'];
    // определяем переменную для хранения начальной позиции выборки из базы данных
    $offset = isset($_GET["offset"]) ? $_GET["offset"] : 0;

    // формируем SQL-запрос для получения данных из таблицы message с ограничением по количеству и смещением
    $sql = "SELECT m.id, m.name, m.content, m.images, m.date, m.time, m.stickers, m.audio,
        a.profile_img AS admin_profile_img,
        u.profile_img AS user_profile_img
    FROM messages m
    LEFT JOIN users u ON m.user_id = u.id
    LEFT JOIN admin a ON u.admin_id = a.id
    WHERE m.user_id = $user_id
    ORDER BY m.date DESC, m.time DESC
    LIMIT $limit OFFSET $offset";

$data = execute_query($sql);
    echo json_encode($data);
} 
elseif ($action === 'attachments_user') {
	    $limit = 20;

    $user_id = $_GET['id'];
    // определяем переменную для хранения начальной позиции выборки из базы данных
    $offset = isset($_GET["offset"]) ? $_GET["offset"] : 0;

    // формируем SQL-запрос для получения данных из таблицы message с ограничением по количеству и смещением
$sql = "SELECT m.images
    FROM messages m
    WHERE m.user_id = $user_id AND m.images IS NOT NULL
    ORDER BY m.date DESC, m.time DESC";
    $data = execute_query($sql);
    echo json_encode($data);
	} 
	elseif ($action === 'get_search') {
		$limit = 20;
$search = $_GET['search'];
$user_id = $_GET['id'];
// определяем переменную для хранения начальной позиции выборки из базы данных
$offset = isset($_GET["offset_s"]) ? $_GET["offset_s"] : 0;

// формируем SQL-запрос для получения данных из таблицы message с ограничением по количеству и смещением
$sql = "SELECT
    m.name,
    m.time,
    m.images,
    m.content,
    m.date,
    CASE
        WHEN m.name = 'Вы' AND a.id = 1 THEN a.profile_img
        ELSE u.profile_img
    END AS profile_img
FROM messages m
LEFT JOIN users u ON m.user_id = u.id
LEFT JOIN admin a ON u.admin_id = a.id
WHERE m.content REGEXP '$search' AND m.user_id = $user_id
ORDER BY m.date DESC, m.time ASC
LIMIT $limit OFFSET $offset";
    $data = execute_query($sql);
    echo json_encode($data);
}

elseif ($action === 'get_search_date') {
$limit = 20;
$search = $_GET['search'];
$user_id = $_GET['id'];
// определяем переменную для хранения начальной позиции выборки из базы данных
$offset = isset($_GET["offset_s"]) ? $_GET["offset_s"] : 0;

// формируем SQL-запрос для получения данных из таблицы message с ограничением по количеству и смещением
$sql = "SELECT
    m.name,
    m.time,
    m.images,
    m.content,
    m.date,
    CASE
        WHEN m.name = 'Вы' AND a.id = 1 THEN a.profile_img
        ELSE u.profile_img
    END AS profile_img
FROM messages m
LEFT JOIN users u ON m.user_id = u.id
LEFT JOIN admin a ON u.admin_id = a.id
WHERE m.date REGEXP '$search' AND m.user_id = $user_id
ORDER BY m.date DESC, m.time ASC
LIMIT $limit OFFSET $offset";
	$data = execute_query($sql);
    echo json_encode($data);
}

function execute_query($sql) {
    global $db;

    // выполняем SQL-запрос и получаем результат в виде объекта mysqli_result
    $result = $db->query($sql);

    // проверяем наличие ошибок при выполнении запроса
    if ($result === false) {
        die("Ошибка выполнения запроса: " . $db->error);
    }

    // создаем пустой массив для хранения данных из базы данных
    $data = array();

    // проверяем количество полученных строк в результате запроса
    if ($result->num_rows > 0) {
        // перебираем каждую строку результата запроса в виде ассоциативного массива
        while ($row = $result->fetch_assoc()) {
            // добавляем строку в массив данных
            $data[] = $row;
        }
    }

    // закрываем результат запроса
    $result->close();

    return $data;
}

// закрываем подключение к базе данных
$db->close();
?>
