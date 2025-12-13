<?php
require "../../dbconnect.php";
$content = $_POST['content'];
$choice1 = $_POST['choice1'];
$choice2 = $_POST['choice2'];
$choice3 = $_POST['choice3'];
$choices = [$choice1, $choice2, $choice3];
$valid = $_POST['valid'];
$supplement = $_POST['supplement'];

// 画像アップロードのエラーハンドリング追加
$image_name = '';
if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $image_name = uniqid(mt_rand(), true) . '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
    $image_path = dirname(__FILE__) . '/../../assets/img/quiz/' . $image_name;
    move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
}

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