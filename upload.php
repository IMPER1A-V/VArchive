<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Загрузка данных</title>
<link rel="stylesheet" href="style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/style.js"></script>



</head>
<header>
<div class="container d-flex justify-content-between align-items-center h-100">
<a href="" class="logo" style="color:white"><img src="img/logo.png" alt="logo"></a>
		

   


</div>
</header>
<body>
<div id="notification" style="display: none;"></div>
<?php
require 'db.php';
ini_set('log_errors', 'On');
ini_set('error_log', 'error.log');

$sql = "CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    profile_img VARCHAR(255),
    theme INT NOT NULL DEFAULT 0,
    cover VARCHAR(255)
);
CREATE INDEX admin_id_index ON admin (id);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    profile_img VARCHAR(255),
    admin_id INT,
    FOREIGN KEY (admin_id) REFERENCES admin(id)
);
CREATE INDEX users_id_index ON users (id);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(255),
    date DATE,
    time TIME,
    content LONGTEXT,
    images LONGTEXT,
	audio LONGTEXT,
	links LONGTEXT,
	stickers LONGTEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE INDEX messages_user_id_index ON messages (user_id);
CREATE INDEX messages_date_time_index ON messages (date, time);

CREATE TABLE IF NOT EXISTS processed_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255)
)";


if ($db->multi_query($sql) === TRUE) {
    // echo "Таблицы успешно созданы";
    
    do {
        if ($result = $db->store_result()) {
            $result->free();
        }
    } while ($db->more_results() && $db->next_result());
} else {
    echo "Ошибка при создании таблиц: " . $db->error;
}

$sql = "SELECT COUNT(*) FROM admin WHERE id = 1";
$result = $db->query($sql);
if ($result === FALSE) {
    echo "Ошибка при выборке данных из таблицы admin: " . $db->error;
} else {
    $row = $result->fetch_row();
    if ($row[0] == 0) {
        $db->begin_transaction();
        
        $sql = "ALTER TABLE admin AUTO_INCREMENT = 1";
        $db->query($sql);
        $profile_img_admin = 'img/no_avatar.png';
        $admin_cover = 'img/cover_admin.jpg';
        $sql = "INSERT INTO admin (name, profile_img, cover, theme) VALUES ('Admin', '$profile_img_admin', '$admin_cover', '0')";
        if ($db->query($sql) === TRUE) {
            // echo "Запись успешно добавлена в таблицу admin";
            $db->commit();
        } else {
            echo "Ошибка при добавлении записи в таблицу admin: " . $db->error;
            $db->rollback();
        }
    }
}

$months = [
    'янв' => '01',
    'фев' => '02',
    'мар' => '03',
    'апр' => '04',
    'мая' => '05',
    'июн' => '06',
    'июл' => '07',
    'авг' => '08',
    'сен' => '09',
    'окт' => '10',
    'ноя' => '11',
    'дек' => '12'
];
 // echo '<form method="POST" enctype="multipart/form-data">
    // <input type="file" name="profile_img">
	  // <input type="hidden" name="profile_img_index" value="1">
    // <input type="submit" value="Загрузить">
// </form>
// ';

// if (isset($_POST['profile_img_index'])) {
    // // получаем путь к загруженному изображению
    // $profile_img = $_FILES['profile_img']['tmp_name'];

    // // задаем путь для сохранения изображения на сервере
    // $upload_dir = 'img/users';
    // $upload_file = $upload_dir . '/' . basename($_FILES['profile_img']['name']);

    // // перемещаем загруженный файл в указанное место
    // if (move_uploaded_file($profile_img, $upload_file)) {
        // echo "Файл успешно загружен.\n";
        // echo "Новый путь к файлу: " . $upload_file;
    // } else {
        // echo "Ошибка при загрузке файла.\n";
    // }
// }

		
if (isset($_FILES['files'])) {
    foreach ($_FILES['files']['name'] as $index => $filename) {
		 if (empty($filename)) {
            // пропускаем пустые имена файлов
            continue;
        }
		if (pathinfo($filename, PATHINFO_EXTENSION) === 'html') {
        // проверяем наличие имени файла в таблице processed_files
        $stmt = $db->prepare("SELECT id FROM processed_files WHERE filename=?");
        $stmt->bind_param("s", $filename);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // файл уже был обработан
            // echo "Файл '$filename' уже был обработан\n";
        } else {
            // файл еще не был обработан
            // загружаем HTML-документ
            $file = $_FILES['files']['tmp_name'][$index];
            $doc = new DOMDocument();
            @$doc->loadHTMLFile($file);
             $xpath = new DOMXPath($doc);
			 
        $elements = $xpath->query("//div[contains(@class, 'ui_crumb')]");
    foreach ($elements as $element) {
    // обрабатываем найденные элементы
    // например, извлекаем текст из элемента
    $name = trim($element->nodeValue);
    
 // проверяем наличие записи в таблице users
$stmt = $db->prepare("SELECT id FROM users WHERE name=?");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    // запись уже существует
    $row = $result->fetch_assoc();
    $user_id = $row['id'];
} else {
    if (!empty($name)) {
        $profile_img = 'img/no_avatar.png';
        // добавляем новую запись в таблицу users
        // добавляем кнопку загрузки изображения
        // добавляем запись в таблицу users с указанием пути к изображению
        $stmt = $db->prepare("INSERT INTO users (name, admin_id, profile_img) VALUES (?, 1, ?)");
        $stmt->bind_param("ss", $name, $profile_img);
        if ($stmt->execute()) {
            $user_id = $db->insert_id;


        } else {
            echo "Ошибка при добавлении записи в таблицу users: " . $db->error . "\n";
        }
    }
}
if (isset($user_id) && $user_id != 0) {
    // Создание папки для пользователя
    $base_dir = 'users'; // Базовая директория для хранения папок пользователей
    $user_dir_name = 'id-' . $user_id;
    $user_dir = $base_dir . DIRECTORY_SEPARATOR . $user_dir_name;
    if (!is_dir($user_dir)) {
        mkdir($user_dir, 0755, true);
    }
    if (!is_dir($user_dir . DIRECTORY_SEPARATOR . 'stickers')) {
        mkdir($user_dir . DIRECTORY_SEPARATOR . 'stickers', 0755, true);
    }
    if (!is_dir($user_dir . DIRECTORY_SEPARATOR . 'audio')) {
        mkdir($user_dir . DIRECTORY_SEPARATOR . 'audio', 0755, true);
    }
    if (!is_dir($user_dir . DIRECTORY_SEPARATOR . 'images')) {
        mkdir($user_dir . DIRECTORY_SEPARATOR . 'images', 0755, true);
    }
}


}

        
        $data = [];
        $elements = $xpath->query("//div[contains(@class, 'message')]");
        foreach ($elements as $element) {
            // получаем имя
            $name_node = $xpath->query(".//div[contains(@class, 'message__header')]", $element)->item(0);
            if ($name_node) {
                list($name, ) = explode(",", trim($name_node->nodeValue));
                
                // получаем дату и время
                list(, $date_time) = explode(",", trim($name_node->nodeValue));
				
$date_time = str_replace(' в', '', $date_time);
if (strpos($date_time, '(ред.)') !== false) {
  $date_time = str_replace('(ред.)', '', $date_time);
}

list($date_str) = [$date_time];
                // list($date_str, ) = explode(",", trim($date_time));
				
                list($day, $month_name,$year, $time_str) = preg_split('/\s+/', trim($date_str));
		
				// echo "day: $day\n";
				// echo "month_name: $month_name\n";
				// echo "year: $year\n";
				// echo "time_str: $time_str\n";
                // заменяем название месяца на его номер
                if (isset($months[$month_name])) {
                    $month = $months[$month_name];
                } else {
                    // обработка ошибки: название месяца не найдено в массиве
                }
			
$date = date_create_from_format('d m Y', "$day $month $year")->format('Y-m-d');
                $time = date_create_from_format('H:i:s', $time_str)->format('H:i:s');
                
                // получаем содержимое сообщения
              $content_node = $xpath->query(".//div[contains(@class, 'kludges')]/preceding-sibling::node()", $element)->item(0);
if ($content_node) {
    $content = trim($content_node->nodeValue);
 $content = preg_replace('/&([^;]+)(?![^<]*>)/', '&$1;', $content);
    // echo " $content";
} else {
    // echo "Элемент не найден\n";
}
$images = null;
$stickers = null;
$audio = null;
$links = null;
                // $img_node = $xpath->query(".//a[contains(@class, 'attachment__link')]", $element)->item(0);
// if ($img_node) {
    // $img = $img_node->getAttribute('href');
    // if (strpos($img, 'https://vk.com/doc') === 0) {
        // // Получите HTML-код страницы
        // $html = file_get_contents($img);

        // // Проверьте наличие тега img
        // if (preg_match('/"docUrl":"([^"]+)"/', $html, $matches)) {
            // $stickers = $matches[1];
        // }
    // } elseif (substr($img, -4) === '.ogg') {
        // $audio = $img;
    // }
	// elseif (strpos($img, 'https://sun') === 0) {
       // $images = $img;
    // }
	// else {
        // $links = $img_node->getAttribute('href');
    // }
// }

                $img_node = $xpath->query(".//a[contains(@class, 'attachment__link')]", $element)->item(0);
if ($img_node) {
    $img = $img_node->getAttribute('href');
    if (strpos($img, 'https://vk.com/doc') === 0) {
        // Получите HTML-код страницы
        $html = file_get_contents($img);

        // Проверьте наличие тега img
        if (preg_match('/"docUrl":"([^"]+)"/', $html, $matches)) {
            $stickers = preg_replace('/\.png.*/', '.png', $matches[1]);
            $stickers = str_replace('\\', '/', $stickers);
            $stickers = preg_replace('/([^:])(\/\/+)/', '$1/', $stickers);
        }
    } elseif (substr($img, -4) === '.ogg') {
        $audio = $img;
    } 
elseif (strpos($img, 'https://sun') === 0) {
       $images = strtok(basename($img), '?');
file_put_contents($user_dir . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $images, file_get_contents($img));
$images = $user_dir . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . basename($images);


     }
	 else {
         $links = $img_node->getAttribute('href');
     }
	
	
}

// Здесь вы можете добавить код для загрузки файлов в соответствующие папки
if ($stickers) {
    file_put_contents($user_dir . DIRECTORY_SEPARATOR . 'stickers' . DIRECTORY_SEPARATOR . basename($stickers), file_get_contents($stickers));
    $stickers = $user_dir . DIRECTORY_SEPARATOR . 'stickers' . DIRECTORY_SEPARATOR . basename($stickers);
}
if ($audio) {
    file_put_contents($user_dir . DIRECTORY_SEPARATOR . 'audio' . DIRECTORY_SEPARATOR . basename($audio), file_get_contents($audio));
	$audio = $user_dir . DIRECTORY_SEPARATOR . 'audio' . DIRECTORY_SEPARATOR . basename($audio);
}




// добавляем данные в массив
array_push($data, [$user_id, $name, $date, $time, $content, $images, $audio, $stickers, $links]);

                ///////////////
            }
        }
        
        // формируем запрос на вставку данных в таблицу messages
   if (count($data) > 0) {
    // создаем шаблон для запроса с несколькими значениями
    $values_template = implode(',', array_fill(0, count($data), '(?, ?, ?, ?, ?, ?, ?, ?, ?)'));
    // объединяем все данные в один массив
    $data_flat = array_reduce($data, 'array_merge', []);
    // подготавливаем и выполняем запрос
    $stmt = $db->prepare("INSERT INTO messages (user_id, name, date, time, content, images, audio, stickers, links) VALUES " . $values_template);
    $stmt->bind_param(str_repeat('issssssss', count($data)), ...$data_flat);
            if ($stmt->execute()) {
                   echo "<script>
				   $('#notification').css('background-color', 'lightgreen');
        $('#notification').html('Записи успешно добавлены');
        $('#notification').fadeIn();
        setTimeout(function() {
            $('#notification').fadeOut();
        }, 5000);
    </script>";
            } else {
                // echo "Ошибка при добавлении записей в таблицу messages: " . $db->error . "\n";
				 echo "<script>
				   $('#notification').css('background-color', 'red');
        $('#notification').html('Ошибка при добавлении записей в таблицу messages');
        $('#notification').fadeIn();
        setTimeout(function() {
            $('#notification').fadeOut();
        }, 5000);
    </script>";
            }
        }
            
            // добавляем имя файла в таблицу processed_files
            $stmt = $db->prepare("INSERT INTO processed_files (filename) VALUES (?)");
            $stmt->bind_param("s", $filename);
            if ($stmt->execute()) {
                // echo "Имя файла '$filename' успешно добавлено в таблицу processed_files\n";
            } else {
                echo "Ошибка при добавлении имени файла в таблицу processed_files: " . $db->error . "\n";
            }
        }
	}
    }
	

}
if (isset($_POST['go_to_index'])) {
    // Проверка того, пустая ли таблица messages в базе данных vk
    $sql = "SELECT COUNT(*) FROM messages";
    $result = $db->query($sql);
    $row = $result->fetch_row();
    if ($row[0] == 0) {
        // Таблица messages в базе данных vk пустая
       echo "ошибка";
	   
    } else {
        header('Location: /');
        exit;
    }
}

$db->close();
?>

<main>
    <div class="wrapper container col-4 d-flex justify-content-center align-items-center">
	<div class=" row w-100">
		<div class="vk_content col-12  px-0">


		<div class="p_general">
		<div class="p_general_header_block">

			<div class="p_general_header">Загрузить архив</div>
			<form  style="padding: 0 15px;" method="post">
    <input type="hidden" name="go_to_index" value="1">
    <button  class="btn_bf" type="submit">ОК</button>
</form>
			</div>
			
			<div class="hrh"></div>
		<div class="p_general_block">


<form method="post" enctype="multipart/form-data">
    <div class="d-flex align-items-start form_select_file">
        <input id="file-upload" type="file" name="files[]" multiple style="display: none;" accept=".html">
        <ul id="file-names"><li class="file-names-placeholder"><svg style="padding-right:20px;" height="40px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 312.602 312.602" xml:space="preserve" fill="#ffffff" ><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path style="fill:#ffffff;" d="M251.52,137.244c-3.966,0-7.889,0.38-11.738,1.134c-1.756-47.268-40.758-85.181-88.448-85.181 c-43.856,0-80.964,32.449-87.474,75.106C28.501,129.167,0,158.201,0,193.764c0,36.106,29.374,65.48,65.48,65.48h54.782 c4.143,0,7.5-3.357,7.5-7.5c0-4.143-3.357-7.5-7.5-7.5H65.48c-27.835,0-50.48-22.645-50.48-50.48c0-27.835,22.646-50.48,50.48-50.48 c1.367,0,2.813,0.067,4.419,0.206l7.6,0.658l0.529-7.61c2.661-38.322,34.861-68.341,73.306-68.341 c40.533,0,73.51,32.977,73.51,73.51c0,1.863-0.089,3.855-0.272,6.088l-0.983,11.968l11.186-4.367 c5.356-2.091,10.99-3.151,16.747-3.151c25.409,0,46.081,20.672,46.081,46.081c0,25.408-20.672,46.08-46.081,46.08 c-0.668,0-20.608-0.04-40.467-0.08c-19.714-0.04-39.347-0.08-39.999-0.08c-4.668,0-7.108-2.248-7.254-6.681v-80.959l8.139,9.667 c2.667,3.17,7.399,3.576,10.567,0.907c3.169-2.667,3.575-7.398,0.907-10.567l-18.037-21.427c-2.272-2.699-5.537-4.247-8.958-4.247 c-3.421,0-6.686,1.548-8.957,4.247l-18.037,21.427c-2.668,3.169-2.262,7.9,0.907,10.567c1.407,1.185,3.121,1.763,4.826,1.763 c2.137,0,4.258-0.908,5.741-2.67l7.901-9.386v80.751c0,8.686,5.927,21.607,22.254,21.607c0.652,0,20.27,0.04,39.968,0.079 c19.874,0.041,39.829,0.081,40.498,0.081c33.681,0,61.081-27.4,61.081-61.08C312.602,164.644,285.201,137.244,251.52,137.244z"></path> </g></svg>Кликните, чтобы загрузить</li></ul>
    </div>
    <button class="btn_bf" id="upload_btn" type="submit">Загрузить</button>
</form>




		</div>
		</div>
		
		
		
		
		
		
		
	</div>
	</div>
	

       
    </div>
	</main>



<!-- Оставшийся код для отображения страницы arm.php -->



<!-- Оставшийся код для отображения страницы upload.php -->


</body>
</html>
