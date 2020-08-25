<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Mission_5-1</title>
    </head>
    <body>
        <form method="post" action="">
            <?php
            //データベースに接続
            $dsn='データベース名';
            $user='ユーザー名';
            $passwordM='パスワード';
            $pdo=new PDO($dsn,$user,$passwordM,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
            
            //「board」という名前のテーブルをデータベース内に作成
            $sql='CREATE TABLE IF NOT EXISTS board'
            ."("
            ."id INT AUTO_INCREMENT PRIMARY KEY,"
            ."name char(32),"
            ."comment TEXT,"
            ."date DATETIME,"
            ."password TEXT"
            .");";
            $stmt=$pdo->query($sql);
            
            //編集フォームに入力した投稿番号に対応する名前とコメントを投稿フォームに表示（編集選択）
            //投稿フォームに表示する編集対象の番号（hidden）、名前、コメント、パスワードを抽出
            if(!empty($_POST["edit"])&&!empty($_POST["password3"])){
                
                $edit_c=$_POST["edit"];
                $password3=$_POST["password3"];
                
                $sql='SELECT * FROM board WHERE id=:id';
                $stmt=$pdo->prepare($sql);
                $stmt->bindParam(':id',$edit_c,PDO::PARAM_INT);
                $stmt->execute();
                $results=$stmt->fetchAll();
                foreach($results as $row){
                    //入力したパスワードと抽出したパスワードが一致すれば抽出
                    if($row['password']==$password3){
                        $numberE=$row['id'];
                        $nameE=$row['name'];
                        $commentE=$row['comment'];
                        $passwordE=$row['password'];
                    }
                    else {
                        $passwordN="パスワードが違います。<br>";
                    }
                }
            }
            
            ?>
            <input type="text" name="name" placeholder="名前" value="<?php if(!empty($nameE)){echo $nameE;}?>">
            <input type="text" name="comment" placeholder="コメント" value="<?php if(!empty($commentE)){echo $commentE;}?>">
            <input type="password" name="password" placeholder="パスワードを設定" value=
            "<?php if(!empty($passwordE)){echo $passwordE;}?>">
            <input type="hidden" name="hidden" value="<?php if(!empty($numberE)){echo $numberE;}?>">
            <input type="submit" name="submit">
            <br>
            <input type="number" name="delete" placeholder="削除したい投稿の番号">
            <input type="password" name="password2" placeholder="パスワード">
            <input type="submit" name="submit2" value="削除">
            <br>
            <input type="number" name="edit" placeholder="編集したい投稿の番号">
            <input type="password" name="password3" placeholder="パスワード">
            <input type="submit" name="submit3" value="編集">
        </form>
        <?php
        
        $date = date_create()->format('Y-m-d H:i:s');
        
        //投稿と編集
        if(!empty($_POST["name"])&&!empty($_POST["comment"])&&!empty($_POST["password"])){
            
            $name=$_POST["name"];
            $comment=$_POST["comment"];
            $password=$_POST["password"];
            
            //hiddenに値が入っているか
            if(!empty($_POST["hidden"])){
                
                $edit_e=$_POST["hidden"];
                
                //編集（実行）
                $sql='UPDATE board SET name=:name,comment=:comment,date=:date,password=:password WHERE id=:id';
                $stmt=$pdo->prepare($sql);
                $stmt->bindParam(':name',$name,PDO::PARAM_STR);
                $stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
                $stmt->bindParam(':date',$date);
                $stmt->bindParam(':password',$password);
                $stmt->bindParam(':id',$edit_e,PDO::PARAM_INT);
                $stmt->execute();
            }
            else{
                //投稿
                $sql=$pdo->prepare("INSERT INTO board (name,comment,date,password) VALUES(:name,:comment,:date,:password)");
                $sql->bindParam(':name',$name,PDO::PARAM_STR);
                $sql->bindParam(':comment',$comment,PDO::PARAM_STR);
                $sql->bindParam(':date',$date);
                $sql->bindParam(':password',$password);
                $sql->execute();
            }
        }
        
        //削除
        if(!empty($_POST["delete"])&&!empty($_POST["password2"])){
            
            $delete=$_POST["delete"];
            $password2=$_POST["password2"];
            
            //データベースにある該当番号に対応するパスワードを抽出
            $sql='SELECT * FROM board WHERE id=:id';
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(':id',$delete,PDO::PARAM_INT);
            $stmt->execute();
            $results=$stmt->fetchAll();
            foreach($results as $row){
                $passwordD=$row['password'];
            }
            //入力したパスワードと抽出したパスワードが一致すれば削除
            if($passwordD==$password2){
                $sql='DELETE FROM board WHERE id=:id';
                $stmt=$pdo->prepare($sql);
                $stmt->bindParam(':id',$delete,PDO::PARAM_INT);
                $stmt->execute();
            }
            else{
                $passwordN="パスワードが違います。<br>";
            }
        }
        
        //表示
        $sql='SELECT * FROM board';
        $stmt=$pdo->query($sql);
        $results=$stmt->fetchAll();
        foreach($results as $row){
            echo $row['id'].' ';
            echo $row['name'].' ';
            echo $row['comment'].' ';
            echo $row['date'].'<br>';
        }
        //削除、編集においてパスワードが一致しない場合はエラーメッセージを表示
        if(!empty($passwordN)){
            echo $passwordN;
        }
        ?>
    </body>
</html>