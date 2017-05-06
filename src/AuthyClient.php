<?php

namespace Appointer\AuthyApi;

use Appointer\AuthyApi\Exceptions\BadRequestException;
use Appointer\AuthyApi\Exceptions\InternalAuthyApiErrorException;
use Appointer\AuthyApi\Exceptions\InvalidApiKeyException;
use Appointer\AuthyApi\Exceptions\InvalidApiResponseException;
use Appointer\AuthyApi\Exceptions\UnexpectedAuthyApiResponseException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class AuthyClient
{
    /** @var Client */
    private $httpClient;
    /** @var string */
    private $apiKey;

    const STATUS_CODE_200_OK = 200;
    const STATUS_CODE_400_BAD_REQUEST = 400;
    const STATUS_CODE_401_INVALID_API_KEY = 401;
    const STATUS_CODE_503_INTERNAL_AUTHY_ERROR = 503;

    public function __construct(string $apiBaseUri, string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = new Client([
            'base_uri' => $apiBaseUri
        ]);
    }

    public function startPhoneVerification(
        string $phoneNumber,
        string $countryCode,
        string $via = 'sms',
        int $codeLength = null,
        string $locale = null
    ): bool {
        $codeLength = $codeLength ?? config('authy-api.verification-default-code-length');
        $locale = $locale ?? config('authy-api.verification-default-locale');
        $via = in_array($via, ['sms', 'phone']) ? $via : 'sms';

        $response = $this->post('/phones/verification/start', [
            'via' => $via,
            'phone_number' => $phoneNumber,
            'country_code' => $countryCode,
            'code_length' => $codeLength,
            'locale' => $locale,
        ]);

        return $this->processResponse($response);
    }

    public function checkPhoneVerification(
        string $phoneNumber,
        string $countryCode,
        string $verificationCode
    ): bool {
        $response = $this->get('/phones/verification/check', [
            'phone_number' => $phoneNumber,
            'country_code' => $countryCode,
            'verification_code' => $verificationCode,
        ]);

        return $this->processResponse($response);
    }

    protected function processResponse(ResponseInterface $response): bool
    {
        switch ($response->getStatusCode())
        {
            case self::STATUS_CODE_401_INVALID_API_KEY:
                throw new InvalidApiKeyException();
            case self::STATUS_CODE_503_INTERNAL_AUTHY_ERROR:
                throw new InternalAuthyApiErrorException();
        }

        try {
            $bodyContent = $response->getBody();
            $content = \GuzzleHttp\json_decode($bodyContent, true);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidApiResponseException('Invalid response received from authy api.');
        }

        switch ($response->getStatusCode())
        {
            case self::STATUS_CODE_200_OK:
                if ($content['success'] === true) {
                    return true;
                } else {
                    return false;
                }
            case self::STATUS_CODE_400_BAD_REQUEST:
                throw new BadRequestException($content['message']);
            default:
                $errorCode = $content['error_code'];
                switch ($errorCode) {
                    case 60023: // no pending verifications
                        throw new BadRequestException($content['message']);
                }

                throw new UnexpectedAuthyApiResponseException();
        }
    }

    protected function post(string $uri, array $body): ResponseInterface
    {
        return $this->httpClient->post('/protected/json' . $uri, [
            'http_errors' => false,
            'query' => [
                'api_key' => $this->apiKey,
            ],
            'form_params' => $body,
        ]);
    }

    protected function get(string $uri, array $queryParams): ResponseInterface
    {
        $queryParams = array_merge(['api_key' => $this->apiKey], $queryParams);

        return $this->httpClient->get('/protected/json' . $uri, [
            'http_errors' => false,
            'query' => $queryParams,
        ]);
    }
}
