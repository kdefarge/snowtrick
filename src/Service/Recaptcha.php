<?php

/*
 *
 * (c) Kevin DEFARGE <kdefarge@gmail.com>
 * 
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
     * Checking the recaptcha by contacting the google api
     * https://developers.google.com/recaptcha/docs/verify
     * https://symfony.com/doc/current/http_client.html
     *
     * @return bool
     *
     * @throws DecodingExceptionInterface    When the body cannot be decoded to an array
     * @throws TransportExceptionInterface   When a network error occurs
     * @throws RedirectionExceptionInterface On a 3xx when $throw is true and the "max_redirects" option has been reached
     * @throws ClientExceptionInterface      On a 4xx when $throw is true
     * @throws ServerExceptionInterface      On a 5xx when $throw is true
     * @throws ParameterNotFoundException if the parameter is not defined
     */
    public function verifying(): bool
    {
        $gRecaptchaResponse = $this->requestStack->getCurrentRequest()->request->get('g-recaptcha-response');

        if(!$gRecaptchaResponse)
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
