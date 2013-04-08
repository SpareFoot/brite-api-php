<?php

require 'BriteAPIContact.php';
require 'BriteAPIClient.php';

$config = json_decode(file_get_contents('config.json'));

$client = new BriteAPIContact($config->api_key);
# OR: $client = new BriteAPIContact($config->api_key, array('name' => '123456', 'ip' => '1.1.1.1'));

$client->name = '1234567';
$client->ip = '128.128.128.328';
$client->email = 'fake@wrong.com';
$client->phone = '+123456789';
$client->address = array('zip' => '28210', 'street' => '120 N Cedar', 'unit' => 'Apt 3201');
$client->verify();

print_r($client->response);
echo "valid: ", var_dump($client->is_valid()), "\n";
echo "status: ", $client->status(), "\n";
echo "errors: ", print_r($client->errors()), "\n";
echo "error_codes: ", print_r($client->error_codes()), "\n";