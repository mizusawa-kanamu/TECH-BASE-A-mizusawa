<?php /*mission_6-1 スレッド内容表示ページ*/ 

include "mission_6-1_DBconnect.php"; //データベース接続
include "mission_6-1_logic.php"; //掲示板処理関数集

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<link rel="stylesheet" href="mission_6-1_common.css">
			<link rel="stylesheet" href="mission_6-1_thread-page.css">
			<title><?php echo "$thread_name"; ?></title>
	</head>
<body>

<!--ページ共通ヘッダー-->
<?php include "mission_6-1_header.php";

//サイトのコンテンツを真ん中に寄せる
echo "<div class=\"container\">";

//データ受け取り
$thread_name = $_GET['thread_name'];

//get送信で受け取れなかったら、POST送信で受け取る
if($thread_name == ''){
	$thread_name = $_POST['thread_name'];
}


$type = $_POST['type']; //ページの機能フラグ
$comment = htmlspecialchars($_POST['comment']);	//コメント受け取り
$name = htmlspecialchars($_POST['name']); //名前受け取り
$pass = htmlspecialchars($_POST['pass']); //パスワード受け取り
$num = htmlspecialchars($_POST['num']); //指定番号受け取り
$category = $_POST['category']; //カテゴリ

$num2 = $num; //指定番号表示用
$i=0; //繰り返しカウンター


//ページ機能使用判定
if(!empty($type)){
	//機能実行フラグがある
	
	//ログインしているかチェック
	$login = login_check();
	if($login != -1){
		//ログイン中
		if($type == "toukou"){
			//新規投稿
			
			//投稿する関数呼び出し
			$toukou_result = new_post($pdo,$name,$comment,$pass,$thread_name);
			
		}else if($type == "edit_run"){
			//編集機能
			
			//編集する関数
			$edit_result = edit($pdo,$thread_name,$num,$name,$comment);

		}else if($type == "remove"){
			//削除機能

			//削除する関数
			$remove_result = remove($pdo,$thread_name,$num,$pass);
		}else if($type == "thread_remove"){
			//スレッド削除
			$thread_remove_result = thread_remove($pdo,$thread_name,$pass);
		
		}else if($type == "thread_edit_run"){
			//スレッド編集
			$thread_edit_result = thread_edit($pdo,$thread_name,$name,$comment,$category);
			$thread_name = $name; //現在のスレッド名を編集後のスレッド名にする
		
		}
	}
}
	
//スレッド情報取得関数
$result = get_thread($thread_name,$pdo);

//結果からデータを取得
foreach($result as $row){
	$title = $row['title']; //掲示板名
	$id = $row['id']; //作成者id
	$time = $row['create_time']; //作成年日時間
	$agenda = $row['agenda']; //スレッドの説明。
	$creater_name = $row['name']; //スレッド作成者名
	$i++;
}

if($result == -3 || $i == 0){
	//スレッド検索に失敗
	echo "<div class=\"message\">スレッドが見つかりません</div>";
	
}else{
	//スレッド検索に成功

	//--スレッドの概要を表示--
	echo "<div class=\"agenda\">";
	echo "<div class=\"thread_title\">"."$title"."<br></div>";
	echo "作成者："."$creater_name"."　作成日："."$time"."<br><br>";
	echo "<fieldset class=\"section\">";
	echo "	<legend><div class=\"section_title\">スレッドの説明</div></legend>";
	echo "$agenda"."</fieldset>";
	echo "</div>";


	//--投稿を表示する--

	//スレッド内容取得関数
	$result = read_thread($thread_name,$pdo);

	if($result == -3){
		echo "予期せぬエラーが起りました。<br>";
	}else{
		//スレッド内容を表示
		foreach($result as $row){
			echo "<div class=\"toukou\">". $row['num'].".　".$row['name']."　".$row['post_time']."</div>";
			echo "<div class=\"balloon\">";
			echo "<div class=\"faceicon\">";
			echo "<image src=\"".$row['thumb']."\">";
			echo "</div>";
			echo " <div class=\"chatting\">";
			echo " <div class=\"says\">";
			echo "<p>".$row['comment'];
			
		
			
			$num = $row['num'];
			
			//動画像のパスを取得する関数
			 $image_pass = get_DBpass($pdo,$thread_name,$num);
			 
			//パスがあれば、動画像を表示する
			if($image_pass != ''){
				//パスがある
				
				//画像か動画か調べる
				$result = pass_type($image_pass);
				
				if($result == "video"){
					//動画表示
					
					//リサイズしたサイズを取得
					$size =  video_resize($image_pass);
					
					//動画を表示
					echo "<br><br>"."<video src=\"$image_pass\" controls height=\"$size[1]\" width=\"$size[0]\"></video>";
					
				}else if($result == "image"){
					//画像表示
					
					echo "<br><br>"."<image src=\""."$image_pass"."\">";
				}
			}
		
			echo "</p>";
			echo "</div>";
			echo "</div>";
			echo "</div>";	
		
		}
	}

	//新規投稿ボタン
	?>
	<div class="form_button">
		<div class="form_conf">
			<form action="./mission_6-1_thread-page.php?thread_name=<?php echo $thread_name; ?>" method="post" class="form_padding">
				<input type="submit" value="新規投稿をする">
				<input type="hidden" name="type" value="new_post">
				<input type="hidden" name="thread_name" value="<?php echo $thread_name; ?>">
			</form>

			<?php
			//編集・削除ボタン
			?>

			<form action="./mission_6-1_thread-page.php?thread_name=<?php echo $thread_name; ?>" method="post" class="form_padding">
				<input type="submit" value="編集削除をする">
				<input type="hidden" name="type" value="edit">
				<input type="hidden" name="thread_name" value="<?php echo $thread_name; ?>">
			</form>
			
			
			<form action="./mission_6-1_thread-page.php?thread_name=<?php echo $thread_name; ?>" method="POST" class="form_padding">
				<input type="hidden" name="type" value="thread_edit">
				<input type="submit" value="スレッドを編集・削除する">
			</form>
		</div>

	<?php

	if($type != ''){
		//何か処理をしたなら、スクロール位置は処理ボタンの所から
		echo "<div id=\"target\">";
		
		
		//ログインしているかチェック
		$login = login_check();
		if($login == -1){
			//ログアウト中
			echo "<br><br>投稿機能を使用するにはログインする必要があります。<br>";
			echo "<input type=\"button\" value=\"ログインページへ行く\" onClick=\"location.href='./mission_6-1_login-page.php'\"><br>";
		}else{
			//ログイン中

			//新規投稿ボタンが押されたら投稿フォームを表示
			if($type == "new_post"){
				//入力フォーム
				?>
					<form action="./mission_6-1_thread-page.php?thread_name=<?php echo $thread_name; ?>" method="POST" enctype="multipart/form-data">
						<br>
						名前：<input type="text" name="name" value="<?php echo $_SESSION['name']; ?>"><br>
						コメント：<textarea rows="5" cols="40" wrap="hand" name="comment"></textarea><br>
						パスワード：<input type="password" name="pass">
						<input type="hidden" name="type" value="toukou"><br><br>
						<input type="hidden" name="thread_name" value="<?php echo $thread_name; ?>">
						<p>※画像はjpg,png形式、動画はmp4形式に対応しています。<br>
							データサイズが大きいと投稿に時間がかかります。</p>
						画像または動画の投稿：<input type="file" name="file"><br><br>
						<input type="submit" value="投稿">
					</form>
			<?php	
			}else if($type == "edit"){
				//編集・削除ボタンが押された
				
				//編集・削除フォームを出す
				?>
				<form action="./mission_6-1_thread-page.php?thread_name=<?php echo $thread_name; ?>" method="POST">
					<br>
					編集<input type="radio" name="type" value="edit_form">
					削除<input type="radio" name="type" value="remove"><br>
					指定番号：<input type="text" name="num"><br>
					パスワード：<input type="password" name="pass">
					<input type="hidden" name="thread_name" value="<?php echo $thread_name; ?>">
					<input type="submit" value="送信">
				</form>
			<?php
			}else if($type == "edit_form"){
				//編集フォーム
				
				//編集フォームにデータを戻す関数p
				$name = edit_form($pdo,$thread_name,$num2,"name",$pass);
				$come = edit_form($pdo,$thread_name,$num2,"comment",$pass);
				
				//エラーの場合は戻すフォームデータに数字が入っている。
				if($name == -2){
					//パスが違う
					echo "パスワードが違います。<br>";
					
				}else if($name == -1){
					//パスワードが入力されていない
					echo "パスワードが入力されていません。<br>";
					
				}else if($name == -4){
					//指定番号が数字ではない
					echo "数字が入力されていません。<br>";
					
				}else{
					//編集フォームを出す
					?>
					
					<form action="./mission_6-1_thread-page.php?thread_name=<?php echo $thread_name; ?>" method="POST" enctype="multipart/form-data">
						<br>
						名前：<input type="text" name="name" value="<?php echo $name; ?>"><br>
						コメント：<textarea rows="5" cols="40" wrap="hand" name="comment"><?php echo "$come"; ?></textarea><br>
						<input type="hidden" name="type" value="edit_run"><br><br>
						<input type="hidden" name="thread_name" value="<?php echo $thread_name; ?>">
						<input type="hidden" name="num" value="<?php echo $num2; ?>">
						<p>※画像はjpg,png形式、動画はmp4形式に対応しています。</p>
						画像または動画：<input type="file" name="file"><br><br>
						<input type="submit" value="編集">
					</form>
				<?php
				}
			}else if($type == "thread_edit"){
				//スレッド編集
				
				$result = db_search($pdo,'thread_board','title',$thread_name); //スレッド管理dbのデータ取得
				
				foreach($result as $row){
					//フォームに戻す値を取得
					$form_name = $row['title'];
					$form_agenda = $row['agenda'];
					$form_category = $row['category'];
				}
				
				?>
				<form action="./mission_6-1_thread-page.php?thread_name=<?php echo $thread_name; ?>" method="post">
					<br>
					<p>編集をする場合は各項目の入力お願いします</p>
					編集<input type="radio" name="type" value="thread_edit_run">
					削除<input type="radio" name="type" value="thread_remove"><br>
					
					<p>スレッドタイトル</p>
					<input type="text" name="name" paceholder="スレッドタイトル" value="<?php echo $form_name; ?>">
					
					<p>スレッドの説明</p>
					<textarea rows="5" cols="40" wrap="hand" name="comment"><?php echo "$form_agenda"; ?></textarea><br> <!--コメント入力欄-->
					
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
						<option value="<?php echo $form_category; ?>"selected><?php echo "$form_category"; ?></option>
					</select><br><br>
			
					<input type="submit" value="送信">
				</form>
				
				<?php
			}


			//新規投稿処理結果を表示
			if($type == "toukou"){
				if($toukou_result == 1){
					echo "<br><br>新規投稿しました！<br>";
				}else if($toukou_result == -1){
					echo "<br><br>名前かコメントが入力されていません。<br>";
				}else if($toukou_result == -2){
					echo "<br><br>パスワードが入力されていません。<br>";
				}else if($toukou_result == -3){
					echo "<br><br>投稿時に予期せぬエラーが起りました。<br>";
				}
				
			}

			//削除機能処理結果を表示
			if($type == "remove"){
				if($remove_result == 1){
					echo "<br><br>"."$num2"."番目の投稿を削除しました！<br>";
				}else if($remove_result == -4){
					echo "<br><br>数字が入力されていません。<br>";
				}else if($remove_result == -2){
					echo "<br><br>パスワードが違います。<br>";	
				}else if($remove_result == -1){
					echo "<br><br>パスワードが入力されていません。<br>";
				}else if($remove_result == -3){
					echo "<br><br>投稿時に予期せぬエラーが起りました。<br>";
				}
				
			}

			//編集機能処理結果を表示
			if($type == "edit_run"){
				if($edit_result == 1){
					echo "<br><br>"."$num2"."番目の投稿を編集しました！<br>";
				}else if($edit_result == -1){
					echo "<br><br>名前またはコメントが入力されていません。<br>";
				}else if($edit_result == -3){
					echo "<br><br>編集時に予期せぬエラーが起りました。<br>";
				}
				
			}
			
			//スレッド削除機能処理結果を表示
			if($type == "thread_remove"){
				if($thread_remove_result == -1){
					echo "<br><br>"."投稿者のみ削除することが出来ます。";
				}else if($thread_remove_result == -3){
					echo "<br><br>予期せぬエラーが起りました。<br>";
				}
				
			}
			
		

			//何か処理をしたなら、スクロール位置は処理ボタンの所から
			echo "</div>";
		}
	}


	if($type != ''){
	?>

		<script type="text/javascript">
		//表示位置を指定するスクリプト
		var targetElement = document.getElementById( "target" ) ;

			var clientRect = targetElement.getBoundingClientRect() ;

			// 画面内の位置
			var x = clientRect.left ;
			var y = clientRect.top ;

			// ページ内の位置
			var px = window.pageXOffset + clientRect.left ;
			var py = window.pageYOffset + clientRect.top ;

			window.scrollTo(px,py);
			
	<?php
	}
	?>

	</script>
	<?php /*form_buttonの閉じタグ*/ ?>
	</div>
	<?php/*containerの閉じタグ*/ ?>
	</div>
	
<?php
//スレッド検索成功の閉じ括弧
}

?>
</body>
</html>