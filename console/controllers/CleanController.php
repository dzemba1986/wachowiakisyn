<?php

namespace console\controllers;

use common\models\address\Address;
use yii\console\Controller;


class CleanController extends Controller {

    function actionAddress() {
        
        $counter = 0;
        $addresses = Address::find()->select('id')->all();
        foreach ($addresses as $address) {
            $count = 0;
            $count = $address->getConnections()->count();
            if ($count > 0) continue;
            $count += $address->getInstallations()->count();
            if ($count > 0) continue;
            $count += $address->getTasks()->count();
            if ($count > 0) continue;
            $count += $address->getDevices()->count();
            if ($count > 0) continue;
            $count += $address->getHistories()->count();
            if ($count > 0) continue;
            $count += $address->getHistoryIps()->count();
            if ($count > 0) continue;
            $address->delete();
            $counter++;
            echo '.';
        }
        echo "\nUsunięto $counter adresów.";
    }
}
?>