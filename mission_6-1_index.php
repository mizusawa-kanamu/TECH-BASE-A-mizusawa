<?php /*mission_6-1 掲示板トップページ*/ 

include "mission_6-1_DBconnect.php"; //データベース接続
include "mission_6-1_logic.php"; //掲示板処理関数集
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" href="mission_6-1_common.css">
		<link rel="stylesheet" href="mission_6-1_index.css">
		<title>mission6_トップページ</title>
	</head>
<body>

<!--ページ共通ヘッダー-->
<?php include "mission_6-1_header.php";?>

<div class="container">
	<h4>新着スレッド</h4>
	<?php
	//新着スレッド表示

	//新着スレッド取得
	$result = new_thread('',$pdo);

	$i=0; //表示件数取得用

	//新着スレッドを表示
	echo "<ul class=\"new\">";
	foreach ($result as $row){
		echo "<li>".$row['create_time'];
		echo "　"."<a href=\"./mission_6-1_thread-page.php?thread_name=".$row['title']."\">".$row['title']."</a></li>";
		$i++;
	}
	echo "</ul>";

	//新着スレッドがない場合
	if($i == 0){
		echo "新着スレッドはありません<br>";
	}
	?>

	<!--カテゴリリンク-->
	<?php include "mission_6-1_category.php"; ?>
</div>
</body>
</html>