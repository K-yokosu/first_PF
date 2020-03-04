<?php

//共通変数・関数ファイルの読み込み
require('function.php');
require('auth.php');

//functionへ移行予定------------------------------------------------------------------------
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
function spend_allget($str1){
    $dbh = dbConnect();
    $sql = 'SELECT SUM(price) FROM spend WHERE year = :year AND user_id = :user_id';
    $data = array(':year' => $str1, ':user_id' => $_SESSION['user_id']);
        
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $spend_all = (int)$result['SUM(price)'];
    return $spend_all;
}
//functionへ移行予定------------------------------------------------------------------------

$timestamp = time();

$current_year = date("Y年",$timestamp); 
$current_month = date("n月",$timestamp);

$income_month = income_month($current_year,$current_month);
var_dump($income_month);
$income_year = income_year($current_year);
var_dump($income_year);
$savings_value = saving_get($current_year);
var_dump($savings_value);
$spend_value = spend_allget($current_year);
var_dump($spend_value);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
   <p><?php echo $current_year; ?></p>
   <p><?php echo $current_month; ?></p>
   
    
</body>
</html>