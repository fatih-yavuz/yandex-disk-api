<?php

include 'Disk.php';
use Yesilmadde\Disk;

$id = '';

$disk = new Disk($id);

$disk->downloadFile('your-file');