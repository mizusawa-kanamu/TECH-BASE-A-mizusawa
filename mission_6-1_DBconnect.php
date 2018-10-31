<?php

//mission_6-1 データベース接続プログラム

//データベース接続
$dsn = 'mysql:dbname=tt_251_99sv_coco_com;host=localhost';
$user = 'tt-251.99sv-coco.com'; 
$password = 'b6E3Phcg';
$pdo = new PDO($dsn,$user,$password);

//例外をスローしてくれる
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

?>