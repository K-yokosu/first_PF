<?php

//共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログインページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//========================================================
//ログイン画面処理

//post送信されていた場合
if(!empty($_POST)){
    
    debug('POST送信があります。');
    
    //変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false; //ショートハンド（略記法）という書き方
    
    //未入力チェック
    validRequired($email,'email');
    validRequired($pass,'pass');
    
        
        //emailの形式チェック
        validEmail($email,'email');
        //emailの最大文字数チェック
        validMaxLen($email,'email');
        
        
        //パスワードの半角英数字チェック
        validHalf($pass,'pass');
        //パスワードの最大文字数チェック
        validMaxLen($pass,'pass');
        //パスワードの最小文字数チェック
        validMinLen($pass,'pass');
        
        
      
        if(empty($err_msg)){
            
            debug('バリデーションOKです。');
            
            //例外処理
            try{
                //DBへ接続
                $dbh = dbConnect();
                //SQL文作成
                $sql = 'SELECT password,id FROM users WHERE email = :email';
                $data = array(':email' => $email);
                //クエリ実行
                $stmt = queryPost($dbh,$sql,$data);
                //クエリ結果の値を取得
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                debug('クエリ結果の中身：'.print_r($result,true));
                
                //パスワード照合
                if(!empty($result) && password_verify($pass, array_shift($result))){
                    debug('パスワードがマッチしました。');
                    
                    //ログイン有効期限（デフォルトを1時間とする）
                    $sesLimit = 60*60;
                    //最終ログイン日時を現在日時に
                    $_SESSION['login_date'] = time(); //time関数は1970年1月1日00:00:00を0として、1秒経過するごとに１ずつ増加させた値が入る
                    
                    //ログイン保持にチェックがある場合
                    if($pass_save){
                        debug('ログイン保持にチェックがあります。');
                        //ログイン有効期限を30日にしてセット
                        $_SESSION['login_limit'] = $sesLimit * 24 * 30;
                    }else{
                        debug('ログイン保持にチェックはありません。');
                        //次回からログイン保持しないので、ログイン有効期限を1時間後にセット
                        $_SESSION['login_limit'] = $sesLimit;
                    }
                    
                    //ユーザーIDを格納
                    $_SESSION['user_id'] = $result['id'];
                    
                    debug('セッション変数の中身：'.print_r($_SESSION,true));
                    debug('マイページへ遷移します。');
                    header("Location:mypage.php");//マイページへ
                }else{
                    debug('パスワードがアンマッチです。');
                    $err_msg['common'] = MSG09;
                }
                
            } catch (Exception $e){
                error_log('エラー発生:' . $e->getMessage());
                $err_msg['common'] = MSG07;
            }
      }
    }
  
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>
<!DOCTYPE html>
<html lang="ja">
   <head>
       <meta charset="utf-8">
       <title>webサービス作成1</title>
       <link rel="stylesheet" href="css/common.css">
       <link rel="stylesheet" href="css/login.css">
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
              <h2>ログイン</h2>
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