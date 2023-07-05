<?php

    require 'db.php';
	
	
if (isset($_FILES['profile_img'])) {
    // получаем путь к загруженному изображению
    $profile_img = $_FILES['profile_img']['tmp_name'];

    // задаем путь для сохранения изображения на сервере
    $upload_dir = 'img/users';
    $upload_file = $upload_dir . '/' . basename($_FILES['profile_img']['name']);

    // перемещаем загруженный файл в указанное место
    if (move_uploaded_file($profile_img, $upload_file)) {
        if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
            // получаем текущее изображение профиля пользователя
            $user_id = $_POST['user_id'];
            $stmt = $db->prepare("SELECT profile_img FROM users WHERE id=?");
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $old_profile_img = $row['profile_img'];

                    // удаляем старое изображение профиля пользователя
                    if (!empty($old_profile_img) && file_exists($old_profile_img)) {
                        unlink($old_profile_img);
                    }
                }
            }

            // обновляем значение столбца profile_img в таблице users
            $stmt = $db->prepare("UPDATE users SET profile_img=? WHERE id=?");
            $stmt->bind_param("si", $upload_file, $user_id);
            if ($stmt->execute()) {
                // возвращаем новый путь к изображению
                echo $upload_file;
                exit;
            } else {
                echo "Ошибка при обновлении записи в таблице users: " . $db->error;
                exit;
            }
        } else {
            // получаем текущее изображение профиля администратора
            $result = $db->query("SELECT profile_img FROM admin WHERE id=1");
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $old_profile_img = $row['profile_img'];

                // удаляем старое изображение профиля администратора
                if (!empty($old_profile_img) && file_exists($old_profile_img)) {
                    unlink($old_profile_img);
                }
            }

            // обновляем значение столбца profile_img в таблице admin
            $stmt = $db->prepare("UPDATE admin SET profile_img=? WHERE id=1");
            $stmt->bind_param("s", $upload_file);
            if ($stmt->execute()) {
                // возвращаем новый путь к изображению
                echo $upload_file;
                exit;
            } else {
                echo "Ошибка при обновлении записи в таблице admin: " . $db->error;
                exit;
            }
        }
    } else {
        echo "Ошибка при загрузке файла.";
        exit;
    }
}



?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Настройки</title>
<link rel="stylesheet" href="style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>



</head>
<body><?php
include 'header.php';
	$sqli = "SELECT * FROM admin";
    $adm = $db->query($sqli);
	$sql = "SELECT * FROM users";
	 $users = $db->query($sql);
	?>
<div id="notification" style="display: none;"></div>

<main>
    <div class="wrapper container col-4">
	<div class=" row">
		<div class="px-0">
		<div class="p_block">
		<div class="p_block_header">Профиль</div>
		<div class="p_block_edit">
		   <?php while($row = $adm->fetch_assoc()): ?>
        <div class="p_block_cover"><img src="<?php echo $row['cover']; ?>" alt="cover"></div>   
		<div class="p_block_profile_wrapper">
		   <div class="profile_avatar">
		   <img src="<?php echo $row['profile_img']; ?>" alt="profile_img">
		   		<div class="profile_upload_avatar">
<img src="img/camera.png" alt="upload_avatar">
</div>
		   </div>  <p class="prifile_name_user"><?php echo $row['name']; ?></p>
		</div> </div>
		</div>
    <?php endwhile; ?>
<form id="upload-form" method="POST" enctype="multipart/form-data">
    <input id="upload-input" type="file" name="profile_img" accept=".png,.jpg,.jpeg"  style="display: none;">
    <input type="hidden" name="user_id" value="">
</form>

		<div class="p_general">
		<div class="p_general_header">Чаты</div>
			<div class="hrh"></div>
		<div class="p_general_block ">
		  
<div class="row">
<?php while($row = $users->fetch_assoc()): ?>
<div class="col-2 p_general_block_users" data-id="<?php echo $row['id']; ?>">
<img src="<?php echo $row['profile_img']; ?>" alt="Profile Image">
<span><?php echo $row['name']; ?></span>
</div>
  <?php endwhile; ?>




		</div>
		</div>
		</div>
		
		
		
		
		
		
		
	</div>
	</div>
	

       
    </div>
	
	</main>


<script>
$('.profile_upload_avatar').on('click', function() {
    $('#upload-input').click();
});

$('#upload-input').on('change', function() {
    var formData = new FormData($('#upload-form')[0]);

    $.ajax({
        url: window.location.href,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            var userId = $('input[name="user_id"]').val();
            if (userId) {
                $('.p_general_block_users[data-id="' + userId + '"] img').attr('src', response);
            } else {
                $('.profile_avatar > img').attr('src', response);
            }
        }
    });
});
// скрываем элемент с классом profile_upload_avatar
$('.profile_upload_avatar').hide();

// отображаем элемент с классом profile_upload_avatar при наведении курсора на элемент с классом profile_avatar
$('.profile_avatar').hover(
    function() {
        // плавно отображаем элемент с классом profile_upload_avatar
        $(this).find('.profile_upload_avatar').fadeIn();
    },
    function() {
        // плавно скрываем элемент с классом profile_upload_avatar
        $(this).find('.profile_upload_avatar').fadeOut();
    }
);

///

$(document).ready(function() {
    var mainHeight = $('main').outerHeight();
    var pBlockHeight = $('.p_block').height();
    var pGeneralHeight = mainHeight - pBlockHeight - 53;
    $('.p_general').height(pGeneralHeight);
  
});
$('.p_general_block_users').on('click', function() {
    var userId = $(this).data('id');
    $('input[name="user_id"]').val(userId);
    $('#upload-input').click();
});

</script>
</body>
</html>