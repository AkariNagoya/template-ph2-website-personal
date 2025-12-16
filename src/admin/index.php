<?php
require '../dbconnect.php';

// ここから削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  try{
  $dbh->beginTransaction();
  // choicesテーブルから削除
  // SQL命令を準備する
  $stmt = $dbh->prepare('DELETE FROM choices WHERE question_id = :id');
  // DELETE命令にポストデータの内容をセットする
  $stmt->bindValue(':id', $_POST['delete_id']);
  // SQL命令を実行する
  $stmt->execute();

  // questionsテーブルから削除
  $stmt = $dbh->prepare('DELETE FROM questions WHERE id = :id');
  $stmt->bindValue(':id',$_POST['delete_id']);
  $stmt->execute();
  $dbh->commit();

  header('Location: index.php');
  exit;

  }catch (Exception $e) {
    // ロールバック
    $dbh->rollBack();
    echo "削除に失敗しました: " . $e->getMessage();
    exit;
  }
} 

$stmt = $dbh->query('SELECT id, content FROM questions');
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>問題一覧 | POSSE</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<!-- Header -->
<header class="bg-teal-400 text-white flex justify-between items-center px-6 py-4 text-lg">
  <div class="font-extrabold text-2xl">POSSE</div>
  <a href="./auth/signout.php" class="hover:underline">ログアウト</a>
</header>

<div class="flex">

  <!-- Sidebar -->
  <aside class="w-48 bg-[#e6d2b8] min-h-screen py-6">
    <ul>
      <li class="px-5 py-2 cursor-pointer text-gray-700 hover:bg-amber-200">ユーザー招待</li>
      <li class="px-5 py-2 font-bold text-teal-700 bg-amber-200">問題一覧</li>
      <!-- <li class="px-5 py-2 cursor-pointer text-gray-700 hover:bg-amber-200">問題作成</li> -->
      <li>
        <a href="./questions/create.php" class="block px-5 py-2 text-gray-700 hover:bg-amber-200 cursor-pointer">
        問題作成
        </a>
      </li>

    </ul>
  </aside>

  <!-- Main -->
  <main class="flex-1 bg-[#F4E0C8] p-8">
    <h2 class="text-xl font-semibold mb-4">問題一覧</h2>

    <table class="w-full border-collapse text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="border-b p-2 text-left">ID</th>
          <th class="border-b p-2 text-left">問題</th>
          <th class="border-b p-2 text-left"></th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach($questions as $question){
          echo
          '<tr class="border-b">
          <td class="p-2">'.htmlspecialchars($question["id"], ENT_QUOTES, 'UTF-8').'</td>
          <td class="p-2"><a class="text-blue-600 hover:underline" href="./questions/edit.php?id='.htmlspecialchars($question["id"], ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($question["content"], ENT_QUOTES, 'UTF-8').'</a></td>
          <td class="p-2">
            <form method="POST" action="index.php" style="display:inline;" onsubmit="return confirm(\'本当に削除しますか？\');">
              <input type="hidden" name="delete_id" value="'.htmlspecialchars($question["id"],ENT_QUOTES,'UTF-8').'">
              <button type="submit" class="text-gray-600 hover:underline cursor-pointer">削除</button>
            </form>
          </td>
          </tr>';
        }
        ?>
      </tbody>
    </table>
  </main>
</div>

</body>
</html>

