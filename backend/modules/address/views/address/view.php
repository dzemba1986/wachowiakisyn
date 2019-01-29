<?php

/**
 * @var yii\web\View $this
 * @var backend\modules\address\models\Address $address
 * @var common\models\soa\Connection[] $connections
 */

$connections = $address->getConnections()->select('id')->asArray()->all();
$installations = $address->getInstallations()->select('id')->asArray()->all();
$tasks = $address->getTasks()->select('id')->asArray()->all();
$devices = $address->getDevices()->select('id')->asArray()->all();
$histories = $address->getHistories()->select('id')->asArray()->all();
$historyIps = $address->getHistoryIps()->count();

echo 'Połaczenia: ';
foreach ($connections as $connection) {
    echo $connection['id'] . ', ';
}
echo "<br>";
echo 'Instalacje: ';
foreach ($installations as $installation) {
    echo $installation['id'] . ', ';
}
echo "<br>";
echo 'Zadania: ';
foreach ($tasks as $task) {
    echo $task['id'] . ', ';
}
echo "<br>";
echo 'Urządzenia: ';
foreach ($devices as $device) {
    echo $device['id'] . ', ';
}
echo "<br>";
echo 'Historie: ';
foreach ($histories as $history) {
    echo $history['id'] . ', ';
}
echo "<br>";
echo 'Historie IP: ' . $historyIps;

?>