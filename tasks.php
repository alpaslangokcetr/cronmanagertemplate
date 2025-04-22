<?php
require_once __DIR__.'/auth.php'; require_once __DIR__.'/inc/header.php';
$tasks=loadTasks(); $scripts=array_map('basename',glob(SCRIPT_DIR.'*.php'));
$formError='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $script=$_POST['script'];$min=trim($_POST['min']??'');$hour=trim($_POST['hour']??'');$day=trim($_POST['day']??'');$mon=trim($_POST['mon']??'');$dow=trim($_POST['dow']??'');
  $schedule="$min $hour $day $mon $dow";
  if(!preg_match('/^(\S+\s+){4}\S+$/',$schedule)) $formError='Geçersiz cron ifadesi.';
  if(!$formError){
    $active=isset($_POST['active']); $id=$_POST['id']!==''?(int)$_POST['id']:null;
    if($id!==null){foreach($tasks as &$t) if($t['id']===$id){$t['script']=$script;$t['schedule']=$schedule;$t['active']=$active;break;} unset($t);} else{$max=empty($tasks)?0:max(array_column($tasks,'id'));$tasks[]=['id'=>$max+1,'script'=>$script,'schedule'=>$schedule,'active'=>$active];}
    saveTasks($tasks); rebuildCrontab($tasks); header('Location: tasks.php'); exit; }
}
if(isset($_GET['delete'])){$del=(int)$_GET['delete'];$tasks=array_values(array_filter($tasks,fn($t)=>$t['id']!==$del));saveTasks($tasks);rebuildCrontab($tasks);header('Location: tasks.php');exit;}
$edit=null;if(isset($_GET['edit'])){$id=(int)$_GET['edit'];foreach($tasks as $t) if($t['id']===$id){$edit=$t;break;}}
$perPage=20;$page=max(1,(int)($_GET['page']??1));$total=max(1,ceil(count($tasks)/$perPage));$tasksPaged=array_slice($tasks,($page-1)*$perPage,$perPage);
?>
<section class="mb-8 bg-white p-8 rounded shadow-lg">
 <h2 class="text-xl mb-4">Görev Ekle / Güncelle</h2><?php if($formError): ?><p class="text-red-500 mb-4"><?php echo htmlspecialchars($formError); ?></p><?php endif; ?>
 <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
  <input type="hidden" name="id" value="<?php echo $edit['id']??''; ?>">
  <div><label class="block mb-1">Script</label><select name="script" class="w-full p-2 border rounded" required><?php foreach($scripts as $s): ?><option value="<?php echo htmlspecialchars($s); ?>" <?php echo ($edit&&$edit['script']===$s)?'selected':''; ?>><?php echo htmlspecialchars($s); ?></option><?php endforeach; ?></select></div>
  <?php foreach(['min'=>'Dakika','hour'=>'Saat','day'=>'Gün','mon'=>'Ay','dow'=>'Hafta Günü'] as $f=>$lbl): ?><div><label class="block mb-1"><?php echo $lbl; ?></label><input name="<?php echo $f; ?>" type="text" class="w-full p-2 border rounded" value="<?php echo htmlspecialchars(explode(' ', $edit['schedule']??'     ')[array_search($f,['min','hour','day','mon','dow'])]??''); ?>"></div><?php endforeach; ?>
  <div class="flex items-center"><input type="checkbox" name="active" class="mr-2" <?php echo ($edit&&$edit['active'])?'checked':''; ?>>Aktif</div>
  <div class="flex items-end"><button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600" type="submit"><?php echo $edit?'Güncelle':'Ekle'; ?></button></div>
 </form>
</section>
<section class="mb-8 bg-white p-8 rounded shadow-lg">
 <h2 class="text-xl mb-4">Mevcut Görevler</h2>
 <table class="min-w-full bg-white text-sm"><thead class="bg-gray-200"><tr><th class="p-2 text-left">Script</th><th class="p-2">Zamanlama</th><th class="p-2">Aktif</th><th class="p-2">İşlemler</th><th class="p-2">Açıklama</th></tr></thead><tbody><?php foreach($tasksPaged as $t): ?><tr class="border-t"><td class="p-2"><?php echo htmlspecialchars($t['script']); ?></td><td class="p-2"><?php echo htmlspecialchars($t['schedule']); ?></td><td class="p-2"><?php echo $t['active']?'Evet':'Hayır'; ?></td><td class="p-2 space-x-2"><a href="tasks.php?edit=<?php echo $t['id']; ?>" class="text-blue-500 hover:underline">Düzenle</a><a href="tasks.php?delete=<?php echo $t['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('Silinsin mi?')">Sil</a></td><td class="p-2"><?php echo htmlspecialchars(describeCron($t['schedule'])); ?></td></tr><?php endforeach; ?></tbody></table>
 <div class="mt-4 flex justify-center space-x-2"><?php for($i=1;$i<=$total;$i++): ?><a href="tasks.php?page=<?php echo $i; ?>" class="px-3 py-1 rounded <?php echo $i==$page?'bg-blue-600 text-white':'bg-gray-200'; ?>"><?php echo $i; ?></a><?php endfor; ?></div>
</section>
<?php require_once __DIR__.'/inc/footer.php'; ?>
