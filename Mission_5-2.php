<?php
   	/* DB接続設定*/
	$dsn = '******';
	//データベースに接続するために必要な情報
	$user = '******';
	//ユーザーの定義づけ
	$password = '******';
	//パスワードの定義づけ
	$pdo = new PDO($dsn, $user, $password, 
	       array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //new演算子、アラートが表示されるようにarray関数で設定

    /*4-2テーブル作成*/
    $sql = "CREATE TABLE IF NOT EXISTS tb1"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "pass char(32),"
	. "date char(32)"
	.");";
	$stmt = $pdo->query($sql);
	
$name = ($_POST["name"]);//お名前を受信
$comment = ($_POST["comment"]);//コメントを受信
$date = date("Y年m月d日H時i分s秒");//日付獲得
$pass = ($_POST["pass"]);//パスワードを受信

/*編集、新規投稿機能*/
if($_POST["submit"]){ //!もし新規投稿の「送信ボタンが押され」たら
$HDnum = ($_POST["number"]);//投稿番号を受信
    if($HDnum =="" && !empty($name && $comment && $pass)){
    //4-5データレコードの挿入
    $sql = $pdo -> prepare("INSERT INTO tb1 (name, comment, pass, date) 
    VALUES (:name, :comment, :pass, :date)");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
	$sql -> bindParam(':date', $date, PDO::PARAM_STR);
	//好きな名前、好きな言葉は自分で決めること
	$sql -> execute();
    }
        if(!empty($HDnum && $name && $comment && $pass)){
        $id = $HDnum;
	    $sql = 'SELECT * FROM tb1';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
	        foreach ($results as $row){
	            if($id == $HDnum && $pass == $row['pass']){
	                $sql = 'UPDATE tb1 SET 
	                name=:name,comment=:comment,pass=:pass,date=:date WHERE id=:id';
	                $stmt = $pdo->prepare($sql);
	                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
	                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
	                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	                $stmt->execute();
	            }
	        }
        }
}//もし新規投稿の「送信ボタンが押され」たら!

/*削除機能*/
if($_POST["delete"]){
$delete_num = ($_POST["delete_num"]);//削除番号受信
$delete_pass = ($_POST["delete_pass"]);//削除パス受信
    if(!empty($delete_num)){
    $id = $delete_num;
	$sql = 'SELECT * FROM tb1';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
	    foreach ($results as $row){
            if($id == $delete_num && $delete_pass == $row['pass']){
            //4-8データの削除
	        $sql = 'delete from tb1 where id=:id';
	        $stmt = $pdo->prepare($sql);
	        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    	    $stmt->execute();
            }
	    }
    }
}

/*編集機能*/
/*編集番号が送信されると一致した投稿番号が入力フォームに表示されるように*/
if($_POST["edit"]){
$edit_pass = ($_POST["edit_pass"]);
$edit_num = ($_POST["edit_num"]);//編集対象番号を受信
    if(!empty($edit_num)){//■■■もし編集番号が空じゃなければ■■■
    $id = $edit_num;
	$sql = 'SELECT * FROM tb1';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
	    foreach ($results as $row){
	        if($id == $edit_num && $edit_pass == $row['pass']){
	        $editName = $row['name'];
	        $editComment = $row['comment'];
	        }
	    }
    }
}
?>


<!DOCTYPE html>

<html lang="ja">

<head>

    <meta charset="UTF-8">

    <title>Mission_5-1</title>

</head>

    <body>
    <form action = "#" method = "post">
<!--新規投稿フォーム-->
    <p>お名前：<br>
        <input type = "text" name = "name" value = <?php echo $editName?>></p>
        <!--名前書くところ-->
        <!--3-4編集対象番号が一致すると$editNameを表示する-->
    <p>コメント：<br>
        <input type = "text" name = "comment" style = "width:200px;"
        value = <?php echo $editComment?>></p>
        <!--コメント書くところ-->
        <!--3-4編集対象番号が一致すると$editCommentを表示する-->
    <input type = "hidden" name = "number" value = <?php echo $edit_num?>>
    <!--投稿番号が表示されるところ-->
        <!--3-4編集対象番号が一致すると$edit_numを表示する-->
    <p>password：<br>
        <input type = "text" name = "pass"></p>
    <input type = "submit" name = "submit" value = "送信"><!--送信ボタン-->

<!--削除フォーム-->
    <p>削除対象番号：<br>
        <input type = "text" name = "delete_num" ></p><!--削除番号書くところ-->
    <p>password：<br>
        <input type = "text" name = "delete_pass"></p>
    <input type = "submit" name = "delete" value = "削除"><!--削除ボタン-->

<!--編集番号フォーム-->
    <p>編集対象番号：<br>
        <input type = "text" name = "edit_num" ></p><!--編集番号書くところ-->
    <p>password：<br>
        <input type = "text" name = "edit_pass"></p>
    <input type = "submit" name = "edit" value = "編集"><!--編集ボタン-->
    </form>


<?php
//4-6入力したデータレコードを抽出し、表示する
    //$rowの添字（[ ]内）は、4-2で作成したカラムの名称に併せる必要があります。
	$sql = 'SELECT * FROM tb1';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'].'<br>';
	echo "<hr>";
	}
?>
    </body>
</html>