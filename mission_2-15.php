<html> 
<meta charset="utf-8"> 
<lang="ja"> 

<head>
	<title>ミミミブログ</title>
	<meta http-equiv="content-type" charset="utf-8">
</head>

<body> 

　<!-タイトルをつける->
　<h3>ミミミブログ</h3></br>

<?php

//データベースの接続
$dsn = 'mysql:dbname=co_924_it_99sv_coco_com;host=localhost';
$user = 'co-924.it.99sv-coco.com';
$password = 'Jbui7hY';
$pdo = new PDO($dsn,$user,$password);
//文字化け対策
$stmt=$pdo->query('SET NAMES utf8');
header('Content-Type: text/html; charset=UTF-8');

//----------------------------------------------変数の群
//fetchColumnで結果セットから番号の数字を抜き出す
$id = $pdo -> query("SELECT max(id)+1 from mission_2_15;")->fetchColumn(0); 
$name=$_GET['nickname'];
$comment=$_GET['comment'];
$time=date("Y/m/d H:i:s");
$pass0=$_GET['pass0'];
$ehidden2=$_GET['ehidden2'];

$delete=$_POST['delete'];
$dhidden=$_POST['dhidden'];
$dpass=$_POST['dpass'];
$edit=$_POST['edit'];
$epass=$_POST['epass'];
$ehidden1=$_POST['ehidden1'];

//------------------------------------結果の表示をわかりやすくする
$left="＊＊＊＊【";
$right="】＊＊＊＊";
$enterpass="<font color='blue'>$left-パスワードを入力してください-$right</font>";
$wrongpass="<font color='red'>$left-パスワードが間違っています-$right</font>";

//----------------------------------------------------全フォームが空白の場合

if(empty($name) && empty($comment) && empty($delete) && empty($edit) && empty($dhidden) && empty($ehidden1) && empty($ehidden2))
{ 
	//歓迎語
	echo "$left-ミミミブログへようこそ！-$right";

//---------------------------------------------------名前とコメントのどちらかが空白の場合

}elseif((empty($name) && !empty($comment)) || (!empty($name) && empty($comment)))
{
	echo "<font color='blue'>$left-お名前とコメントを同時に入力してください-$right</font>";

//-----------------------------------------------------以下は機能コード
}else
{

//--------------------------------------------------------1、削除機能

if(!empty($dhidden) && !empty($dpass))
	{
	//対象番号の持っている行を探し出す
	$result=$pdo -> query("SELECT * from mission_2_15 where id=$dhidden;") -> fetch();
	
	//パスが正確な場合
	if($result['id']==$dhidden && $result['pass_word']==$dpass)
		{
		//パスワードがあったら、「削除された」に書き換え
		$sql="update mission_2_15 set name='', comment='＊＊このコメントが削除されました＊＊', time='', pass_word='' where id=$dhidden;";
		$pdo->query($sql);
		echo "$left-".$dhidden."番を削除しました-$right";
		}
		
	//対象行のパスが間違えてる
	elseif($result['id']==$dhidden)
		{
		echo $wrongpass; //パス間違いの警告
		echo "<br>（或いは、このコメントはすでに削除されました。）"; //対象番号は既に削除された
		$delete=$dhidden;//パス間違っても番号が入力済みにキープする
		}
	}
	
//パスが空白の場合
elseif(!empty($dhidden) && empty($dpass))
	{
	echo $enterpass; //パス入力の警告
	$delete=$dhidden;//パス間違っても番号が入力済みにキープする
	}
	
//番号を入力されてからパスを要求する
elseif(!empty($delete) && empty($dpass))
	{
	echo $enterpass; 
	}

//---------------------------------------------------------------2、編集モードに入る

if(!empty($ehidden1) && !empty($epass))
	{	
		//対象番号の持っている行を探し出す
		$result=$pdo -> query("SELECT * from mission_2_15 where id=$ehidden1;") -> fetch();
		
		//パスが正確な場合
		if($result['id']==$ehidden1 && $result['pass_word']==$epass)
			{
			//編集対象の名前とコメントを取得して、新しい変数に代入して、下のHTMLで表示させる
			$enam=$result['name'];
			$ecom=$result['comment'];
			echo "$left-編集モードです-$right";
			}
			
		//パスが間違ってる場合
		elseif($result['id']==$ehidden1)
			{
			echo $wrongpass;//間違い警告
			$edit=$ehidden1; //パス間違っても番号が入力済み状態に維持できる
			}
	}
	
//パスが空白の場合
elseif(!empty($ehidden1) && empty($epass))
	{
	echo $enterpass;
	$edit=$ehidden1;//番号入力済みで表示させる
	}
	
//番号を入力されてパスを要求する
elseif(!empty($edit) && empty($dpass))
	{echo $enterpass;}

//---------------------------------------------------------------3、編集の書き換え機能

if(!empty($name) && !empty($comment) && !empty($pass0) && empty($edit) && empty($ehidden1) && !empty($ehidden2) && empty($delete) && empty($dhidden))
	{
	$sql="update mission_2_15 set name='$name', comment='$comment', time='$time', pass_word='$pass0' where id='$ehidden2';";
	$pdo->query($sql);
	echo "$left-".$ehidden2."番の編集を完成しました-$right";
	}

//編集モードで、パスが空白で送信されたの場合
elseif(!empty($name) && !empty($comment) && empty($pass0) && empty($ehidden1) && !empty($ehidden2))
	{
	//パス入力の警告
	echo $enterpass;
	
	//パスが空白でも名前とコメントを入力済みの状態にキープする
	$ehidden1=$ehidden2;
	$result=$pdo -> query("SELECT * from mission_2_15 where id=$ehidden1;") -> fetch();
		if($result['id']==$ehidden1)
			{
			$enam=$result['name'];
			$ecom=$result['comment'];
			}
		
	}
	
//-----------------------------------------------------------------4、コメントを書き込む機能

if(!empty($name) && !empty($comment) && !empty($pass0) && empty($delete) && empty($dhidden) && empty($ehidden1) && empty($ehidden2) && empty($edit))
	{
	//PHPで使われる変数をテーブルのコラム名に関連する
	$sql = $pdo -> prepare("INSERT INTO mission_2_15 (id,name,comment,time,pass_word) VALUES(:id,:name,:comment,:time,:pass_word);");
	$sql -> bindParam(':id',$id,PDO::PARAM_INT);
	$sql -> bindParam(':name',$name,PDO::PARAM_STR);
	$sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
	$sql -> bindParam(':time',$time,PDO::PARAM_STR);
	$sql -> bindParam(':pass_word',$pass0,PDO::PARAM_STR);
	$sql->execute();
	
	echo "$left-コメントを書き込みました-$right";
	}
	
//パスが空白の場合
elseif(!empty($name) && !empty($comment) && empty($delete) && empty($dhidden) && empty($ehidden1) && empty($ehidden2) && empty($edit))
{echo $enterpass;}

 }
//-----------------------------------------------------------------------以下はhtml部分
?>

<!-名前フォーム->
<form action="" method="get">
	</br></br>1. ニックネーム：<input type="text" name="nickname" value = "<?php echo $enam;?>"/></br>
	</br>2. コメント：<textarea name="comment" cols ="36" rows = "5" ><?php echo $ecom;?></textarea></br>
	</br>3. パスワード：<input type="text" name="pass0" /></br></br>
	
	<!-if文によって編集モードかどうかを判断する。編集モードだったらhidden2によって対象番号を転送->
	<?php if (!empty($ehidden1)) : ?>　
	<?php echo "<input type='hidden' name='ehidden2' value='$ehidden1'/>"; ?>
	<?php endif; ?>
	
	<input class="button" type="reset" value="書き直す" />
	<input class="button" type="submit" value="発信" /></br>
</form>

<!-削除フォーム->
<form action="" onsubmit="return confirm('本当に削除しますか？')" method="post">
	<p>-----------------------------------------------------------------------------</p>
	<!-if文によってパスのフォームを隠す->
	<?php if (empty($delete)) : ?>
	<?php echo "削除対象番号：<input type='number' name='delete' />"; ?>
	
	<?php else: ?>
	<?php echo "削除対象番号：".$delete;  ?>
	</br>パスワード：<input type="text" name="dpass" size="20" />
	<?php echo "<input type='hidden' name='dhidden' value='$delete' />"; ?>
	<?php endif; ?>
	
	<input class="button" type="submit" value="削除" /></br>
</form>

<!-編集フォーム->
<form action="" method="post">
	<p>-----------------------------------------------------------------------------</p>
	<!-if文によってパスのフォームを隠す->
	<?php if (empty($edit)) : ?>
	<?php echo "編集対象番号：<input type='number' name='edit' />"; ?>
	
	<?php else: ?>
	<?php echo "編集対象番号：".$edit; ?>
	<?php echo "</br>パスワード：<input type='text' name='epass' />"; ?>
	<!-2回目で編集ボタンを押す時に、対象番号をhidden1によって転送->
	<?php echo "<input type='hidden' name='ehidden1' value='$edit'/>"; ?>
	<?php endif; ?>
	
	<input class="button" type="submit" value="編集" /></br></br>
	<hr>
　</form>

<?php
//--------------------------------------------------------------------------------------以下は表示機能

$lines = $pdo->query("SELECT * FROM mission_2_15;");
foreach ($lines as $line) 
	{
	echo "#<b>{$line['id']}</b> : 　　＜".$line['name']."＞さん　　　投稿時間：".$line['time']."<br />";
	echo "<br>".$line['comment']."<br><br>";
	echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br />";
	}

?>

</body> 
</html>