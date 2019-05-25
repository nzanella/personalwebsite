<?php

$file = $_GET['file'];
echo '<code>' . file_get_contents('samplecode/' . $file) . '</code>';
//echo file_get_contents('samplecode/' . $file);
