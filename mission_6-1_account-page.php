<?php /*mission_6-1 掲示板アカウントページ*/ 

//掲示板処理に必要な関数集
include "mission_6-1_logic.php";
include "mission_6-1_DBconnect.php"; //データベース接続
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" href="mission_6-1_common.css">
		<title>mission6_アカウント</title>
	</head>
<body>

<!--ページ共通ヘッダー-->
<?php include "mission_6-1_header.php";?>
<div class="container">

<h1>アカウント</h1>

<?php

//データ受け取り
$type = $_POST['type']; //ページで使用する機能の種類
$edit_flag = $_POST['edit_flag']; //編集フラグ
$name = $_POST['name'];
$pass = $_POST['pass'];

//セッション開始
session_start();

//セッションがあるかチェック
if($_SESSION['id'] == session_id()){
	//ログイン中
	
	//--編集フラグがonなら編集する--
	if($edit_flag == "on"){
		edit_acc($name,$pass,$pdo);
	}
	
	//--アカウント情報を表示する--
	//アカウントのサムネイル表示
	$image_pass = $_SESSION['thumb'];
	
	//キャッシュ更新のため、画像後ろに更新日付をつける。
	$time = date("YmdHis");
	echo "<img src=\"$image_pass?$time\" alt=\"サムネイル\">"."<br><br>";
	
	//アカウントidとアカウント名を表示
	echo "アカウントid:".$_SESSION['acc_id']."<br>";
	echo "アカウント名:".$_SESSION['name']."<br>";
	
	//--アカウントページの機能--
	echo "<p>アカウント編集</p>";
	
	if($type == "edit"){
		//編集ボタンをおされたら編集フォーム表示
	?>
		<form action="" method="post" enctype="multipart/form-data">
	名前：<input type="text" name="name" value="<?php echo $_SESSION['name']; ?>"><br>
	パスワード：<input type="text" name="pass" value="<?php echo $_SESSION['pass']; ?>"><br>
	<input type="hidden" name="edit_flag" value="on">
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
	サムネイル画像：<input type="file" name="file">
	<input type="submit" value="送信">
	</form>

	
	<?php	
	}else{
		//アカウント編集ボタン
		?>
		<form action="" method="post">
		<input type="hidden" name="type" value="edit">
		<input type="submit" value="編集する">
		</form>
		
<?php
	}

}else{
	//ログアウト中
	
	//アカウント作成フラグがあるなら、アカウント作成
	if($type == "create"){
		//アカウント作成
		$result = create_acc($name,$pass,$pdo);
		
		//処理結果のメッセージ表示
		if($result == -1){			
			//名前、パスワードの入力無し
			echo "パスワードまたは名前が入力されていません！<br>";
			echo "<form><input type=\"button\" value=\"アカウント作成に戻る\" onclick=\"history.back()\"></form>";
			
		}else if($result == -2){
			//画像がアップロードされていない
			echo "サムネイルが設定されていません!<br>";
			echo "<form><input type=\"button\" value=\"アカウント作成に戻る\" onclick=\"history.back()\"></form>";
				
		}else if($result == -3){
			//アップロードされた画像ではない
			echo "jpgまたはpng形式ではありません。<br>";
			echo "<form><input type=\"button\" value=\"アカウント作成に戻る\" onclick=\"history.back()\"></form>";
			
		}else if($result == -4){
			//DBに挿入できない
			echo "予期せぬエラーでアカウントの作成に失敗しました。<br>";
			echo "<form><input type=\"button\" value=\"アカウント作成に戻る\" onclick=\"history.back()\"></form>";
			
		}else if($result == 1){
			//アカウント作成成功
			echo "アカウントを作成しました！<br><br>";
			echo "アカウントidはログインする時に必要となるので、メモして下さい。"."<br>";
			
			$id = rows($pdo,account);
			
			echo "アカウントid："."$id"."<br>";
		}
	}else{
	//アカウント作成フォーム
	?>
	
	<p>ログインされていません！</p>

	<p>アカウントが無い方は<br>
	名前とパスワード、サムネイルを入力して、アカウントを作成してください。</p>
	<p>サムネイル画像は10MBまでです。<br>
	jpeg,png以外は使えません。</p>

	<form action="" method="POST" enctype="multipart/form-data">
		<input type="text" name="name" placeholder="アカウント名">
		<input type="password" name="pass" placeholder="アカウントパスワード">
		<input type="hidden" name="type" value="create"><br>
		
		<?php /*ファイルの容量制限*/ ?> 
		<input type="hidden" name="MAX_FILE_SIZE" value="10000000"><br>
		<input type="file" name="file" placeholder="サムネイル画像"> 
		<input type="submit" value="アカウントを作成する">
	</form>

<?php 
	} 
}

?>
</div<
</body>
</html>