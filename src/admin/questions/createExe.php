<?php
require "../../dbconnect.php";
require "../../vendor/autoload.php";
use Verot\Upload\Upload;
$content = $_POST['content'];
$choices = [$_POST['choice1'], $_POST['choice2'], $_POST['choice3']];
$valid = $_POST['valid'];
$supplement = $_POST['supplement'];

try{
  $file = $_FILES['image'];
  $lang = 'ja_JP';

// アップロードされたファイルを渡す
$handle = new Upload($file, $lang);

if ($handle->uploaded) {
  $handle->file_max_size = 5 * 1024 * 1024; // 5MB
  $handle->allowed = ['image/jpeg', 'image/png', 'image/gif'];
  $handle->image_convert = 'png'; // すべての画像をPNGに変換する例
  $handle->image_resize = true;
  $handle->image_x = 718; // 幅を718pxにリサイズ
  $handle->image_ratio_y = true;
  
  // アップロードディレクトリを指定して保存
  $handle->process('../../assets/img/quiz/');
  if ($handle->processed) {
    // アップロード成功
    $image_name = $handle->file_dst_name;
    $handle->clean();
  } else {
    // アップロード処理失敗
    throw new Exception($handle->error);
  }
} else {
  // アップロード失敗
  throw new Exception($handle->error);
}

}catch (Exception $e) {
  
    echo '<div style="color: red; padding: 20px; border: 2px solid red; margin: 20px;">';
    echo '画像アップロードエラー: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo '<br><a href="javascript:history.back()">戻る</a>';
    echo '</div>';
    exit;
}

// 画像アップロードのエラーハンドリング追加
// $image_name = '';
// $upload_error = '';
// if(!empty($_FILES['image']['name'])){

//   // アップロードバリデーション
//   if($_FILES['image']['error'] !== UPLOAD_ERR_OK){
//     $upload_error = '画像のアップロードに失敗しました';
//   }
//   // ファイルサイズバリデーション
//   elseif($_FILES['image']['size'] > 5 * 1024 * 1024){
//     $upload_error = '画像サイズは5MB以内にしてください';
//   }
//   // 拡張子バリデーション
//   else{
//     $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
//     $allow_ext = ['jpg', 'jpeg', 'png', 'gif'];
//     if (!in_array($ext, $allow_ext, true)) {
//     $upload_error = '画像形式は jpg / jpeg / png / gif のみ対応しています';
//     }

//   // 本当に画像かどうか
//     else{
//       $image_info = getimagesize($_FILES['image']['tmp_name']);
//       if ($image_info === false) {
//         $upload_error = 'アップロードされたファイルは画像ではありません';
//       }

//       // すべて通過
//       // else{
//       //   $image_name = uniqid(mt_rand(), true) . '.' . $ext;
//       //   $image_path = dirname(__FILE__) . '/../../assets/img/quiz/' . $image_name;
//       //   move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
//       // }
//     }
//   }
// }

// エラーがあった場合は処理を中断

// if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
//     $image_name = uniqid(mt_rand(), true) . '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
//     $image_path = dirname(__FILE__) . '/../../assets/img/quiz/' . $image_name;
//     move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
// }

// bindValueを使った修正版
$sql = "INSERT INTO questions (content, image, supplement) VALUES (:content, :image, :supplement)";
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':content', $content, PDO::PARAM_STR);
$stmt->bindValue(':image', $image_name, PDO::PARAM_STR);
$stmt->bindValue(':supplement', $supplement, PDO::PARAM_STR);
$stmt->execute();

$lastId = $dbh->lastInsertId();

$sql2 = "INSERT INTO choices (question_id, name, valid) VALUES (:question_id, :name, :valid)";
$stmt2 = $dbh->prepare($sql2);
for($i = 0; $i < count($choices); $i++){
  $stmt2->bindValue(':question_id', $lastId, PDO::PARAM_INT);
  $stmt2->bindValue(':name', $choices[$i], PDO::PARAM_STR);
  $stmt2->bindValue(':valid', ($i + 1 == $valid ? 1 : 0), PDO::PARAM_INT);
  $stmt2->execute();
}

header('Location: ../index.php');
exit();
// 直接SQLに埋め込んでいる（超危険！）
// $dbh->exec($sql);
// $lastId = $dbh->lastInsertId();
// for($i = 0; $i < count($choices); $i++){
//   if($i + 1 == $valid){
//     $sql2 = "INSERT INTO choices (question_id, name, valid) VALUES ('$lastId', '$choices[$i]', '1')";
//   }else{
// $sql2 = "INSERT INTO choices (question_id, name, valid) VALUES ('$lastId', '$choices[$i]', '0')";
//   }
// $dbh->exec($sql2);
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <main>
    <div>
      
    </div>
  </main>
</body>
</html>