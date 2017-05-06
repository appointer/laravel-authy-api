<?php

namespace Appointer\AuthyApi\Test;

use Appointer\AuthyApi\AuthyClient;

class PhoneVerificationTest extends TestCase
{
    public function test_start_phone_verification()
    {
        /** @var AuthyClient $authyApi */
        $authyApi = app()->make(AuthyClient::class);

        $phoneNumber = '17633447284';
        $countryCode = '49';

        $this->assertTrue($authyApi->startPhoneVerification($phoneNumber, $countryCode));
    }

    public function test_check_phone_verification()
    {
        /** @var AuthyClient $authyApi */
        $authyApi = app()->make(AuthyClient::class);

        $phoneNumber = '17633447284';
        $countryCode = '49';

        $authyApi->startPhoneVerification($phoneNumber, $countryCode);
        $authyApi->checkPhoneVerification($phoneNumber, $countryCode, 1234);
    }
}