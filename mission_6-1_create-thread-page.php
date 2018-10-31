<?php /*mission_6-1 掲示板スレッド作成ページ(スレッドを立てる)*/ 

include "mission_6-1_DBconnect.php"; //データベース接続
include "mission_6-1_logic.php"; //掲示板処理関数集
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" href="mission_6-1_common.css">
		<title>mission6_スレッド作成</title>
	</head>
<body>

<!--ページ共通ヘッダー-->
<?php include "mission_6-1_header.php";?>
<div class="container">

<h1>スレッド作成</h1>

<?php
$type = $_POST['type']; //ページが結果画面か入力フォームか示す
$title = $_POST['title']; //スレッドタイトル
$agenda = $_POST['agenda']; //スレッドの説明
$category = $_POST['category']; //カテゴリ名

//セッション開始
session_start();

//セッションがあるかチェック
if($_SESSION['id'] == session_id()){
	//ログイン中
	
	if($type=="after"){
		//ページタイプが結果なら、結果を表示
		
		//スレッド作成関数
		$result = create_thread($pdo,$title,$agenda,$category);
		
		//結果メッセージ
		if($result == -1){			
			//タイトル、スレッドの説明の入力無し
			echo "スレッドタイトルまたはスレッドの説明が入力されていません！<br>";
			echo "<form><input type=\"button\" value=\"スレッド作成に戻る\" onclick=\"history.back()\"></form>";
			
		}else if($result == -2){
			//カテゴリが選択されていない
			echo "カテゴリが選択されていません!<br>";
			echo "<form><input type=\"button\" value=\"スレッド作成に戻る\" onclick=\"history.back()\"></form>";
				
		}else if($result == -3){
			//予期せぬエラー
			echo "予期せぬエラーが起り、スレッドの作成に失敗しました。<br>";
			echo "<form><input type=\"button\" value=\"スレッド作成に戻る\" onclick=\"history.back()\"></form>";
		}else if($result == 1){
			//スレッド作成に成功
			echo "スレッドの作成に成功しました。<br>";
			echo "<a href=\"./mission_6-1_thread-page.php?thread_name="."$title"."\">"."作成したスレッドのページへ行く。<br>"."</a>";
		}
	
	}else{
		//--スレッド作成ページ表示--
		?>
		<p>必要事項を入力してスレッド作成ボタンを押すとスレッドを作成出来ます。</p>
		
		<form action="" method="post">
		<p>スレッドタイトル</p>
		<input type="text" name="title" paceholder="スレッドタイトル">
		<input type="hidden" name="type" value="after">
		
		<p>スレッドの説明</p>
		<textarea rows="5" cols="40" wrap="hand" name="agenda"></textarea><br> <!--コメント入力欄-->
		
		カテゴリ選択：
		<select name="category">
			<option value="業務">業務</option>
			<option value="学校生活">学校生活</option>
			<option value="家事">家事</option>
			<option value="アルバイト">アルバイト</option>
			<option value="部活動">部活動</option>
			<option value="経済">経済</option>
			<option value="人間関係">人間関係</option>
			<option value="勉強">勉強</option>
			<option value="健康">健康</option>
			<option value="悩み">悩み</option>
			<option value="雑談">雑談</option>
			<option value="趣味">趣味</option>
		</select><br><br>
		
		<input type="submit" value="スレッドを作成する">
		</form>


		
	<?php
	}
	
}else{
	//ログアウト中
	echo "スレッドを作成するにはログインして下さい。<br>";
}
?>

</div>
</body>
</html>