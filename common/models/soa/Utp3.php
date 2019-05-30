<?php

/**
 * @property integer $id
 * @property integer $address_id
 * @property integer $wire_length
 * @property string $wire_date
 * @property string $socket_date
 * @property string $wire_user
 * @property string $socket_user
 * @property integer $type_id
 * @property string $invoice_date
 * @property string $status
 * @property array $connectionTypeIds
 */

namespace common\models\soa;

class Utp3 extends Installation {
	
    const TYPE = 2;
    const TYPENAME = 'Kabel telefoniczny';
}