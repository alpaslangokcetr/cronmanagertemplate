<?php
require_once __DIR__.'/config.php';
session_start();
if(isset($_GET['logout'])){ session_destroy(); header('Location: login.php'); exit; }
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u=$_POST['username']??''; $p=$_POST['password']??'';
  if(isset($USERS[$u]) && $USERS[$u]===$p){ $_SESSION['logged_in']=true; header('Location: tasks.php'); exit; }
  else $error='Kullanıcı adı veya şifre hatalı.';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Giriş - Cron Yönetim</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
  <form method="POST" class="bg-white p-8 rounded shadow-md w-full max-w-sm">
    <h1 class="text-2xl mb-6 text-center">Cron Yönetim Girişi</h1>
    <?php if($error): ?><p class="text-red-500 mb-4"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <label class="block mb-2">Kullanıcı Adı</label><input name="username" type="text" required class="w-full mb-4 p-2 border rounded">
    <label class="block mb-2">Şifre</label><input name="password" type="password" required class="w-full mb-6 p-2 border rounded">
    <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Giriş Yap</button>
  </form>
</body></html>