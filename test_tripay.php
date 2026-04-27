<?php
require 'index.php';

$channels = \Altum\Controllers\Tripay::get_channels();
print_r($channels);
