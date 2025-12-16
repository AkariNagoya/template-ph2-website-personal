<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>問題作成 | POSSE</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">

<!-- Header -->
<header class="bg-teal-400 text-white flex justify-between items-center px-6 py-4">
  <div class="font-extrabold text-2xl">POSSE</div>
  <a href="../auth/signout.php" class="hover:underline">ログアウト</a>
</header>

<div class="flex">

  <!-- Sidebar -->
  <aside class="w-48 bg-[#e6d2b8] min-h-screen py-6">
    <ul>
      <li class="block w-full px-5 py-2 cursor-pointer text-gray-700 hover:bg-amber-200">ユーザー招待</li>
      <!-- <li class="px-5 py-2 cursor-pointer text-gray-700 hover:bg-amber-200">問題一覧</li> -->
      <li>
        <a href="../index.php" class="block w-full px-5 py-2 text-gray-700 hover:bg-amber-200 cursor-pointer">
          問題一覧
        </a>
      </li>

      <li class="px-5 py-2 font-bold text-teal-700 bg-amber-200">問題作成</li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 bg-[#f5e3cc] p-10">

    <h2 class="text-2xl font-semibold mb-6">問題作成</h2>

    <!-- Form -->
    <form class="space-y-6 max-w-4xl" method="POST" action="createExe.php" enctype="multipart/form-data">

      <!-- 問題文 -->
      <div>
        <label class="block mb-1 font-medium">問題文:</label>
        <input type="text" placeholder="問題文を入力してください" class="w-full border rounded-md p-2" name="content"/>
      </div>

      <!-- 選択肢 -->
      <div>
        <label class="block mb-1 font-medium">選択肢:</label>
        <div class="flex gap-2">
          <input type="text" placeholder="選択肢1を入力してください" class="w-1/3 border rounded-md p-2" name="choice1"/>
          <input type="text" placeholder="選択肢2を入力してください" class="w-1/3 border rounded-md p-2" name="choice2"/>
          <input type="text" placeholder="選択肢3を入力してください" class="w-1/3 border rounded-md p-2" name="choice3"/>
        </div>
      </div>

      <!-- 正解 -->
      <div>
        <label class="block mb-2 font-medium">正解の選択肢</label>
        <div class="flex items-center gap-6">
          <label class="flex items-center gap-1">
            <input type="radio" name="valid" checked value="1"/>
            <span>選択肢1</span>
          </label>
          <label class="flex items-center gap-1">
            <input type="radio" name="valid" value="2"/>
            <span>選択肢2</span>
          </label>
          <label class="flex items-center gap-1">
            <input type="radio" name="valid" value="3"/>
            <span>選択肢3</span>
          </label>
        </div>
      </div>

      <!-- 画像 -->
      <div>
        <label class="block mb-1 font-medium">問題の画像</label>
        <input type="file" class="border rounded-md p-2 w-full bg-white" name="image"/>
      </div>

      <!-- 補足 -->
      <div>
        <label class="block mb-1 font-medium">補足:</label>
        <input type="text" placeholder="補足を入力してください" class="w-full border rounded-md p-2" name="supplement"/>
      </div>

      <!-- Button -->
      <button type="submit" class="w-full bg-teal-400 text-white font-bold py-2 rounded-lg hover:bg-teal-500">
        作成
      </button>

    </form>
  </main>
</div>

</body>
</html>
