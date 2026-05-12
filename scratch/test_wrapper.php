<?php
require_once __DIR__ . '/app/libraries/RajaOngkir.php';

$provinces = \Altum\Libraries\RajaOngkir::get_provinces();
echo "Provinces: " . count($provinces) . "\n";
if(count($provinces) > 0) print_r($provinces[0]);

$cities = \Altum\Libraries\RajaOngkir::get_cities(5);
echo "\nCities in Prov 5: " . count($cities) . "\n";
if(count($cities) > 0) print_r($cities[0]);

$costs = \Altum\Libraries\RajaOngkir::get_all_costs(501, 114, 1700);
echo "\nCosts:\n";
print_r($costs);
