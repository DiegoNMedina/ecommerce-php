<?php
echo "PHP funcionando correctamente";
echo "<br>REQUEST_URI: " . $_SERVER['REQUEST_URI'];
echo "<br>SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'];
echo "<br>PATH_INFO: " . (isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : 'No definido');