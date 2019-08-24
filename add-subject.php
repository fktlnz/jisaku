<?php 

require('functions.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザー登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');


if(!empty($_POST)) {
    //追加する項目を取得
    $subject_name = empty($_POST['name']) ? '' : $_POST['name'];
    $u_id = empty($_SESSION['user_id']) ? '' : $_SESSION['user_id'];

    $rst = Db::addSubject($subject_name, $u_id);
    if($rst) {
        debug('メインページに遷移します');
        setDispMessage('項目を追加しました');
        debug('メッセージ内容を記載しました：＝＞'.$_SESSION['msg']);
        header("Location:main-board.php");
    }else{
        setDispMessage('項目の追加に失敗しました。一度ログアウトしてから再度お試しください。');
        header("Location:main-board.php");
    }
    
}



?>



<?php require('head.php') ?>
<body>
    
    <main class="add-subject">
        <section class="main-board">
            
            <h1>項目を追加する</h1>
            <form class="l-main-bord add-subject-board" action="" method="POST">
                
                <div class="add-subject-board__twitter c-form">
                    <label>項目名</label>
                    <input type="text" class="text-init" name="name">
                </div>                
                <input type="submit" class="btn-add submit-init" value="追加する" >
            </form>
        </section>
    </main>
    
</body>
</html>