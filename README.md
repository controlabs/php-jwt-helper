# php-jwt-helper

[![Build Status](https://travis-ci.org/controlabs/php-jwt-helper.svg?branch=master)](https://travis-ci.org/controlabs/php-jwt-helper)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/controlabs/php-jwt-helper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/controlabs/php-jwt-helper/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/controlabs/php-jwt-helper/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/controlabs/php-jwt-helper/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/controlabs/php-jwt-helper/badges/build.png?b=master)](https://scrutinizer-ci.com/g/controlabs/php-jwt-helper/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/controlabs/php-jwt-helper/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)

[![License](https://poser.pugx.org/controlabs/jwt-helper/license)](https://packagist.org/packages/controlabs/jwt-helper)
[![Latest Stable Version](https://poser.pugx.org/controlabs/jwt-helper/v/stable)](https://packagist.org/packages/controlabs/jwt-helper)
[![Latest Unstable Version](https://poser.pugx.org/controlabs/jwt-helper/v/unstable)](https://packagist.org/packages/controlabs/jwt-helper)
[![composer.lock](https://poser.pugx.org/controlabs/jwt-helper/composerlock)](https://packagist.org/packages/controlabs/jwt-helper)
[![Total Downloads](https://poser.pugx.org/controlabs/jwt-helper/downloads)](https://packagist.org/packages/controlabs/jwt-helper)

Helper to generate JWT using firebase/php-jwt in a simplified way.

## Installation

```
composer require controlabs/jwt-helper
```

## Usage

##### Load private and public keys
```php
define('PRIVATE_KEY', file_get_contents('path-to-private-key.pem'));
define('PUBLIC_KEY', file_get_contents('path-to-public-key.pem'));
```

##### Generating JWT string
```php
use Controlabs\Helper\JWT as JWTHelper;

$iss = 'https://controlabs.github.io'; // issuer
$aud = 'https://controlabs.github.io'; // audition string
$sub = 'controlabs'; // subject
$exp = '+ 10 days'; // 'expiration constraint'

$claims = [ //public claims
    'user_id' => '7b1ded55-67bb-4c42-971d-814e15ba8c05',
    'group_id' => '2065b625-3f0e-4dda-98ed-5d48956f5ee6',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
    'remote_addr' => $_SERVER['REMOTE_ADDR']
];

$helper = new JWTHelper(PRIVATE_KEY, PUBLIC_KEY);

$token = $helper->encode($iss, $aud, $sub, $exp, $claims);

echo json_encode([
    'token' => $token
]);
```

##### Decoding JWT string
```php
use Controlabs\Http\Exception\Unauthorized; // composer require controlabs/http-exceptions (optional)
use Controlabs\Helper\JWT as JWTHelper;

$helper = new JWTHelper(PRIVATE_KEY, PUBLIC_KEY);

try {
    $payload = $helper->decode($_POST['token']);
} catch(ExpiredToken $exception) {
    throw new Unauthorized('Inalid token');
}

// or use $helper->decode($_POST['token'], true) to supress errors

if($payload['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    throw new Unauthorized('User agent is invalid.');
}

echo json_encode([
    'user_id' => $payload['user_id'],
    'group_id' => $payload['group_id']
]);
```

##### Extracting payload without decode validations
```php
use Controlabs\Helper\JWT as JWTHelper;

$helper = new JWTHelper(PRIVATE_KEY, PUBLIC_KEY);

// Use only for logs or specific purposes because it extracts content without validating the token.
$payload = $helper->payload($_POST['token']);

echo json_encode([
    'user_id' => $payload['user_id'],
    'group_id' => $payload['group_id']
]);
```

## License

This software is open source, licensed under the The MIT License (MIT). See [LICENSE](https://github.com/controlabs/php-jwt-helper/blob/master/LICENSE) for details.
