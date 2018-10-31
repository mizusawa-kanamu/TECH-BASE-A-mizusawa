<?php /*mission_6-1 掲示板処理関数集*/ 

/*
*関数のプロトタイプ

function a($,$){
	
	
}
*/

//----名前パスワード入力チェック関数----
function check($name,$pass){

	//名前かパスワードが空白なら、-1を返す
	if(strval($name)=='' || strval($pass)==''){
		return -1;
	}else{
		return 1;
	}	
}

//----画像の送信チェック関数----
function im_check(){
	//画像が送信されたら1を返す。それ以外は-1を返す。
	if(is_uploaded_file($_FILES['file']['tmp_name'])){
		return 1;
	}else{
		return -1;
	}
}

//----画像の拡張子チェック関数----
function im_type($file){
	//ファイルの種類を取得
	$file_type = getimagesize($file);

	if(IMAGETYPE_PNG == $file_type[2]){
		//png形式、pngを返す
		return "png";
	 }else if(IMAGETYPE_JPEG == $file_type[2]){
	 	//jpeg形式jpgを返す
		return "jpg";
	}else{
		//それ以外。-1を返す
		return -1;
	}
}

//--指定したDBの行数を取得する関数--
function rows($pdo,$table){

	//行数の取得処理
	try{
		$sql = "SELECT COUNT(*) FROM $table"; //行数の取得をするsql
		$results =  $pdo -> query($sql);
		$rows = $results -> fetchColumn(); //データベースの行数を取得
	}catch(Exception $e){
		var_dump($e->getMessage());
		echo "rows<br>";
		return -1;
	}
	return $rows;
	
}

//--サムネイル作成関数(jpg) 返り値は保存した画像へのパス--
function thumb_jpg($file,$acc_id){
	//正方形にして保存する(サムネイル)
	//--画像をリサイズする--

	//元の画像のサイズを取得する
	list($w, $h) = getimagesize($file);

	//元画像の縦横の大きさを比べてどちらかにあわせる
	if($w > $h){
	    $diffW = $h;
	    $diffH = $h;
	}elseif($w < $h){
	    $diffW = $w;
	    $diffH = $w;
	}elseif($w === $h){
	    $diffW = $w;
	    $diffH = $h;
	}
	//サムネイルのサイズ
	$thumbW = 100;
	$thumbH = 100;

	//サムネイルになる土台の画像を作る(黒背景)
	$thumbnail = imagecreatetruecolor($thumbW, $thumbH);

	//元の画像を読み込む
	$baseImage = imagecreatefromjpeg($file);
	//サムネイルになる土台の画像に合わせて元の画像を縮小しコピーペーストする
	imagecopyresampled($thumbnail, $baseImage, 0, 0, 0, 0, $thumbW, $thumbH, $diffW, $diffH);

	//画像ファイルの名前はidと同じ。
	$acc_thumb ="./image/"."$acc_id".".jpg";

	//圧縮率100で保存する
	imagejpeg($thumbnail, "$acc_thumb", 100);
	return $acc_thumb;
}
	
//--サムネイル作成関数(png) 返り値は保存した画像へのパス--
function thumb_png($file,$acc_id){
	//PNG画像の場合
	//--画像をリサイズする--

	//元の画像のサイズを取得する
	list($w, $h) = getimagesize($file);

	//元画像の縦横の大きさを比べてどちらかにあわせる
	if($w > $h){
	    $diffW = $h;
	    $diffH = $h;
	}elseif($w < $h){
	    $diffW = $w;
	    $diffH = $w;
	}elseif($w === $h){
	    $diffW = $w;
	    $diffH = $h;
	}
	//サムネイルのサイズ
	$thumbW = 100;
	$thumbH = 100;

	//サムネイルになる土台の画像を作る(黒背景)
	$thumb = imagecreatetruecolor($thumbW, $thumbH);

	//--pngの透過部分をそのまま保存する処理--
	//ブレンドモードを無効にする
	imagealphablending($thumb, false);
	//完全なアルファチャネル情報を保存するフラグをonにする
	imagesavealpha($thumb, true);

	//元の画像を読み込む
	$baseImage = imagecreatefrompng($file);
	//サムネイルになる土台の画像に合わせて元の画像を縮小しコピーペーストする
	imagecopyresampled($thumb, $baseImage, 0, 0, 0, 0, $thumbW, $thumbH, $diffW, $diffH);

	//画像ファイルの名前はidと同じ。
	$acc_thumb ="./image/"."$acc_id".".png";

	//保存する
	imagepng($thumb,"$acc_thumb",9);
	return $acc_thumb;
}

//アカウント作成関数
function create_acc($name,$pass,$pdo){
	
	//名前、パスワードの入力チェック
	$result = check($name,$pass);
	if($result == -1){	
		//名前入力無し
		return -1;
	}
	
	//画像送信チェック
	$result = im_check();
		if($result == -1){	
		//画像送信されていない
		return -2;
	}
	
	//画像受け取り
	$file = $_FILES['file']['tmp_name'];
	
	//行数の取得
	$rows = rows($pdo,account);
	$rows++;
	
	//画像の拡張子チェック
	$result = im_type($file);
	if($result == "jpg"){
		$thumb = thumb_jpg($file,$rows);
		
	}else if($result == "png"){
		$thumb = thumb_png($file,$rows);
		
	}else{
		//画像では無い
		return -3;
	}

	try{
	//アカウントDBにアカウント情報を挿入
	$sql = "INSERT INTO account(acc_id,acc_name,acc_password,acc_thumb) VALUES($rows,'$name','$pass','$thumb')";

	//sql実行
	$rezult = $pdo -> query($sql);

	}catch(Exception $e){
	  var_dump($e->getMessage());
	  echo "create_acc<br>";
	  return -4;
	}
	return 1;
}

//アカウント編集関数
function edit_acc($name,$pass,$pdo){
	//セッション変数の受け取り
	$id = $_SESSION['acc_id'];

	//--名前とパスワード更新--

	//名前入力チェック
	if(strval($name) != ''){
		//名前更新
		
		//アカウントidを参照して、名前を更新
		$sql = "update account set acc_name='$name' where acc_id = $id";

		try{
			$results =  $pdo -> query($sql);
		}catch(Exception $e){
			var_dump($e->getMessage());
		}
		
		//セッション変数も更新
		$_SESSION['name'] = $name;
	}

	//パスワード入力チェック
	if(strval($pass) != ''){
		//パスワード更新
		
		//アカウントidを参照して、パスワードを更新
		$sql = "update account set acc_password='$pass' where acc_id = $id";

		try{
			$results =  $pdo -> query($sql);
		}catch(Exception $e){
			var_dump($e->getMessage());
		}
		
		//セッション変数も更新
		$_SESSION['pass'] = $pass;
	}

	//--画像更新--

	//画像送信判定
	$result = im_check();

	if($result == 1){
		//画像あり	
		$file = $_FILES['file']['tmp_name'];

		//画像の拡張子チェック
		$result = im_type($file);

		if($result == "jpg"){
			//jpgのサムネイル作成
			$thumb = thumb_jpg($file,$id);

		}else if($result == "png"){
			//pngのサムネイル作成
			$thumb = thumb_png($file,$id);
			
		}else{
			//画像では無い
			return -3;
		}
		
		//DBの更新

		//画像パスを更新
		$sql = "update account set acc_thumb='$thumb' where acc_id = $id";

		try{
			$results =  $pdo -> query($sql);
		}catch(Exception $e){
			var_dump($e->getMessage());
			echo "edit-image<br>";
		}

		//セッション変数の更新
		$_SESSION['thumb'] = $thumb;
	}
}

//新着スレッドを取得する関数
function new_thread($category_name,$pdo){

	if(empty($category_name)){
	//カテゴリ名が無いなら、カテゴリ関係なく新着スレッド表示
	
	//スレッドを新着の順番になるように取得
	$sql = "SELECT * FROM thread_board order by create_time DESC";
	}else{
	//カテゴリ内新着表示
	$sql = "SELECT * FROM thread_board where category='$category_name' order by create_time DESC";
	}
	
	try{
	$result = $pdo -> query($sql);

	}catch(Exception $e){
		var_dump($e->getMessage());
		echo "newthread<br>";
	}
	
	//戻り値はsql実行結果
	return $result;
	
}


//--ログイン処理関数--
function login($id,$pass,$pdo){
	//idと名前の入力チェック
	$result = check($id,$pass);
	if($result == -1){	
		//名前かid入力無し
		return -1;
	}
	
	//idが番号チェック
	if(!is_numeric($id)){
		//数字ではない
		return -4;
	}
	
	//データベースにアカウントがあるか確認
	try{
		$sql = "SELECT * FROM account WHERE acc_id=$id AND acc_password='$pass'";
		$results = $pdo -> query($sql);
	}catch(Exception $e){
		var_dump($e->getMessage());
		echo "login<br>";
	}
	
	$i=0;
	//結果からデータ読み出し
	foreach($results as $row){
		$acc_id = $row['acc_id'];
		$acc_pass = $row['acc_pass'];
		$acc_name = $row['acc_name'];
		$acc_thumb = $row['acc_thumb'];;
		//読み出した行数をカウント
		$i++;
	}

	if($i == 1){
		//読み出した行が1つなら、ログイン成功
		
		//--セッション変数にデータを書き込む--
		$_SESSION['id'] = session_id();
		$_SESSION['name']=$acc_name;
		$_SESSION['acc_id']=$acc_id;
		$_SESSION['pass']=$acc_pass;
		$_SESSION['thumb']=$acc_thumb;
		return 1;

	}else{
		//読み出した行が1つ以外なら、ログイン失敗
	return -2;
	}
}

//--検索関数--
function search($search,$category_name,$pdo){
		try{
	
		if(empty($category_name)){
		//カテゴリ名が無いなら、カテゴリ関係なく検索
		//検索ワードが含まれるスレッドタイトルを最新の投稿順に取得
		$sql = "SELECT * FROM thread_board WHERE title LIKE '%$search%' order by new_post DESC";
		
		}else{
		//カテゴリ名があるなら、カテゴリ内検索
		$sql = "SELECT title,new_post,category FROM thread_board WHERE category='$category_name' AND title LIKE '%$search%' order by new_post DESC";
		}
		
		$result = $pdo -> query($sql);
	}catch(Exception $e){
		//エラーを表示
		var_dump($e->getMessage());
		echo "serch<br>";
		return -1;
	}
	
	return $result;
}

//スレッド作成関数
function create_thread($pdo,$title,$agenda,$category){
	//データを取得
	$time = date("Y-m-d H:i:s"); //スレッド作成年日時間
	$id = $_SESSION['acc_id']; //作成者者id
	$pass = $_SESSION['pass']; //作成者パスワード
	$name = $_SESSION['name']; //作成者名
	$new_post = '0000-00-00 00:00:00'; //最新の投稿時間
	
	//データの入力チェック
	$result = check($title,$agenda);
	
	if($result == -1){
		//タイトルかスレッドの説明を書いていない
		return -1;
	}
	
	$result = check($category,"pass");
	
	if($result == -1){
		//カテゴリを選択していない
		return -2;
	}
	
	//--投稿を保存するテーブル作成--
	try{
		$sql = "CREATE TABLE $title(id INT,comment TEXT,pass TEXT,post_time datetime,name text,num INT,image TEXT,thumb TEXT);";
		$stmt = $pdo->query($sql);

	}catch(Exception $e){
	  return -3;
	}

	//--スレッド管理DBに作成したスレッドを入力--
	try{
		$sql = "INSERT INTO  thread_board(id,name,password,title,create_time,category,new_post,agenda) VALUES($id,'$name','$pass','$title','$time','$category','$new_post','$agenda')";
		$rezult = $pdo -> query($sql);

	}catch(Exception $e){
	   return -3;
	}
	
	return 1;
	
}


//スレッド情報取得関数
function get_thread($thread_name,$pdo){

	try{
		//指定されたスレッド名の情報を取得
		$sql = "SELECT * FROM thread_board where title='$thread_name'";
		$result = $pdo -> query($sql);

	}catch(Exception $e){
		return -3;
	}
	
	return $result;

}

//スレッド内容取得関数
function read_thread($thread_name,$pdo){
	
	//スレッド内容を読み出し
	try{
		$sql = "SELECT * FROM $thread_name ORDER BY num ASC";
		$result= $pdo -> query($sql);

	}catch(Exception $e){
	var_dump($e->getMessage());
	echo "スレッド内容取得関数<br>";
		return -3;
	}
	return $result;

}

//----ログインチェック関数----
function login_check(){
	session_start();
	
	//セッションidがサーバとクライアントで同じか確かめる
	if($_SESSION['id'] == session_id()){
		//ログイン中
		return 1;
	}else{
		//ログアウト中
		return -1;
	}

}

//投稿する関数
function new_post($pdo,$name,$comment,$pass,$thread_name){
	//データを取得
	$post_time = date("Y-m-d H:i:s"); //投稿年日時間
	$id = $_SESSION['acc_id']; //投稿者id
	$name = $_SESSION['name']; //投稿者名
	
	//サムネイルのパスを取得
	//指定したidのアカウント情報が返ってくる
	$result = serch_acc($pdo,$id);
	
	//サムネイルのパスを取り出し
	foreach($result as $rows){
		$thumb = $rows['acc_thumb'];
	}
	
	//投稿番号取得
	$num = rows($pdo,$thread_name);
	$num++;

	//名前とコメントのチェック
	$result = check($name,$pass);
	if($result == -1){
		//名前、コメントが空白
		return -1;
	}

	//パスワードチェック
	$result = check($comment,"test");
	if($result == -1){
		//パスワードが空白
		return -2;
	}

	//動画像をサーバに保存し、パスを取得
	//画像、動画が送信されたか確認
	$result = im_check();
	
	if($result == 1){
		//送信されたら、動画像パスを主取得
		$im_pass = get_impass();
	
	}else{
		//送信が無かったら、パスは空にする
		$im_pass ='';
	
	}
	
	try{
		//投稿するsql文
		$sql = "INSERT INTO $thread_name(id,comment,pass,post_time,name,num,image,thumb) VALUES($id,'$comment','$pass','$post_time','$name',$num,'$im_pass','$thumb')";
		//sql実行
		$rezult = $pdo -> query($sql);

	}catch(Exception $e){
	  var_dump($e->getMessage());
	  return -3;
	}
	
	//処理が成功したことを示す
	
	//最終投稿更新
	last_post($pdo,$thread_name,$post_time);
	return 1;
}

//----動画像投稿関数----
function get_impass(){
	//アップロードされた動画像のパスを返す
	
	//ファイルの種類を調べる
	
	//動画か調べる
	$type = $_FILES['file']['type'];
	$file = $_FILES['file']['tmp_name'];

	if($type == "video/mp4"){
		//mp4の処理
		
		//ファイル名作成
		$hash = sha1_file($file);
		
		//ファイルパス生成
		$pass = "./video/$hash.mp4";
		
		//アップロードされたファイルを指定の場所へ移動
		move_uploaded_file($file, "$pass");
		
		return $pass;
	}else{
		//画像である
		$type = im_type($file);
		
		if($type == "jpg"){
			//jpgの処理
			
			//--ファイル名(ハッシュ値)取得（掲示板の場合）--
			$hash = sha1_file($file);
			
			//--ファイルサイズ計算--
			//元画像のサイズを取得
			list($newwidth, $newheight) = getimagesize($file);
			
			//新規サイズ計算
			$x = $newwidth/$newwidth;
			$y = $newwidth/$newheight;

			while($newwidth > 600 || $newheight > 600){
			//サイズを小さくするループ(500以上だったら、500に近いサイズにする)
			$newwidth = $newwidth - $y;
			$newheight = $newheight - $x;
			}
			
			// 元画像サイズを取得
			list($width, $height) = getimagesize($file);

			//新規サイズの黒い画像を作成
			$thumb = imagecreatetruecolor($newwidth, $newheight);
			

			//指定サイズの黒い画像を作成
			$thumb = imagecreatetruecolor($newwidth, $newheight);
			
			//引数のパスに画像リソースを確保
			$source = imagecreatefromjpeg($file);

			// リサイズ
			imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
			
			//保存先パス
			$pass = "./image/"."$hash".".jpg";
			
			// ディレクトリへ画像を出力
			imagejpeg($thumb,"$pass",90);
			
			//パスを返す
			return $pass;
	
		}else if($type == "png"){
			//pngの処理
			
			//--ファイル名(ハッシュ値)取得（掲示板の場合）--
			$hash = sha1_file($file);

			//--ファイルサイズ計算--
			//元画像のサイズを取得
			list($newwidth, $newheight) = getimagesize($file);
			
			//新規サイズ計算
			$x = $newwidth/$newwidth;
			$y = $newwidth/$newheight;

			while($newwidth > 600 || $newheight > 600){
			//サイズを小さくするループ(500以上だったら、500に近いサイズにする)
			$newwidth = $newwidth - $y;
			$newheight = $newheight - $x;
			}

			// 元画像サイズを取得
			list($width, $height) = getimagesize($file);

			//新規サイズの黒い画像を作成
			$thumb = imagecreatetruecolor($newwidth, $newheight);
			
			
			//--ファイルリサイズ--
			//指定サイズの黒い画像を作成
			$thumb = imagecreatetruecolor($newwidth, $newheight);
			
			//--pngの透過部分をそのまま保存する処理--
			//ブレンドモードを無効にする
			imagealphablending($thumb, false);
			//完全なアルファチャネル情報を保存するフラグをonにする
			imagesavealpha($thumb, true);
			
			//引数のパスに画像リソースを確保
			$source = imagecreatefrompng($file);

			// リサイズ
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
			
			//保存先のパス
			$pass = "./image/"."$hash".".png";
			
			// ディレクトリへ画像を出力
			imagepng($thumb,"$pass",9);
			
			//画像パスを返す
			return $pass;
		}else{
			//画像か動画では無い
			
			//空白を返す
			$pass = '';
			return $pass;
		}
	}
}	

//アカウント情報取得関数
function serch_acc($pdo,$id){
	
	try{
		$sql = "SELECT * FROM account WHERE acc_id = $id";
		$result= $pdo -> query($sql);
	}catch(Exception $e){
		return -1;
	}
	return $result;
}

//----DBから動画像のパスを取り出す関数----
function get_DBpass($pdo,$table,$num){

	//データベースからデータを取り出し
	$result = DB_data($pdo,$table,$num);
	
	//検索結果からパスを取り出し
	foreach($result as $row){
		$image_pass =  $row['image'];
	}
	
	//パスを返す
	return $image_pass;
	
}


//--指定したDBの指定した行のデータを取得する関数--
function DB_data($pdo,$table,$num){

	$sql = "SELECT * FROM $table WHERE num=$num";
	$result= $pdo -> query($sql);
	
	return $result;
}

//パスから画像か動画か調べる
function pass_type($im_pass){
	//文字列を分割して、拡張子を取り出してそれで判定
	
	$char = explode("/",$im_pass);
	
	//拡張子は[1]に入る
	$image_type = explode(".",$char[2]);
	
	
	if($image_type[1] == "mp4")
		return "video";
	else
		return "image";
}

//動画のリサイズ
function video_resize($pass){
	
	//ファイルの受け取り
	$file = $_FILES['file']['tmp_name'];
	
	$cmd = "ffmpeg -i $pass 2>&1";
	//2>&1はエラー出力を標準出力するってことだそうです。

	exec($cmd,$out);
	//execコマンドで$cmdを実行。結果を$outに出力

	for ($h = 0; $h < count($out); $h++) {
	//$outに出力された値は配列に格納されるので、forで配列を一つづつ確認する。
	     if(preg_match('/([0-9]{3,4})x([0-9]{2,4})/', $out[$h])){
	     //preg_match関数で176x144のような結果が$out[$h]にあるかどうかを確認。
	     //正規表現を使っているので'//'で囲む。
	     //([0-9]{3,4})の{}の数字は文字数3または4を示す。
	     //[0-9]:数字0～9の一文字にマッチ
	     //{n,m}:直前の表現をn～m回繰り返す。

	        if(!preg_match('/([0-9]{5,})x([0-9]{5,})/', $out[$h])){
	        //{5,}は今のところ解像度で5桁はないだろうから!で5桁以上を除外。
	           preg_match('/([0-9]{3,4})x([0-9]{2,4})/', $out[$h],$PregMatch,PREG_OFFSET_CAPTURE);
	           //3番目の要素($PregMatch)に検索結果が入る。
	           //4番目の要素は{}で指定した文字数を分割して出力する場合に記載する。
	        }
	     }
	}

	$width = $PregMatch[1][0];
	$height = $PregMatch[2][0];

	//サイズ計算
	$x = $width/$width;
	$y = $width/$height;

	//リサイズ
	while($width > 600 || $height > 600){
		//サイズを小さくするループ
		$width = $width - $y;
		$height = $height - $x;
	}

	
	//高さと横の大きさを返したいので、配列に入れて返す
	$size[0] = $width;
	$size[1] = $height;
	
	return $size;
}

//削除機能
function remove($pdo,$table,$num,$pass){
	
	//指定番号が番号チェック
	if(!is_numeric($num)){
		return -4;
	}
	
	//パスワード入力チェック
	$result = check("tmp",$pass);
	if($result == -1){
		//パスが空白
		return -1;
	}
	
	//パスワード判定
	$result = pass_check($pdo,$table,$pass,$num);
	if($result == -2){
		//パスワードが違う
		return -2;
	}
	
	//スレッドの指定行を削除する

	//行を消去するsql
	$sql = "delete from $table where num = $num";
	
	try{
		$result = $pdo -> query($sql);
	}catch(Exception $e){
		//予期せぬエラー
		return -3;
	}
	
	//行数の取得
	$row = rows($pdo,$table);
	
	//投稿番号を振りなおす機能
	//投稿番号を更新するSQLの準備
	$sql = $pdo -> prepare("update $table set num = :new_num where num = :old_num");
	
	$new_num = $num; //新しい投稿番号
	$old_num= $num+1; //元々の投稿番号
	
	while($new_num <= $row){
		//消した行以降の行数分繰り返す
		
		//パラメーターの変数を設定
		$sql -> bindParam(':new_num',$new_num,PDO::PARAM_INT);
		$sql -> bindParam(':old_num',$old_num,PDO::PARAM_INT);
		
		$sql -> execute();
		
		//id更新
		$new_num++;
		$old_num++;
	}
	
	return 1;
}

//パスワードチェック関数
function pass_check($pdo,$table,$pass,$num){
	
	$result = DB_data($pdo,$table,$num);
	
	//検索結果からパスを取り出し
	foreach($result as $row){
		$db_pass =  $row['pass'];
	}
	
	if($db_pass == $pass){
		//パスワードが合っている
		return 1;
	}else{
		//パスワードが間違っている
		return -2;
	}
}

//編集する関数
function edit($pdo,$table,$num,$name,$comment){
	
	//入力チェック
	$result = check($name,$comment);
	if($result == -1){
		//名前かコメントが空白
		return -1;
	}
	
	//ファイルがアップロードされたか確認
	if(is_uploaded_file($_FILES['file']['tmp_name'])){
		
		//画像、動画を保存する関数呼び出し。画像、動画でない場合は空白が帰ってくる。
		$image_pass = get_impass();
		
		//パスが帰ってきたか確認
		if($image_pass != ''){
			//パスがあるので、動画像も編集する
			
			//動画像を編集するsql
			$sql = "update $table set image='$image_pass' where num = $num";
				
			try{
				$results =  $pdo -> query($sql);	
			}catch(Exception $e){
				var_dump($e->getMessage());
			}
		}
	}
	
	//名前、コメントを編集するsql
	$sql = "update $table set name='$name',comment='$comment' where num = $num";
		
	try{
		$results =  $pdo -> query($sql);
	}catch(Exception $e){
		return -3;
	}
	
	return 1;

}

//フォームデータ取得関数
function edit_form($pdo,$table,$num,$column,$pass){
	
	//パスワード入力チェック
	$result = check("tmp",$pass);
	if($result == -1){
		//パスが空白
		return -1;
	}
	
	//指定番号が番号チェック
	if(!is_numeric($num)){
		//数字ではない
		return -4;
	}
	
	//パスワード判定
	$result = pass_check($pdo,$table,$pass,$num);
	if($result == -2){
		//パスワードが違う
		return -2;
	}
	
	//DBからデータ取り出し
	$result = DB_data($pdo,$table,$num);
	
	//指定したカラムのデータを取得
	foreach($result as $row){
		$data = $row["$column"];
	}
	
	return $data;
}

//最終投稿時間を取得する関数
function get_last_time($pdo,$table){
	
	$sql = "SELECT * FROM $table order by post_time DESC";
	
	try{
		$results =  $pdo -> query($sql);	
	}catch(Exception $e){
		var_dump($e->getMessage());
	}
	
	$time = $result['post_time'];
	
	echo "time is ".$time;
	return $time;
	
}

//投稿時に最終投稿を更新する関数
function last_post($pdo,$table,$date){

	$sql = "update thread_board set new_post='$date' where title = '$table'";
			
	try{
		$results =  $pdo -> query($sql);	
	}catch(Exception $e){
		var_dump($e->getMessage());
	}

}

//DB検索（テーブル名、カラム名、値を指定できる）
function db_search($pdo,$table,$column,$value){

	if(is_string ( $value )){
		//値が文字の場合
		$sql = "SELECT * FROM $table where $column = '$value'";
	}else{
		//それ以外
		$sql = "SELECT * FROM $table where $column = $value";
	}
	$result= $pdo -> query($sql);
	
	return $result;
}



//スレッドの削除
function thread_remove($pdo,$table){
	
	//削除者がスレッド作成者か判定(idでチェック)
	$result = check_id($pdo,$table);
	
	if($result == 1){
		//スレッドを削除する
		
		//テーブルを削除
		delete_table($pdo,$table);
		
		//スレッド管理テーブルからも消す
		delete_thread_db($table,$pdo);
		return 1;
		
	}else if($result == -1){
		//idが違う
		return -1;
	
	}else{
		//予期せぬエラー
		return -3;
	}

}

//スレッド編集
function thread_edit($pdo,$table,$name,$comment,$category){
	
	$result = check($name,$comment); //入力チェック（テーブル名前、スレッドの説明）
	
	if($resutlt == -1){
		//コメントか名前が無い
		return -1;
	}
	
	$result = check($category,"a"); //入力チェック（カテゴリ）
	
	if($resutlt == -1){
		//カテゴリが選択されてない
		return -2;
	}
	
	check_id($pdo,$table); //idチェック
	
	change_table($pdo,$table,$name); //テーブル名の変更
	thread_db_change_table($pdo,$table,$name,$comment,$category); //スレッド管理dbのテーブルのデータ変更
	
	return 1;
}


//テーブル名の変更
function change_table($pdo,$table,$name){
	$sql = "ALTER TABLE $table RENAME TO $name";
	$result = $pdo -> query($sql);
}

//スレッド管理dbのテーブル名変更
function thread_db_change_table($pdo,$table,$name,$agenda,$category){
	try{
		$sql = "update thread_board set title='$name', agenda='$agenda', category='$category' where title = '$table'";
	}catch(Exception $e){
		var_dump($e->getMessage());
	}
	$result= $pdo -> query($sql);
}


//テーブルを削除
function delete_table($pdo,$table){
	try{
		$sql = "DROP TABLE $table;";
		$stmt = $pdo->query($sql);

	}catch(Exception $e){
		var_dump($e->getMessage());
	}	
}

//スレッド管理テーブルからスレッドデータを消す
function delete_thread_db($table,$pdo){
	try{
		$sql = "delete from thread_board where title='$table';";
		$stmt = $pdo->query($sql);

	}catch(Exception $e){
		var_dump($e->getMessage());
	}	

}

//idチェック
function check_id($pdo,$table){
	$id = $_SESSION['acc_id']; //ログイン者のid
	
	$result = get_thread($table,$pdo); //スレッドのデータ取得
	
	//読み出し失敗
	if($result == -3){
		return -3;
	}

	
	foreach($result as $row){
		//id読み出し
		$db_id = $row['id'];
	}
	
	//idが同じか判定
	if($id == $db_id){
		return 1;
	}
	
	//idが違う
	return -1;
		
}

?>