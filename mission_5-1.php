<!DOCTYPE html>
<html lang="ja">
	<head>
		<title>mission_5-1.php</title>
		<meta charset="utf-8">
	</head>
	
	
	<?php
	//mission4-1(データベースへの接続)
	$dsn='データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
	//mission4-2(テーブルを作る)
	$sql = "CREATE TABLE IF NOT EXISTS test4"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"  //これがカラム
	. "name char(32),"
	. "comment TEXT,"
	. "pass char(32),"
	. "date char(32)"
	.");";
	$stmt = $pdo->query($sql); 
	
	//入力フォームからPOST送信しphpで受け取る
		if(!empty($_POST["name"])&&!empty($_POST["comment"])&&empty($_POST["put"])&&!empty($_POST["pass"])){
			$name=$_POST["name"]; 
			$comment=$_POST["comment"];  
			$pass=$_POST["pass"];
			$date=date("Y/m/d H:i:s");	

			//mission4-5(作成したテーブルに、insertを行ってデータを入力する)
			$sql = $pdo -> prepare("INSERT INTO test4 (name, comment,pass,date) VALUES (:name,:comment,:pass,:date)");
			$sql -> bindParam(':name', $name, PDO::PARAM_STR);
			$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
			$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
			$sql -> bindParam(':date', $date, PDO::PARAM_STR);
			$sql->execute();
		}
	?>
	
		<?php		//削除機能作成
		if(!empty($_POST["delete"])&&!empty($_POST["delpass"])){
			$id=$_POST["delete"];
			$delpass=$_POST["delpass"];
			$pass=$_POST["pass"];
			
			//削除したい番号のパスワードをテーブルから取得する。
			$sql = "SELECT pass FROM test4 WHERE id = '$id' ";
			//投稿番号のパスワードを検索
			$stmt = $pdo->query($sql);
			$results = $stmt->fetchAll();
				foreach($results as $row){ 
					//$rowの中にはテーブルのカラム名が入る
					$pass = $row['pass'];
				}
		if($delpass==$pass){
			//mission4-8(入力したデータをdeleteによって削除する)
			$sql = 'delete from test4 where id=:id';  
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
		}else{
			echo "⚠️パスワードが間違っています！⚠️";
		}
		}
	?>
	
	<?php		//編集フォーム作成part1
		if(!empty($_POST["edit"])){
			$edit=$_POST["edit"];
			$id=$edit;
			$editpass=$_POST["editpass"];
			//編集したい番号のパスワードをテーブルから取得する。
			$sql = "SELECT * FROM test4 WHERE id = '$id' ";
			//投稿番号のパスワードを検索
			$stmt = $pdo->query($sql);
			$results = $stmt->fetchAll();
				foreach($results as $row){ 
					//$rowの中にはテーブルのカラム名が入る
					$name = $row['name'];
					$comment = $row['comment'];
					$pass = $row['pass'];
					$id = $row['id'];
					
						//パスワードが一致する場合は編集したい項目の名前、コメントを表示
						if($editpass==$pass){
						$newname=$name;
						$newcomment=$comment;
						}
						//パスワードが一致しない場合
						elseif($editpass!==$pass){
						echo "⚠️パスワードが間違っています！⚠️";
						}
				}
		}
	?>
	<?php		//編集フォーム作成part2
		//投稿フォームに編集した内容が送信された時
		if(!empty($_POST["put"])){
		//編集番号を追加する$idの指定
		$id = $_POST["put"]; //変更する投稿番号
		$name=$_POST["name"]; 
		$comment=$_POST["comment"];
		$pass=$_POST["pass"];
		$date=date("Y/m/d H:i:s");
		$sql = 'update test4 set name=:name,comment=:comment,date=:date,pass=:pass where id=:id';
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
		$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		}
	?>
	
	
		<body>
	<!- この記号の間に入れるとブラウザで表示されない->
	<form method="POST" action="mission_5-1.php">
	<font color="orange"><h1>掲示板</h1></font><br>
	
	<font color="blue"><h3>【投稿フォーム】</h3></font><br>
	名前:&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;<input type="text"  name="name" value= <?php if(!empty($newname)){echo "$newname" ;} ?> ><br>
	コメント:&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;<input type="text"  name="comment" value= <?php if(!empty($newcomment)){echo "$newcomment" ;} ?> ><br>
	パスワード設定:&ensp;<input type="text" name="pass" >
	<!-編集したい投稿の番号をこのテキストの中に表示する-><!-(3-4-7)->
	<input type="hidden" name="put" value= <?php  if(!empty($edit)){echo "$edit";}?> ><br>
   	<input type="submit" value="送信"><br><br>
   	
   <font color="blue">	<h3>【削除フォーム】</h3></font><br>
	削除番号:&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;<input type="number"  name="delete"><br>
	パスワード入力:<input type="text" name="delpass" ><br>
	<input type="submit" value="削除"><br><br>
	
	<font color="blue"><h3>【編集フォーム】</h3></font><br>
	編集番号:&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;<input type="number"  name="edit"><br>
	パスワード入力:<input type="text" name="editpass" ><br>
	<input type="submit" value="編集"><br>
	
	<font color="#8a2be2"><h3>【投稿一覧】</h3></font><br>
	
	<?php		
	
		//mission4-6(入力したデータをselectによって表示する)
			$sql = 'SELECT * FROM test4';
			$stmt = $pdo->query($sql);
			$results = $stmt->fetchAll();
				foreach($results as $row){ 
					//$rowの中にはテーブルのカラム名が入る
					echo $row['id'].',';
					echo $row['name'].',';
					echo $row['comment'].',';
					echo $row['date'].'<br>';
					echo "<hr>";
	}
					
 	 ?> 	

	</form>
	</body>
</html>