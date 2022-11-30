<?php

use Wordless\Abstractions\JsonWebToken;
use Wordless\Helpers\Crypto;
use Wordless\Helpers\Environment;

return [
    JsonWebToken::CONFIG_DEFAULT_CRYPTO => Crypto::JWT_SYMMETRIC_HMAC_SHA256,
    JsonWebToken::CONFIG_SIGN_KEY => Environment::get(JsonWebToken::ENVIRONMENT_SIGN_VARIABLE),
];
