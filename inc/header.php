<?php require_once __DIR__.'/../config.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cron Yönetim Paneli</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="container mx-auto bg-gray-50 p-6">
  <header class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Cron Yönetim Paneli</h1>
    <nav class="space-x-4 text-sm">
      <a href="tasks.php" class="text-blue-600 hover:underline">Görevler</a>
      <a href="logs.php"  class="text-blue-600 hover:underline">Loglar</a>
      <a href="login.php?logout=1" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Çıkış</a>
    </nav>
  </header>
  <main>
