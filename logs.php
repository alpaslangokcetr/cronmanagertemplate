<?php
require_once __DIR__.'/auth.php'; require_once __DIR__.'/inc/header.php';
$filter=$_GET['log_script']??'';$scripts=array_map('basename',glob(SCRIPT_DIR.'*.php'));
if(isset($_GET['clearlog'])){file_put_contents(LOG_FILE,'');header('Location: logs.php');exit;}
?>
<section class="bg-white p-8 rounded shadow-lg">
 <div class="flex items-center justify-between mb-4"><h2 class="text-xl">Log Çıktısı</h2><a href="logs.php?clearlog=1" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" onclick="return confirm('Log temizlensin mi?')">Temizle</a></div>
 <form method="get" class="mb-4"><label class="mr-2">Script Filtresi:</label><select name="log_script" onchange="this.form.submit()" class="p-2 border rounded"><option value="">Tümü</option><?php foreach($scripts as $s): ?><option value="<?php echo htmlspecialchars($s); ?>" <?php echo $filter===$s?'selected':''; ?>><?php echo htmlspecialchars($s); ?></option><?php endforeach; ?></select></form>
 <div class="overflow-y-auto h-96 bg-gray-100 p-4 rounded text-sm"><pre><?php if(file_exists(LOG_FILE)&&filesize(LOG_FILE)>0){$lines=file(LOG_FILE,FILE_IGNORE_NEW_LINES);if($filter)$lines=array_filter($lines,fn($l)=>strpos($l,'['.$filter.']')!==false);$lines=array_reverse(array_slice($lines,-500));echo htmlspecialchars(implode("\n",$lines));}else echo 'Henüz log kaydı yok.'; ?></pre></div>
</section>
<?php require_once __DIR__.'/inc/footer.php'; ?>