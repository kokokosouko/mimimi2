<html> 
<meta charset="utf-8"> 
<lang="ja"> 

<head>
	<title>ミミミブログ</title>
	<meta http-equiv="content-type" charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style1.css" media="screen" />
</head>

<body> 

　<!-タイトルをつける->
<div id="head">
	<h1>ミミミブログ</h1></br>
</div>

<?php
//sessionモードに入る
session_start();
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
$user_name=$_SESSION['user_name'];
$comment=$_POST['comment'];
$time=date("Y/m/d H:i:s");
$pass0=$_SESSION['password'];
$ehidden2=$_POST['ehidden2'];
$binarydata = file_get_contents($_FILES['userfile']['tmp_name']);
$type=$_FILES['userfile']['type'];

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

//-------------------------------------sessionが設置してない場合、ログインしていないと見なし、ログインページに飛んでいく

if(!isset($_SESSION['user_name']) && !isset($_SESSION['password'])){

	echo "<script>alert('まずはログインしてください。');history.go(-1);</script>";
	header("Location:mission_3-9login.php");

//----------------------------------------------------全フォームが空白の場合に歓迎語を出す

}elseif(empty($comment) && empty($delete) && empty($edit) && empty($dhidden) && empty($ehidden1) && empty($ehidden2))
{ 
	//歓迎語
	echo "$left-".$user_name."さん、ミミミブログへようこそ！お気軽に何かをお書きください〜-$right";

//-----------------------------------------------------以下は機能コード
}else
{

//-------------------------------------------------------------------1、削除機能

if(!empty($dhidden) && !empty($dpass))
	{
	//対象番号の持っている行を探し出す
	$result=$pdo -> query("SELECT * from keijiban where comment_id=$dhidden;") -> fetch();
	
	//パスが正確な場合
	if($result['comment_id']==$dhidden && $result['pass_word']==$dpass)
		{
		//パスワードが正確だったら、「削除された」に書き換え、コメントTBとメディアTBを空にする
		$sql=$pdo->exec("delete from keijiban where comment_id=$dhidden;");
		echo "$left-".$dhidden."番は削除されました-$right";
		}
		
	//対象行のパスが間違えてる
	elseif($result['comment_id']==$dhidden)
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

//---------------------------------------------------------------------2、編集モードに入る

if(!empty($ehidden1) && !empty($epass))
	{	
		//対象番号の持っている行を探し出す
		$result=$pdo -> query("SELECT * from keijiban where comment_id=$ehidden1;") -> fetch();
		
		//パスが正確な場合
		if($result['comment_id']==$ehidden1 && $result['pass_word']==$epass)
			{
			//編集対象の名前とコメントを取得して、新しい変数に代入して、下のHTMLで表示させる
			$enam=$result['user_name'];
			$ecom=$result['comment'];
			echo "$left-編集モードです-$right";
			}
			
		//パスが間違ってる場合
		elseif($result['comment_id']==$ehidden1)
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


//名前フォームに書き込んだ場合

if(!empty($comment) && empty($delete) && empty($dhidden) && empty($edit) && empty($ehidden1))
{
//--------------------------------------------------------------------------3、ファイル処理
	//ファイルのエラー報告（0、2タイプのみ）
	$error=$_FILES['userfile']['error'];
	if($error==0 || $error==2)
		{
		//拡張子の制限範囲を定義する
		$allow_type = array('image/jpeg','image/gif','image/png','video/mp4'); 

		//拡張子判断をつける。制限外だったら、発信中止
		if(!in_array($type, $allow_type))
		{
			echo "<script>alert('ご対応できないファイルタイプです');history.go(-1);</script>";
		}
		
		//アップロード成功
		if($error==0)
			{echo "</br>$left-ファイルはアップロードされました-$right</br>";}

		//サイズが制限を超える
		elseif($error==2)
			{echo "<script>alert('ファイルサイズが大き過ぎます');history.go(-1);</script>";} 
		}
	
//--------------------------------------------------------------------4、編集の書き換え機能
	
	if(!empty($ehidden2))
		{
		//コメントとメディアを書き直す
		$sql=$pdo->prepare("update keijiban set user_name=:user_name, comment=:comment, time=:time, pass_word=:pass_word, binarydata=:binarydata, type=:type where comment_id=:comment_id;");
		$sql -> bindParam(':comment_id',$ehidden2,PDO::PARAM_INT);
		$sql -> bindParam(':user_name',$user_name,PDO::PARAM_STR);
		$sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
		$sql -> bindParam(':time',$time,PDO::PARAM_STR);
		$sql -> bindParam(':pass_word',$pass0,PDO::PARAM_STR);
		$sql -> bindParam(':binarydata',$binarydata,PDO::PARAM_STR);
		$sql -> bindParam(':type',$type,PDO::PARAM_STR);
		//実行
		$sql->execute();
		echo "$left-".$ehidden2."番の編集は完成しました-$right";
		}
	
//-------------------------------------------------------------------5、コメントを書き込む機能

	else{
		//テーブルに記入
		$sql = $pdo -> prepare("INSERT INTO keijiban (comment_id,user_name,comment,time,pass_word,binarydata,type) VALUES(:comment_id,:user_name,:comment,:time,:pass_word,:binarydata,:type);");
		$sql -> bindParam(':comment_id',$comment_id,PDO::PARAM_INT);
		$sql -> bindParam(':user_name',$user_name,PDO::PARAM_STR);
		$sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
		$sql -> bindParam(':time',$time,PDO::PARAM_STR);
		$sql -> bindParam(':pass_word',$pass0,PDO::PARAM_STR);
		$sql -> bindParam(':binarydata',$binarydata,PDO::PARAM_STR);
		$sql -> bindParam(':type',$type,PDO::PARAM_STR);
		//実行
		$sql->execute();
		echo "$left-コメントを書き込みました-$right";
		}
}
}
 
//-----------------------------------------------------------------------以下はhtml部分
?>
	
<div id="name">
	<form action="" method="post" enctype="multipart/form-data" >
		</br></br>1. ユーザー名：　<b><?php echo $user_name;?></b>　
		<a href="mission_3-9login.php?logout=1">｜ログアウト｜</a></br>
		</br>2. コメント：<textarea name="comment" cols ="34" rows = "5" ><?php echo $ecom;?></textarea></br>

		</br>3. 画像・動画：<input type="file" name="userfile" />
	
		<!-if文によって編集モードかどうかを判断する。編集モードだったらhidden2によって対象番号を転送->
		<?php if (!empty($ehidden1)) : ?>　
		<?php echo "<input type='hidden' name='ehidden2' value='$ehidden1'/>"; ?>
		<?php endif; ?>
	
		<input class="button" id="toukou" type="submit" value="投稿" /></br>
	</form>
</div>
	
<div id="deledit">
	<!-削除フォーム->
	<form action="" onsubmit="return confirm('本当に削除しますか？')" method="post">
		<!-if文によってパスのフォームを隠す->
		<?php if (empty($delete)) : ?>
		<?php echo "＊削除＊対象番号：<input type='number' name='delete' />"; ?>
	
		<?php else: ?>
		<?php echo "＊削除＊対象番号：".$delete;  ?>
		</br>パスワード：<input type="text" name="dpass" size="20" />
		<?php echo "<input type='hidden' name='dhidden' value='$delete' />"; ?>
		<?php endif; ?>
	
		<input class="button" type="submit" value="削除" /></br>
	</form>
	
	<!-編集フォーム->
	<form action="" method="post">
		<!-if文によってパスのフォームを隠す->
		<?php if (empty($edit)) : ?>
		<?php echo "＊編集＊対象番号：<input type='number' name='edit' />"; ?>
	
		<?php else: ?>
		<?php echo "＊編集＊対象番号：".$edit; ?>
		<?php echo "</br>パスワード：<input type='text' name='epass' />"; ?>
		<!-2回目で編集ボタンを押す時に、対象番号をhidden1によって転送->
		<?php echo "<input type='hidden' name='ehidden1' value='$edit'/>"; ?>
		<?php endif; ?>
	
		<input class="button" type="submit" value="編集" /></br></br>
	</form>
</div>	
	
<div id="content">
	<?php
	//--------------------------------------------------------------------------------------以下は表示機能

	$lines = $pdo->query("SELECT * FROM keijiban order by comment_id;");
	foreach ($lines as $line) 
		{
		$i=$line['comment_id'];
	
		//コメントの表示
		echo "<h3>#{$i}　　＜".$line['user_name']."＞さん　　　　　　　　　　　　　　　　　　　　　　　投稿時間：".$line['time']."</h3><br />";
		echo $line['comment']."<br><br>";
	
		//画像だったら、表示する
		if(!empty($line['type']) && $line['type']!='video/mp4')
			{echo "<img src='show_media.php?no=$i'><br><br><br>";}
	
		//動画の表示
		elseif($line['type']=='video/mp4')
			{echo "<video src=\"show_media.php?no=$i\" width=\"426\" height=\"240\" controls></video><br><br>";}
		else
			{echo "<br>";}
		}
	?>
</div>
		
</body> 
</html>