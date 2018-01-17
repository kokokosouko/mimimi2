<html> 
<meta charset="utf-8"> 
<lang="ja"> 

<head>
	<title>ミミミブログへのログイン</title>
	<meta http-equiv="content-type" charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style1.css" media="screen" />
</head>

<body> 

　<!-タイトルをつける->
　<div id="head"><h1>ミミミブログ・ログイン</h1></br></div>

<?php

//sessionに入る
session_start();

//データベースの接続
$dsn = 'mysql:dbname=co_924_it_99sv_coco_com;host=localhost';
$user = 'co-924.it.99sv-coco.com';
$password = 'Jbui7hY';
$pdo = new PDO($dsn,$user,$password);
//文字化け対策
$stmt=$pdo->query('SET NAMES utf8');
header('Content-Type: text/html; charset=UTF-8');

//-------------------------------------------------------------------変数の定義
//認証メールからトークンを取得
$token=stripslashes(trim($_GET['token']));
//認証リンクをクリックする時間
$nowtime=date("YmdHis");

$user_name = $_GET['user_name'];
$password=$_GET['password'];
$logout=$_GET['logout'];

$left="＊＊＊＊【";
$right="】＊＊＊＊";

//---------------------------------------------------------------メール認証から来た場合
if(!empty($token))
	{
	//トークンがあってるかどうかを確認
	$result=$pdo -> query("SELECT * from user where regflag='0' and token='$token';") -> fetch();
	
	//トークン正確
	if(!empty($result))
		{
		//24時間過ぎたので、仮登録のアカウントを削除する
		if($result['regtime']+1000000 < $nowtime)
			{
			echo "認証メールをお送りしてから<font color='red'>24時間</font>が過ぎましたので、お手数ですが、もう一度ご登録ください。";
			$sql="delete from user where where regflag='0' and token='$token';";
			$result=$pdo->exec($sql);
			}
		
		//メール届いてから24時間以内の場合、本登録を行う
		else
			{
			echo "$left-本登録が成功しました。ログインしてから閲覧可能になります。-$right";
			//登録フラグを本登録（1）に書き換え
			$sql=$pdo->prepare("update user set regflag='1' where token=:token;");
			$sql -> bindParam(':token',$token,PDO::PARAM_STR);
			$sql->execute();
			}
		}
		
	//トークン間違えた
	else
		{echo "<font color='blue'>$left-このアカウントの本登録はすでに完成しました。-$right</font>";}
	}



//メール認証からでない場合
if(empty($token))
{
	//ログアウトボタンから来た場合、セッションを消す
	if(!empty($logout))
		{session_destroy();}
	 
	//-------------------------------------------------------------------歓迎語
	if(empty($user_name) && empty($password))
		{echo "$left-ログインしてから閲覧可能になります。-$right";}

	//--------------------------------------------------------------名前かパスが空の場合

	elseif(empty($user_name))  
	{echo "<font color='blue'>$left-ユーザー名を入力してください-$right</font>";}
	
	elseif(empty($password))
	{echo "<font color='blue'>$left-パスワードを入力してください-$right</font>";}

	//-------------------------------------------------------------------本番のログイン機能
	
	elseif(!empty($user_name) && !empty($password) )
		{
		//名前の対応するパスワードを探し出す
		$result=$pdo -> query("SELECT * from user where user_name='$user_name';") -> fetch();
	
		//名前とパスが正確＋本登録が完成したら
		if($result['user_name']==$user_name && $result['password']==$password && $result['regflag']==1)
			{
			//ユーザーの名前とパスをsessionに納格する
			//ユーザーが15分間以内に何かを動作しないとこのセッションが自動的になくなる
			$_SESSION['user_name']=$result['user_name'];
			$_SESSION['password']=$result['password'];
		
			//掲示板の方に飛ばしていく
			header("Location:mission_3-8HP.php");
			}
			
		//パスか名前が間違ってる場合、警告出す
		elseif($result['user_name']!==$user_name)
			{echo "<font color='red'>$left-このユーザー名が登録されていません-$right</font>";}
			
		//IDが間違えた、またはユーザーが登録していない場合、警告出す
		elseif($result['password']!==$password)
			{echo "<font color='red'>$left-パスワードが間違っています-$right</font>";}
			
		//登録フラグをチェック、まだ仮登録なら、警告出す
		elseif($result['regflag']==0)
			{echo "<font color='red'>$left-メール認証が行われていません-$right</font>";}
		
		}
}

//------------------------------------------------------以下はhtmlのコード
?>


<!-登録フォーム->
<form action="" method="get">
	</br></br>1. ユーザー名：<input type="text" name="user_name" /></br>
	</br>2. パスワード：<input type="password" name="password" /></br></br>
	
	<a href="mission_3-9touroku.php">まだ登録されていない方はこちらへ</a>　　
	<input class="button" type="submit" name="submit" value="ログイン" /></br>
<hr>
</form>

</body> 
</html>