<?php 

//共通変数・関数ファイルを読込み
require('functions.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(isset($_POST['productId']) && isset($_SESSION['user_id'])){
    debug('Ajax処理を開始');

    $p_id = $_POST['productId'];
    debug('このProdictidを削除するよー＞'.$p_id);
    $rst = Db::deleteSubject($p_id);
    if($rst) {
        debug('deleteSubjectに成功しました');
    }else{
        debug('deleteSubjectに失敗しました');
    }
}

