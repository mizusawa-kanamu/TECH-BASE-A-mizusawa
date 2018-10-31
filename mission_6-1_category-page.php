<?php /*mission_6-1 掲示板カテゴリページ*/ 

include "mission_6-1_DBconnect.php"; //データベース接続
include "mission_6-1_logic.php"; //掲示板処理関数集

$category_name = $_GET['category_name'];

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" href="mission_6-1_common.css">
		<link rel="stylesheet" href="mission_6-1_index.css">
		<title>mission6_カテゴリ</title>
	</head>
<body>

<?php 
//ページ共通ヘッダー
include "mission_6-1_header.php";

echo "<div class=\"container\">";

//データ受け取り
$category_name = $_GET['category_name'];

//Gメニューから来た場合は新着スレッド,カテゴリ検索欄は表示しない
if($category_name != none){
?>

	<!--カテゴリ内検索入力欄-->
	<form action="./mission_6-1_searchpage.php" method="post">
		カテゴリ内検索：<input type="text" name="search">
		<input type="hidden" name ="category_name" value="<?php echo $category_name; ?>">
		<input type="submit" value="カテゴリ内検索">
	</form>

	<h4><?php echo "$category_name"; ?><br>
	<?php echo "$category_name"; ?>内新着スレッド</h4>

	<?php
	//カテゴリ内新着スレッド表示
	$result = new_thread($category_name,$pdo);
	$i=0; //表示件数取得用

	//カテゴリ内新着スレッドを表示
	foreach ($result as $row){
		echo "<li>".$row['create_time'];
		echo "<a href=\"./mission_6-1_thread-page.php?thread_name=".$row['title']."\">".$row['title']."</a></li>";
		$i++;
	}

	//新着スレッドがない場合
	if($i == 0){
		echo "$category_name"."内新着スレッドはありません<br>";
	}
?>


<?php
}

//カテゴリ選択
include "mission_6-1_category.php";

?>

</div>
</body>
</html>