<?php

/**
 * Recaptcha verification
 *
 * A service to verify the Recaptcha
 *
 * @author     Kevin DEFARGE <kdefarge@gmail.com>
 * @link       https://developers.google.com/recaptcha/docs/verify
 * @link       https://symfony.com/doc/current/http_client.html
 */

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Recaptcha
{
    private $client;
    private $requestStack;
    private $params;

    public function __construct(HttpClientInterface $client, RequestStack $requestStack, ParameterBagInterface $params)
    {
        $this->client = $client;
        $this->requestStack = $requestStack;
        $this->params = $params;
    }

    /**
     * Recaptcha verification
     * 
     * Retrieve the information sent by the customer and contact the google 
     * API to check if the customer has validated the Recaptcha
     *
     * @return bool
     */
    public function verifying(): bool
    {
        $gRecaptchaResponse = $this->requestStack->getCurrentRequest()->request->get('g-recaptcha-response');

        if (!$gRecaptchaResponse)
            return false;

        $secretKey = $this->params->get('recaptach_key');

        /**
         * POST Parameter	Description
         * secret	        Required. The shared key between your site and reCAPTCHA.
         * response	        Required. The user response token provided by the reCAPTCHA client-side integration on your site.
         * remoteip	        Optional. The user's IP address.
         */

        $formData = new FormDataPart([
            ['secret' => $secretKey],
            ['response' => $gRecaptchaResponse],
        ]);

        $response = $this->client->request(
            'POST',
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'headers' => $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToIterable(),
            ]
        );

        if ($response->getStatusCode() !== 200)
            return false;

        $content = $response->toArray();

        /**
         * "success": true|false,
         * "challenge_ts": timestamp,  - timestamp of the challenge load (ISO format yyyy-MM-dd'T'HH:mm:ssZZ)
         * "hostname": string,         - the hostname of the site where the reCAPTCHA was solved
         * "error-codes": [...]        - optional
         */

        if (isset($content['success']))
            return $content['success'];

        return false;
    }
}
