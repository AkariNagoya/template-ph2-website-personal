<?php
require_once('../../dbconnect.php');

// ここから更新処理↓
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // トランザクション開始
    $dbh->beginTransaction();

    // questionsテーブルの更新
    // SQL命令の準備
    $stmt = $dbh->prepare('UPDATE questions SET content = :content, supplement = :supplement WHERE id = :id');

    // UPDATE命令にポストデータの内容をセット
    $stmt->bindValue(':content', $_POST['content']);
    $stmt->bindValue(':supplement', $_POST['supplement']);
    $stmt->bindValue(':id', $_POST['id']);

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

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../../admin/index.php");
    exit;
}

$id = intval($_GET['id']);

// questions テーブルから1件取得
$sql = "SELECT * FROM questions WHERE id = :id";
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$question = $stmt->fetch(PDO::FETCH_ASSOC);

// choices テーブルから選択肢を取得（3件）
$sql = "SELECT * FROM choices WHERE question_id = :id ORDER BY id ASC";
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$choices = $stmt->fetchAll(PDO::FETCH_ASSOC);

for ($i = 0; $i < 3; $i++) {

    // choices[$i] が存在しない場合 → 空で作る
    if (!isset($choices[$i])) {
        $choices[$i] = ['name' => '', 'valid' => 0];
        continue; // ← ここで次へ（これで content に触れないので警告ゼロ）
    }

    // content が存在しない場合 or null の場合
    if (!isset($choices[$i]['name']) || $choices[$i]['name'] === null) {
        $choices[$i]['name'] = '';
    }
}

// for ($i = 0; $i < 3; $i++) { 
//     if (!isset($choices[$i])) {
//         // 足りない分は空の選択肢を追加
//         $choices[$i] = ['content' => ''];
//     } else {
//         // null が入っていた場合の対策
//         if ($choices[$i]['content'] === null) {
//             $choices[$i]['content'] = '';
//         }
//     }
// }

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>問題編集</title>
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

  <!-- ヘッダー -->
  <header class="bg-teal-400 text-white flex justify-between items-center px-6 py-4 text-lg">
    <div class="font-extrabold text-2xl">POSSE</div>
    <a href="../auth/signout.php" class="hover:underline">ログアウト</a>
  </header>

  <div class="flex">

    <!-- サイドバー -->
    <aside class="w-48 bg-[#e6d2b8] min-h-screen py-6">
      <ul>
        <li class="block w-full px-5 py-2 cursor-pointer text-gray-700 hover:bg-amber-200">ユーザー招待</li>
        <li>
          <a href="../index.php" class="block w-full px-5 py-2 text-gray-700 hover:bg-amber-200 cursor-pointer">
          問題一覧
          </a>
        </li>
        <!-- <li class="px-5 py-2 cursor-pointer text-gray-700 hover:bg-amber-200">問題作成</li> -->
        <li>
          <a href="./create.php" class="block px-5 py-2 text-gray-700 hover:bg-amber-200 cursor-pointer">
          問題作成
          </a>
        </li>
      </ul>
    </aside>

    <!-- メインコンテンツ -->
    <main class="flex-1 bg-[#f5e3cc] p-10">
      <h1 class="text-3xl font-bold mb-8">問題編集</h1>

      <!-- form  -->
      <form action="edit.php" method="post" enctype="multipart/form-data">

      <!-- hidden -->
      <input type="hidden" name="id" value="<?= $question['id'] ?>">

      <!-- 問題文 -->
      <div class="mb-6">
        <label class="block mb-1">問題文:</label>
        <input type="text" name="content" class="w-full border border-gray-300 rounded p-2" placeholder="問題文を入力してください" value="<?= htmlspecialchars($question['content'], ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <!-- 選択肢 -->
      <div class="mb-6">
        <label class="block mb-1">選択肢:</label>
        <div class="flex space-x-4">
          <?php for($i = 0; $i < count($choices); $i++):?>
          <input type="hidden" name="choice_id<?= $i + 1 ?>" value="<?= $choices[$i]['id'] ?>">
          <input type="text" name="choice<?= $i + 1 ?>" class="flex-1 border border-gray-300 rounded p-2" placeholder="選択肢<?= $i + 1 ?>を入力してください" value="<?= $choices[$i]['name'] !== '' ? htmlspecialchars($choices[$i]['name'], ENT_QUOTES, 'UTF-8') : '未設定' ?>">
          <?php endfor; ?>
          <!-- <input type="hidden" name="choice_id1" value="<?= $choices[0]['id'] ?>">
          <input type="text" name="choice1" class="flex-1 border border-gray-300 rounded p-2" placeholder="選択肢1を入力してください" value="<?= htmlspecialchars($choices[0]['name'], ENT_QUOTES, 'UTF-8') ?>">

          <input type="hidden" name="choice_id2" value="<?= $choices[1]['id'] ?>">
          <input type="text" name="choice2" class="flex-1 border border-gray-300 rounded p-2" placeholder="選択肢2を入力してください" value="<?= htmlspecialchars($choices[1]['name'], ENT_QUOTES, 'UTF-8') ?>">

          <input type="hidden" name="choice_id3" value="<?= $choices[2]['id'] ?>">
          <input type="text" name="choice3" class="flex-1 border border-gray-300 rounded p-2" placeholder="選択肢3を入力してください" value="<?= htmlspecialchars($choices[2]['name'], ENT_QUOTES, 'UTF-8') ?>"> -->
        </div>
        <!-- <?php 
        var_dump($choices);
        ?> -->
      </div>

      <!-- 正解 -->
      <div class="mb-6">
        <label class="block mb-1">正解の選択肢</label>
        <div class="flex items-center space-x-6">
          <label class="flex items-center space-x-2">
            <input type="radio" name="valid" value="1" class="w-4 h-4" <?= $choices[0]['valid'] == 1 ? 'checked' : '' ?>>
            <span>選択肢1</span>
          </label>
          <label class="flex items-center space-x-2">
            <input type="radio" name="valid" value="2" class="w-4 h-4" <?= $choices[1]['valid'] == 1 ? 'checked' : '' ?>>
            <span>選択肢2</span>
          </label>
          <label class="flex items-center space-x-2">
            <input type="radio" name="valid" value="3" class="w-4 h-4" <?= $choices[2]['valid'] == 1 ? 'checked' : '' ?>>
            <span>選択肢3</span>
          </label>
        </div>
      </div>

      <!-- 画像 -->
      <div class="mb-6">
        <label class="block mb-1">問題の画像</label>
        <!-- 今登録されてる画像を表示 -->
        <?php if (!empty($question['image'])): ?>
          <div class="mb-2">
            <img src="/../../assets/img/quiz/<?= $question['image'] ?>" class="w-32 border">
          </div>
        <?php endif; ?>
        <input type="file" name="image" class="w-full border border-gray-300 rounded p-2 bg-white">
      </div>

      <!-- 補足 -->
      <div class="mb-6">
        <label class="block mb-1">補足:</label>
        <input type="text" name="supplement" class="w-full border border-gray-300 rounded p-2" placeholder="補足を入力してください" value="<?= htmlspecialchars($question['supplement'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <!-- 更新ボタン -->
      <button class="w-full bg-[#4DB6AC] text-white py-3 rounded text-lg">
        更新
      </button>

      </form>

    </main>
  </div>

</body>
</html>
