<?php

namespace Bitbucket;

use Bitbucket\Api\PullRequest;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use function GuzzleHttp\uri_template;
use Psr\Http\Message\ResponseInterface;

class Api
{
    /**
     * Http client instance
     *
     * @var Client
     */
    protected $client;

    /**
     * Bitbucket api endpoint
     */
    const BITBUCKET_API_URI = 'https://api.bitbucket.org/1.0/repositories/{account}/{repository}/';

    /**
     * Api constructor.
     *
     * @param string $username   The bitbucket username
     * @param string $password   The bitbucket password
     * @param string $account    The bitbucket account name
     * @param string $repository The bitbucket repository name
     */
    public function __construct(string $username, string $password, string $account, string $repository)
    {
        $this->client = new Client([
            'base_uri' => uri_template(self::BITBUCKET_API_URI, [
                'account'    => $account,
                'repository' => $repository,
            ]),
            'auth'     => [$username, $password],
        ]);
    }

    /**
     * Get pull request api object
     *
     * @param int $id The pull request ID
     *
     * @return PullRequest
     */
    public function pullRequest(int $id) : PullRequest
    {
        return new PullRequest($this, $id);
    }

    /**
     * Post json data async
     *
     * @param string $uri     The resource uri
     * @param mixed  $data    The data
     * @param array  $options The request options
     *
     * @return PromiseInterface
     */
    public function postJsonAsync(string $uri, $data, array $options = []) : PromiseInterface
    {
        return $this->client->postAsync(uri_template($uri, $options), array_merge(['json' => $data], $options));
    }

    /**
     * Post json data
     *
     * @param string $uri     The resource uri
     * @param mixed  $data    The data
     * @param array  $options The request options
     *
     * @return ResponseInterface
     */
    public function postJson(string $uri, $data, array $options = []) : ResponseInterface
    {
        return $this->client->post(uri_template($uri, $options), array_merge(['json' => $data], $options));
    }

    /**
     * Get data from
     *
     * @param string $uri     The resource uri
     * @param array  $options The request options
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function getJson(string $uri, array $options = [])
    {
        return \GuzzleHttp\json_decode(
            $this->client->get(uri_template($uri, $options), $options)->getBody()->getContents(),
            true
        );
    }

    /**
     * Delete resource async
     *
     * @param string $uri     The resource uri
     * @param array  $options The request options
     *
     * @return PromiseInterface
     */
    public function deleteAsync(string $uri, array $options = []) : PromiseInterface
    {
        return $this->client->deleteAsync(uri_template($uri, $options), $options);
    }

    /**
     * Get http client
     *
     * @return ClientInterface
     */
    public function getClient(): ClientInterface
    {
        return $this->client;
    }
}