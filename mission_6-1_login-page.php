<?php /*mission_6-1 掲示板ログインページ*/ 

include "mission_6-1_DBconnect.php"; //データベース接続
include "mission_6-1_logic.php"; //掲示板処理関数集
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" href="mission_6-1_common.css">
		<title>mission6_ログイン</title>
	</head>
<body>

<?php 
//Gメニューとか
include "mission_6-1_header.php";

echo "<div class=\"container\">";
echo "<div class=\"main_login\">";

$type = $_POST['type']; //入力フォームかログイン結果画面かの判定
$id = $_POST['id'];
$pass = $_POST['pass'];

//セッション開始
session_start();

//不正アクセス防止
if($_SESSION['id'] == session_id()){
	echo "<p>ログイン中です。</p>";

}else if($type == "login"){
	//ログイン処理
	$result = login($id,$pass,$pdo);
	
	//処理結果のメッセージ表示
	if($result == -1){			
		//名前、パスワードの入力無し
		echo "パスワードまたは名前が入力されていません！<br>";
		echo "<form><input type=\"button\" value=\"ログインに戻る\" onclick=\"history.back()\"></form>";
		
	}else if($result == -2){
		//id,パスワードが間違っている
		echo "idまたはパスワードが間違っています。<br>";
		echo "<form><input type=\"button\" value=\"ログインに戻る\" onclick=\"history.back()\"></form>";
	
	}else if($result == -4){
		//idが数字ではない
		echo "idが数字ではありません。<br>";
		echo "<form><input type=\"button\" value=\"ログインに戻る\" onclick=\"history.back()\"></form>";

	}else if($result == 1){
		echo $_SESSION['name']."様のアカウントにログインしました！<br>";
		
	}else{
		echo "予期せぬエラーが起りました。<br>";
	}
	
}else{
	/*ログインフォーム*/ 
	?>
	<h1>ログインフォーム</h1>

	<p>idとパスワードを入力して、ログインして下さい！</p>

	<form action="" method="POST">
		<input type="text" name="id" placeholder="id">
		<input type="password" name="pass" placeholder="アカウントパスワード">
		<input type="hidden" value="login" name="type">
		<input type="submit" value="ログインする">
	</form>

	<p>アカウントが無い方はアカウントを作成してください</p>
	<input type="button" value="アカウントを作成する" onClick="location.href='./mission_6-1_account-page.php'">

<?php 
}
?>

</div>
</div>
</body>
</html>