<?php /*mission_6-1 スレッド検索結果表示ページ*/ 

include "mission_6-1_DBconnect.php"; //データベース接続
include "mission_6-1_logic.php"; //掲示板処理関数集
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" href="mission_6-1_common.css">
		<title>mission6_検索結果</title>
	</head>

<body>

<!--ページ共通ヘッダー-->
<?php include "mission_6-1_header.php";

echo "<div class=\"container\">";

//検索ワードの受け取り
$search = $_POST['search'];
$category_name = $_POST['category_name']; //カテゴリ名

if(empty($category_name)){
	//カテゴリ名が無いなら、カテゴリ関係なしの検索結果表示
	echo "<h4>スレッド検索結果</h4>";
}else{
	//カテゴリ名があるなら、カテゴリ内検索欄を表示する。
?>

<!--カテゴリ内検索入力欄-->
<form action="./mission_6-1_searchpage.php" method="post">
	カテゴリ内検索：<input type="text" name="search">
	<input type="hidden" name ="category_name" value="<?php echo $category_name; ?>">
	<input type="submit" value="カテゴリ内検索">
</form>

<?php
	echo "<h4>","$category_name","スレッド検索結果</h4>";	
}

//検索ワードがあるなら、検索する
if(strval($search) != ''){
	//検索を行う関数呼び出し
	$result = search($search,$category_name,$pdo);
	
	//スレッド検索結果表示
		echo "<dl>";

	//--検索結果を表示--
	$i=0; //表示件数カウント
	
	foreach ($result as $row){
		//スレッドにリンク出来るようにする
		echo "<dt><a href=\"./mission_6-1_thread-page.php?thread_name=".$row['title']."\">".$row['title']."</a></dt>";
		
		//投稿があるか確認する
		if($row['new_post'] != '0000-00-00 00:00:00'){
			//投稿ある
			
			echo "<dd>最終投稿：".$row['new_post']."</dd>";
		}else{
		 	//投稿無し
		 	echo "<dd>最終投稿：無し</dd>";
		}
		
		echo "<dd>".$row['agenda']."</dd><br><br>"; 
		$i++;
	}

	//検索結果がない場合
	if($i==0){
	echo "スレッドが見つかりません。<br>";
	}
	
}else{
	echo "検索ワードを入力して下さい<br>";
}
?>
</div>
</body>
</html>