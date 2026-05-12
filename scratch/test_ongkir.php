<?php
require_once 'app/libraries/RajaOngkir.php';
$res = \Altum\Libraries\RajaOngkir::get_provinces();
print_r($res);
