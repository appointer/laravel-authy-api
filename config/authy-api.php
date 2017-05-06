<?php

return [
    'api-key' => env('AUTHY_API_KEY', null),
    'api-base-uri' => 'https://api.authy.com',

    'verification-default-code-length' => 4,    // may be a value between 4 and 10 - default is 4
    'verification-default-locale' => 'en',
];
