<?php

//mission_6-1 データベース接続プログラム
//実際に使用するときはファイル名をmission_6-1_DBconnect.phpに変更して下さい。

//データベース接続
$dsn = 'データベース名';
$user = 'ユーザ名'; 
$password = 'パスワード';
$pdo = new PDO($dsn,$user,$password);

//例外をスローしてくれる
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

?>
