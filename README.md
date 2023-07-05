# VArchive
VArchive - allows you to view your vk.com archive in a more convenient and familiar form

To work, you will need: a server or a local server (in my case, it was OPEN SERVER).
---------------------
If you have a large message folder, then you need to adjust the php and mysql configuration. (these values ​​were set solely from my needs)

---PHP---
;default_socket_timeout = 60000
max_execution_time = 60000
max_file_uploads = 25000
;max_input_nesting_level = 64
;max_input_time = -1
;max_input_vars = 1000
memory_limit = 6000M
post_max_size = 7000M
upload_max_filesize = 6500M

---MYSQL---
# TableSettings
table_definition_cache = 4096
table_open_cache = 4096
max_heap_table_size = 500M
tmp_table_size = 450M

# InnoDB Settings
innodb_buffer_pool_size = 200M

You need to open the db.php file and replace danow with your connection:
here
$db = new mysqli('localhost', 'root', '');
and here
$db = new mysqli('localhost', 'root', '', 'vk');
(vk does not need to be changed)

---RU---
VArchive - позволяет просматривать ваш архив Вконтакте в более удобном и привычном виде

Для работы вам понадобится: сервер или локальный сервер (в моем случае это был OPEN SERVER).
---------------------
Если у вас большая папка с сообщениями, вам нужно настроить конфигурацию php и mysql. (эти значения ставились исключительно из моих нужд)
---PHP---
;default_socket_timeout = 60000
max_execution_time = 60000
max_file_uploads = 25000
;max_input_nesting_level = 64
;max_input_time = -1
;max_input_vars = 1000
memory_limit = 6000M
post_max_size = 7000M
upload_max_filesize = 6500M

---MYSQL---
# TableSettings
table_definition_cache = 4096
table_open_cache = 4096
max_heap_table_size = 500M
tmp_table_size = 450M

# InnoDB Settings
innodb_buffer_pool_size = 200M

Вам нужно открыть файл db.php и заменить на ваше соединение:
здесь
$db = new mysqli('localhost', 'root', '');
и тут
$db = new mysqli('localhost', 'root', '', 'vk');
(vk изменять не нужно)
