<?php/*mission_6-1ページの共通部分*/?>

<header class="head-color">

<div class="midasi">掲示板</div>

	<!--グローバルメニュー--> 
<nav class="global-nav">
	<ul class="nav-list">
		<li class="nav-item"><a href="mission_6-1_index.php">トップ</a></li>
		
		<?php
		//ログイン状態によって、Gメニューの項目を変更
		
		session_start();
		
		//ログイン中か判定
		if($_SESSION['id'] == session_id()){
			//ログイン中
			echo "<li class=\"nav-item\"><a href=\"mission_6-1_logout-page.php\">ログアウト</a></li>";
		}else{
			//ログアウト中
			echo "<li class=\"nav-item\"><a href=\"mission_6-1_login-page.php\">ログイン</a></li>";
		}
		?>
		<li class="nav-item"><a href="mission_6-1_category-page.php?category_name=none">カテゴリ</a></li>
		<li class="nav-item"><a href="mission_6-1_create-thread-page.php">スレッド作成</a></li>
		<li class="nav-item"><a href="mission_6-1_account-page.php">アカウント</a></li>	
		<li class="nav-item"><form action="./mission_6-1_search-page.php" method="post">
	　　スレッド検索：<input type="text" name="search"><input type="submit" value="検索">
　　	</form></li>
　　</ul>
　　</nav>
</header>