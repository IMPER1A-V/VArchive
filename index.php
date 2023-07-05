<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>VArchive</title>
<link rel="stylesheet" href="style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/datepicker.js"></script>
<script src="js/style.js"></script>

</head>
<body>
<?php
// Подключение к базе данных vk
require_once "db.php"; 

// Проверка того, пустая ли база данных vk
$sql = "SHOW TABLES FROM vk";
$result = $db->query($sql);
if ($result->num_rows == 0) {
    // База данных vk пустая
    header('Location: /upload');
    exit;
} elseif (isset($_COOKIE['go_to_index'])) {
    header('Location: /');
    exit;
} else {
    // Отображение страницы по умолчанию
}
?>
<?php
    include 'header.php';
	
?>


<main>
    <div class="wrapper container">
	<div class="im-page row">
<?php

    include 'content.php';

?>
	</div>
	

       
    </div>
	</main>


<script>
$('.im-page--dialogs').on('wheel', function() {
    $(this).addClass('scrolling');
});

$('.im-page--dialogs').on('mouseleave', function() {
    $(this).removeClass('scrolling');
});


</script>
</body>
</html>