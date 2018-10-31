<?php /*mission_6-1 掲示板ログアウトページ*/ 

include "mission_6-1_DBconnect.php"; //データベース接続
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" href="mission_6-1_common.css">
		<title>mission6_ログアウト</title>
	</head>
<body>

<!--ページ共通ヘッダー-->
<?php include "mission_6-1_header.php";?>
<div class="container">

<h1>ログアウト</h1>

<p>
<?php

//--ログアウト後の戻るボタン対処--
session_start();
if($_SESSION['id'] != session_id()){
	echo "<p>ログアウトしています。</p>";
}else{

	//ログアウトボタンを押したかのフラグ
	$logout = $_POST['logout'];

	if($logout=="logout"){
		//ログアウトボタン押した
		
		//セッション終了
		session_destroy();
		?>
		<p>ログアウトしました！</p>
		<input type="button" value="トップに戻る" onClick="location.href='./mission_6-1_index.php'">
		
	<?php
	}else{
		//これからログアウト
		
		//アカウント情報表示
		$image_pass = $_SESSION['thumb'];
		echo "<img src=\"$image_pass\" alt=\"サムネイル\">"."<br>";
		echo "アカウントid：";
		echo $_SESSION['acc_id']."<br>";
		echo "アカウント名：";
		echo $_SESSION['name'];
		?>
		</p>
		
		<?php //ログアウト選択フォーム  ?>
		<p>このアカウントからログアウトしますか？</p>
		<form action="" method="post">
		<input type="hidden" name="logout" value="logout">
		<input type="submit" value="はい">
		</form>

	<?php } 	
}?>
</div>
</body>
</html>