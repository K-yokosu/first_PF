<?php

//ログを取る
ini_set('log_errors','on');
//ログの出力ファイルを指定
ini_set('error_log','php.log');

//=================================================
//デバッグ(ネット上に上げるときにはfalseにしてログを吐かないようにする)
$debug_flg = true;
//デバッグログ関数
function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ：'.$str);
    }
}

//=================================================
//セッション準備・セッション有効期限を延ばす
//セッションファイルの置き場を変更する（/var/tmp/以下に置くと30日は削除されない）
session_save_path("/var/tmp/");
//ガーベージコレクションが削除するセッションの有効期限を設定（３０日以上経っているものに対してだけ100分の1の確率で削除）
ini_set('session.gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime', 60*60*24*30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id();


//=================================================
//画面表示処理開始ログ吐き出し関数
function debugLogStart(){
    debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理開始');
    debug('セッションID：'.session_id());
    debug('セッション変数の中身：'.print_r($_SESSION,true));
    debug('現在日時のタイムスタンプ：'.time());
    if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
        debug('ログイン期限日時タイムスタンプ：'.($_SESSION['login_date'] + $_SESSION['login_limit']));
    }
}

//=================================================
//定数
//エラーメッセージを定数に設定
define('MSG01','入力必須です');
define('MSG02','Emailの形式で入力してください');
define('MSG03','パスワード（再入力）が合っていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','6文字以上で入力してください');
define('MSG06','256文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08','そのEmailは既に登録されています');
define('MSG09','メールアドレスまたはパスワードが違います');
define('MSG10','重大なエラー発生');

//=================================================
$timestamp = time();

$current_year = date("Y年",$timestamp); 
$current_month = date("n月",$timestamp);

//=======================================================
//バリデーション関数
//エラーメッセージ格納用の配列
$err_msg = array();

//バリデーション関数（未入力チェック）
function validRequired($str,$key){
    if(empty($str)){
        global $err_msg;
        $err_msg[$key] = MSG01;
    }else{
        debug('未入力チェックOK');
    }
}

//バリデーション関数(Email形式チェック)
function validEmail($str, $key){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG02;
    }
}
function validEmailDup($email){
    global $err_msg;
    //例外処理
    try{
        //DBへ接続
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT count(*) FROM users WHERE email = :email';
        $data = array(':email' => $email);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        //クエリ結果の値を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        //array_shift関数は配列の先頭を取り出す関数です。クエリ結果は配列形式で入っているので、array_shiftで1つ目だけ取り出して判定します
        if(!empty(array_shift($result))){
            $err_msg['email'] = MSG08;
        }
    } catch(Exception $e){
        error_log('エラー発生：' . $e->getMessage());
        $err_msg['common'] = MSG07;
    }
}

//バリデーション関数（同値チェック）
function validMatch($str1, $str2, $key){
    if($str1 !== $str2){
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}
//バリデーション関数（最小文字数チェック）
function validMinLen($str, $key, $min = 6){
    if(mb_strlen($str) < $min){
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}
//バリデーション関数（最大文字数チェック）
function validMaxLen($str, $key, $max = 255){
    if(mb_strlen($str) > $max){
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}
//バリデーション関数（半角英数字チェック）
function validHalf($str, $key){
    if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG04;
    }else{
        debug('半角チェックOK');
    }
}


//DB接続関数
function dbConnect(){
    //DBへの接続準備
    $dsn = 'mysql:dbname=vidulum;host=localhost;charset=utf8';
    $user = 'root';
    $password = 'root';
    $options = array(
        //SQL実行失敗時にはエラーコードのみ設定
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        //デフォルトフェッチモードを連想配列形式に設定
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        //バッファードクエリを使う（一度に結果セットを全て取得し、サーバー負荷を軽減）
        //SELECTで得た結果に対してもrowCountメソッドを使えるようにする
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    //PDOオブジェクト生成（DBへ接続）
    $dbh = new PDO($dsn,$user,$password,$options);
    return $dbh;
}
function queryPost($dbh,$sql,$data){
    //クエリー作成
    $stmt = $dbh->prepare($sql);
    //プレースホルダに値をセットし、SQL文を実行
    $stmt->execute($data);
    return $stmt;
}
//mypage機能
function sql_make($str){
    
    global $switch_msg;
    switch($str){
    case 0:
    case 1:
        return 'SELECT SUM(price) FROM spend WHERE year =:year AND month = :month AND p_type = :p_type AND user_id = :user_id';
        break;
        
    default:
        debug('sql_makeが失敗しました。');
        break;
  }
}

  
//取得する情報によってswitch文で処理を振り分けるための配列を作成
function switch_case(){
    global $switch_msg;
    for($i=0; $i<2; $i++){
        if(!empty($switch_msg[$i])){
            $local_sql = sql_make($i); 
        }
    }
    return $local_sql;
}

//spend_tableの各表DB情報を呼び出す関数
function calldata($number){
    global $spend_table;
    if(!empty($spend_table[$number])){
        echo $spend_table[$number];
    }else{
        echo 0;
    }
}
//spend_tableの平均関数
function callavg($number1,$number2){
    global $spend_table;
    $avg;
    $a;
    for($i=$number1; $i<$number2 +1; $i++){
        if(!empty($spend_table[$i])){
            
            $avg += $spend_table[$i];
            $a++;
        }
    }
    if($avg == 0){
        return 0;
    }else{
        return (int)$avg /= $a;
    }
}
//spend?table各項目の合計
function callsum($number1,$number2){
    global $spend_table;
    $sum;
    for($i=$number1; $i<$number2 +1; $i++){
        $sum += $spend_table[$i];
    }
    echo $sum;
}
//spend_table各月の合計
function call_monthsum($number){
    global $spend_table;
    $month_sum = $spend_table[0+$number] + $spend_table[12+$number] + $spend_table[24+$number] + $spend_table[36+$number] + $spend_table[48+$number]
                  + $spend_table[60+$number] + $spend_table[72+$number] + $spend_table[84+$number] + $spend_table[96+$number] + $spend_table[108+$number];
    echo $month_sum;
}
//spend_table平均の合計
function call_avg_sum(){
    global $w_avg;
    global $ele_avg;
    global $g_avg;
    global $h_avg;
    global $p_avg;
    global $t_avg;
    global $f_avg;
    global $enj_avg;
    global $s_avg;
    global $o_avg;
    $avg_array = array($w_avg,$ele_avg,$g_avg,$h_avg,$p_avg,$t_avg,$f_avg,$enj_avg,$s_avg,$o_avg);
    $avg_sum;
    $a;
    for($i=0; $i<10; $i++){
        if(!empty($avg_array[$i])){
            $avg_sum += $avg_array[$i];
            $a++;
        }
    }
    $avg_sum_re = $avg_sum / $a;
    return (int)$avg_sum_re;
}
//マイページ右のいろいろな金額を計算するためにDBから情報を取得する関数
function income_month($str1, $str2){
    
    $dbh = dbConnect();
    $sql = 'SELECT SUM(price) FROM income WHERE year = :year AND month = :month AND user_id = :user_id';
    $data = array(':year' => $str1, ':month' => $str2, ':user_id' => $_SESSION['user_id']);
        
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $income_mon = (int)$result['SUM(price)'];
    return $income_mon;
}
function income_year($str1){
    
    $dbh = dbConnect();
    $sql = 'SELECT SUM(price) FROM income WHERE year = :year AND user_id = :user_id';
    $data = array(':year' => $str1, ':user_id' => $_SESSION['user_id']);
        
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $income_yr = (int)$result['SUM(price)'];
    return $income_yr;
}

function saving_get($str1){
    $dbh = dbConnect();
    $sql = 'SELECT price FROM savings WHERE year = :year AND user_id = :user_id';
    $data = array(':year' => $str1, ':user_id' => $_SESSION['user_id']);
        
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $savings = (int)$result['price'];
    return $savings;
}
function spend_year($str1){
    $dbh = dbConnect();
    $sql = 'SELECT SUM(price) FROM spend WHERE year = :year AND user_id = :user_id';
    $data = array(':year' => $str1, ':user_id' => $_SESSION['user_id']);
        
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $spend_year = (int)$result['SUM(price)'];
    return $spend_year;
}
function spend_month($str1,$str2){
    $dbh = dbConnect();
    $sql = 'SELECT SUM(price) FROM spend WHERE year = :year AND month = :month AND user_id = :user_id';
    $data = array(':year' => $str1, ':month' => $str2, ':user_id' => $_SESSION['user_id']);
        
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $spend_month = (int)$result['SUM(price)'];
    return $spend_month;
}
?>