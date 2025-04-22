<?php
define('CRON_FILE', __DIR__ . '/cron_jobs.json');
define('LOG_FILE', '/var/log/veri_sync.log');
define('SCRIPT_DIR', rtrim(__DIR__, '/') . '/cronlar/');

$USERS = [ 'admin' => 'sifre123' ];

function loadTasks(): array {
    return file_exists(CRON_FILE) ? json_decode(file_get_contents(CRON_FILE), true) : [];
}
function saveTasks(array $tasks): void {
    file_put_contents(CRON_FILE, json_encode($tasks, JSON_PRETTY_PRINT));
}
function describeCron(string $expr): string {
    $p = preg_split('/\s+/', trim($expr));
    if (count($p) !== 5) return 'Geçersiz cron ifadesi';
    [$min,$hour,$dom,$mon,$dow] = $p; $d=[];
    if (preg_match('/^\*\/(\d+)$/', $min,$m))      $d[]="Her {$m[1]} dakikada bir";
    elseif($min==='*')                               $d[]='Her dakika';
    elseif($min!=='0')                               $d[]="{$min}. dakikada";

    if (preg_match('/^\*\/(\d+)$/',$hour,$m))      $d[]="her {$m[1]} saatte bir";
    elseif($hour==='*')                              $d[]='her saat';
    elseif($hour!=='0')                              $d[]="{$hour}. saatte";

    if (preg_match('/^\*\/(\d+)$/',$dom,$m))       $d[]="her {$m[1]} günde bir";
    elseif($dom!=='*' && $dom!=='0')                $d[]="her ayın {$dom}. günü";

    if (preg_match('/^\*\/(\d+)$/',$mon,$m))       $d[]="her {$m[1]} ayda bir";
    elseif($mon!=='*' && $mon!=='0')                $d[]="{$mon}. ayda";

    if ($dow!=='*' && !preg_match('/^\*\/[0-9]+$/',$dow))
        $d[]="haftanın {$dow}. günü";
    return empty($d) ? '    ' : implode(' ', $d) . ' cron çalışacaktır.';
}
function rebuildCrontab(array $tasks): void {
    $c='';
    foreach($tasks as $t){
        if(!empty($t['active'])){
            $log=LOG_FILE;
            $c.="{$t['schedule']} /bin/bash -c 'echo \"[$(date +\\%F\\ \\%T)] [{$t['script']}]\" >> {$log}; /usr/bin/php ".SCRIPT_DIR."{$t['script']} >> {$log} 2>&1'\n";
        }
    }
    $tmp='/tmp/crontab.tmp';
    file_put_contents($tmp,$c);
    exec('crontab '.escapeshellarg($tmp));
    unlink($tmp);
}
?>