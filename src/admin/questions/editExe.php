<?php
require "../../dbconnect.php";
require "../../vendor/autoload.php";
use Verot\Uproad\Upload;
session_start();

// ここから更新処理↓

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$upload_error = '';
$image_name = '';
try{
  if($handle->uploaded)
  $handle->file_max_size = 5 * 1024 * 1024; // 5MB
  $handle->allowed = ['image/jpeg', 'image/png', 'image/gif'];
}
// 入力内容のバリデーション
  if(empty($_POST['content'])){
    $upload_error = '問題文を入力してください';
  }

  if(empty($_POST['choice1']) || empty($_POST['choice2']) || empty($_POST['choice3'])){
    $upload_error = '選択肢をすべて入力してください';
  }

  elseif(empty($_POST['valid']) || !in_array($_POST['valid'], ['1', '2', '3'], true)){
    $upload_error = '正解の選択肢を選んでください';
  }

// 画像のバリデーション
  $image_name = '';
  if(!empty($_FILES['image']['name']) && empty($upload_error)){

    // アップロードバリデーション
    if($_FILES['image']['error'] !== UPLOAD_ERR_OK){
      $upload_error = '画像のアップロードに失敗しました';
    }
    // ファイルサイズバリデーション
    elseif($_FILES['image']['size'] > 5 * 1024 * 1024){
      $upload_error = '画像サイズは5MB以内にしてください';
    }
    // 拡張子バリデーション
    else{
      $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
      $allow_ext = ['jpg', 'jpeg', 'png', 'gif'];
      if (!in_array($ext, $allow_ext, true)) {
      $upload_error = '画像形式は jpg / jpeg / png / gif のみ対応しています';
      }

    // 本当に画像かどうか
      else{
        $image_info = getimagesize($_FILES['image']['tmp_name']);
        if ($image_info === false) {
          $upload_error = 'アップロードされたファイルは画像ではありません';
        }

        // すべて通過
        else{
          $image_name = uniqid(mt_rand(), true) . '.' . $ext;
          $image_path = dirname(__FILE__) . '/../../assets/img/quiz/' . $image_name;
          move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        }
      }
    }
  }

// エラーがあった場合は処理を中断
  if($upload_error){
    echo '<div style="color: red; padding: 20px; border: 2px solid red; margin: 20px;">';
    echo 'エラー: ' . htmlspecialchars($upload_error, ENT_QUOTES, 'UTF-8');
    echo '<br><a href="javascript:history.back()">戻る</a>';
    echo '</div>';
    exit;
  }

  try {
    // トランザクション開始
    $dbh->beginTransaction();

    // questionsテーブルの更新
    // SQL命令の準備
    // $stmt = $dbh->prepare('UPDATE questions SET content = :content, supplement = :supplement WHERE id = :id');
    if($image_name !== ''){
      $sql = 'UPDATE questions SET content = :content, image = :image, supplement = :supplement WHERE id = :id';
    } else {
      $sql = 'UPDATE questions SET content = :content, supplement = :supplement WHERE id = :id';
    }

    // UPDATE命令にポストデータの内容をセット
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':content', $_POST['content']);
    $stmt->bindValue(':supplement', $_POST['supplement']);
    $stmt->bindValue(':id', $_POST['id']);

    if($image_name !== ''){
      $stmt->bindValue(':image', $image_name);
    }

    // SQL命令の実行
    $stmt->execute();


    // choicesテーブルの更新

    // choicesテーブルの取得
    $choices = [
      ['id' => $_POST['choice_id1'], 'name' => $_POST['choice1']],
      ['id' => $_POST['choice_id2'], 'name' => $_POST['choice2']],
      ['id' => $_POST['choice_id3'], 'name' => $_POST['choice3']]
  ];
    // SQL命令の準備
    $stmt = $dbh->prepare('UPDATE choices SET name = :name, valid = :valid WHERE id = :id');

    // UPDATE命令にポストデータの内容をセット
    for($i = 0; $i < count($choices); $i++){
      $stmt->bindValue(':name', $_POST['choice'.($i + 1)]);
      $stmt->bindValue(':valid',($i + 1 == $_POST['valid'] ? 1 : 0));
      $stmt->bindValue(':id',$_POST['choice_id'.($i + 1)]);
      
      // SQL命令の実行
      $stmt->execute();
    }

    // 全部成功したら確定
    $dbh->commit();

    // リダイレクト
    header("Location: edit.php?id=" . $_POST['id']);
    exit;
  }catch (Exception $e) {
    $dbh->rollBack();
    echo "更新に失敗しました: " . $e->getMessage();
  exit;
  }
}