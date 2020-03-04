<?php
?>
<!DOCTYPE html>
<html lang="ja">
   <head>
       <meta charset="utf-8">
       <title>webサービス作成1</title>
       <link rel="stylesheet" href="css/common.css">
       <link rel="stylesheet" href="css/index.css">
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
          
         <!-- トップ画像 -->
           <div class="top-image">
               <img src='img/top-image.jpg'>
               <p>1円、だいじにしぃやぁ〜〜</p>
           </div>
           
         <!-- サービスの3つのポイント -->
           <div class="merit">
               <p>D.vidulumの3つのメリット</p>
            <section>
               <h1>家庭の出費が一目瞭然！！</h1>
               <p>光熱費からお菓子一つまで分類分けできるので、今までどこに消えていたのか把握できなかったお金の行方を可視化します。</p>
            </section>
            <section>
               <h1>１日の可処分額を教えてくれる！！</h1>
               <p>光熱費からお菓子一つまで分類分けできるので、今までどこに消えていたのか把握できなかったお金の行方を可視化します。</p>
            </section>
            <section>
               <h1>貯蓄目標が立てられる！！</h1>
               <p>貯蓄ペースが可視化されるのでモチベーションが保てます。</p>
            </section>
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