<?php
require_once('../../dbconnect.php');

// ログイン処理
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $stmt = $dbh->prepare('SELECT * FROM users WHERE email = :email');
  $stmt->bindValue(':email', $_POST['email']);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ログイン</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F3E3CC] min-h-screen">

  <!-- ヘッダー -->
  <header class="bg-[#3CB4A0] text-white">
    <div class="max-w-5xl mx-auto flex items-center justify-between py-4 px-4">
      <div class="flex items-center">
        <span class="text-3xl font-bold tracking-wide">POSSE</span>
      </div>
      <a href="#" class="text-sm text-gray-800">ログアウト</a>
    </div>
  </header>

  <!-- メイン内容 -->
  <main class="max-w-5xl mx-auto mt-10 px-4">
    <h1 class="text-4xl font-bold mb-8">ログイン</h1>

    <form action="" method="POST" class="space-y-6">

      <!-- Email -->
      <div>
        <label class="block mb-1 font-medium">Email</label>
        <input
          type="email"
          class="w-full border border-gray-300 rounded p-2"
          placeholder="Email"
          required
        />
      </div>

      <!-- パスワード -->
      <div>
        <label class="block mb-1 font-medium">パスワード</label>
        <input
          type="password"
          class="w-full border border-gray-300 rounded p-2"
          placeholder="パスワード"
          required
        />
      </div>

      <!-- ボタン -->
      <button
        type="submit"
        class="bg-[#3CB4A0] text-white px-4 py-2 rounded shadow hover:opacity-80"
      >
        ログイン
      </button>

    </form>
  </main>

</body>
</html>
