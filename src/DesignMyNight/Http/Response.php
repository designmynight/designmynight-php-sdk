<?php

namespace DesignMyNight\Http;

use Psr\Http\Message\ResponseInterface;

class Response
{
    /**
     * @param ResponseInterface $response
     *
     * @return array|string
     */
    public static function getContent(ResponseInterface $response)
    {
        $body = $response->getBody()->__toString();
        
        $content = json_decode($body, true);
        if (JSON_ERROR_NONE === json_last_error()) {
            return $content['payload'];
        }

        return $body;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return null|string
     */
    public static function getApiLimit(ResponseInterface $response)
    {
        $remainingCalls = self::getHeader($response, 'X-RateLimit-Remaining');

        if (null !== $remainingCalls && 1 > $remainingCalls) {
            throw new ApiLimitExceedException($remainingCalls);
        }
        
        return $remainingCalls;
    }
    
    /**
     * Get the value for a single header
     * @param ResponseInterface $response
     * @param string $name
     *
     * @return string|null
     */
    public static function getHeader(ResponseInterface $response, $name)
    {
        $headers = $response->getHeader($name);

        return array_shift($headers);
    }
}
