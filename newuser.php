<?php

//共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　新規登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


//------------------------------------------------------------------------------------------------関数

//post送信されていた場合
if(!empty($_POST)){
    
    //変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];
    
    //未入力チェック
    validRequired($email,'email');
    validRequired($pass,'pass');
    validRequired($pass_re,'pass_re');
    
    if(empty($err_msg)){
        
        //emailの形式チェック
        validEmail($email,'email');
        //emailの最大文字数チェック
        validMaxLen($email,'email');
        //email重複チェック
        validEmailDup($email);
        
        //パスワードの半角英数字チェック
        validHalf($pass,'pass');
        //パスワードの最大文字数チェック
        validMaxLen($pass,'pass');
        //パスワードの最小文字数チェック
        validMinLen($pass,'pass');
        
        //パスワード（再入力）の最大文字数チェック
        validMaxLen($pass_re,'pass_re');
        //パスワード（再入力）の最小文字数チェック
        validMinLen($pass_re,'pass_re');
        
        
        if(empty($err_msg)){
            
            //パスワードとパスワード再入力が合っているかをチェック
            validMatch($pass, $pass_re, 'pass_re');
            
            if(empty($err_msg)){
            
            //例外処理
            try{
                //DBへ接続
                $dbh = dbConnect();
                //SQL文作成
                $sql = 'INSERT INTO users (email,password,login_time,create_date) VALUES(:email,:pass,:login_time,:create_date)';
                $data = array(':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT),
                             ':login_time' => date('Y-m-d H:i:s'),
                             ':create_date' => date('Y-m-d H:i:s'));
                //クエリ実行
                queryPost($dbh,$sql,$data);
                
                //クエリ成功の場合
                if($stmt){
                    //ログイン有効期限（デフォルトを1時間とする）
                    $sesLimit = 60*60;
                    //最終ログイン日時を現在日時に
                    $_SESSION['login_date'] = time();
                    $_SESSION['login_limit'] = $sesLimit;
                    //ユーザーIDを格納
                    $_SESSION['user_id'] = $dbh->lastInsertId();
                    
                    debug('セッション変数の中身：'.print_r($_SESSION,true));
                    header("Location:mypage.php");//マイページへ
                }else{
                    error_log('クエリに失敗しました。');
                    $err_msg['common'] = MSG07;
                }
                
            } catch (Exception $e){
                error_log('エラー発生:' . $e->getMessage());
                $err_msg['common'] = MSG07;
            }
      }
    }
  }
}

?>
<!DOCTYPE html>
<html lang="ja">
   <head>
       <meta charset="utf-8">
       <title>webサービス作成1</title>
       <link rel="stylesheet" href="css/common.css">
       <link rel="stylesheet" href="css/newuser.css">
   </head>
   <body>
       <header>
           <h1><a href="index.php">D.vidulum</a></h1>
           
          <!-- ナビゲーション -->
           <nav>
             <ul>
               <li><a href="index.php">ホーム</a></li>
               <li><a href="login.php">ログイン</a></li>
               <li><a href="newuser.php">新規登録</a></li>
             </ul>
           </nav>
       </header>
       <main>
         
          <div class="form-container">
           <form action="" method="post" class="form">
              <h2>ユーザー登録</h2>
              <div class="area-msg">
                 <?php
                  if(!empty($err_msg['common'])) echo $err_msg['common'];
                 ?>
               </div>
              
              
               <lavel class="<?php if(!empty($err_msg['email'])) echo 'err';?>">
                メールアドレス
                 <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
               </lavel>
               <div class="area-msg">
                 <?php
                  if(!empty($err_msg['email'])) echo $err_msg['email'];
                 ?>
               </div>
              
              
               <lavel class="<?php if(!empty($err_msg['pass'])) echo 'err';?>">
                パスワード <span style="font-size:12px">※英数字6文字以上</span>
                 <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
               </lavel>
               <div class="area-msg">
                 <?php
                  if(!empty($err_msg['pass'])) echo $err_msg['pass'];
                 ?>
               </div>
              
              
               <lavel class="<?php if(!empty($err_msg['pass_re'])) echo 'err';?>">
                パスワード再入力
                 <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
               </lavel>
               <div class="area-msg">
                 <?php
                  if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];
                 ?>
               </div>
               
               
               <input type="submit" value="送信">
           </form>
          </div>   
         
       </main>
       
    <!-- footer -->
       <footer id="footer">
           Copyright <a href="index.php">D.vidulum ホームページ</a>.All Rights Reserved.
       </footer>

      <script src="js/vendor/jquery-2.2.2.min.js"></script>
      <script>
         $(function(){
          var $ftr = $('#footer');
          if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
            $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
          }
        });
      </script>       
   </body>
</html>