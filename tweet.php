<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./reset.css">
    <link rel="stylesheet" href="./style.min.css">
    <title>勉強管理</title>
</head>
<body>
    <header>
        <h4>2019年6月2日</h4>　<!--PHPで日付取得するように変更する-->
    </header>

    <main class="tweet">
        <section class="main-board">
            <h1>tweet</h1>
            <form class="tweet-board" action="" method="POST">
                <div class="tweet-board__header">
                    <label for="tweet-header">ヘッダー</label>
                    <textarea class="textarea-init" name="tweet-header" id="" cols="30" rows="10"></textarea>
                </div>
                <div class="tweet-board__text">
                    <textarea class="textarea-init" name="tweet-text" id="" cols="30" rows="10"></textarea>
                </div>
                <div class="tweet-board__footer">
                    <label for="">フッター</label>
                    <textarea class="textarea-init" name="tweet-footer" id="" cols="30" rows="10"></textarea>
                </div>
                <input type="submit" class="btn-tweet submit-init" value="投稿する" >
            </form>
        </section>
    </main>
    
</body>
</html>