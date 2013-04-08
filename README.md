The official BriteVerify API Client for PHP

# Usage

```php
require 'BriteAPIContact.php';
require 'BriteAPIClient.php';

# Option #1
$client = new BriteAPIContact($api_key);

# Option #2
$client = new BriteAPIContact($config->api_key, array('name' => '123456', 'ip' => '1.1.1.1'));

# Set/update fields
$client->name = '1234567';
$client->ip = '128.128.128.328';
$client->email = 'fake@wrong.com';
$client->phone = '+123456789';
$client->address = array('zip' => '28210', 'street' => '120 N Cedar', 'unit' => 'Apt 3201');

# Verify input data
$client->verify();

# Output
$client->is_valid() # false
$client->status() # "invalid"
$client->errors() # array('name' => 'Contains non alphabetic', 'phone' => 'Invalid format' ...)
$client->error_codes() # array('invalid_format','address_invalid',...)

$client->response['email']
# array(
#   'address' => 'fake@wrong.com',
#   'account' => 'fake',
#   'domain' => 'wrong.com',
#   'status' => 'invalid',
#   'error_code' => 'email_account_invalid',
#   'error' => 'Email account invalid',
#   'disposable' => '',
#   'role_address' => '',
#   'duration' => 0.049145096
# )

```

# Documentation

* https://www.briteverify.com/
* https://github.com/BriteVerify/BriteCode