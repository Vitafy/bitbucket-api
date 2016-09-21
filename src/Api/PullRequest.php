<?php

namespace Bitbucket\Api;

use Bitbucket\Api;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\Promise;

class PullRequest
{
    /**
     * Api instance
     *
     * @var Api
     */
    protected $api;

    /**
     * Pull request ID
     *
     * @var int
     */
    protected $id;

    /**
     * PullRequest constructor.
     *
     * @param Api $api The API instance
     * @param int $id  The pull request id
     */
    public function __construct(Api $api, int $id)
    {
        $this->api = $api;
        $this->id = $id;
    }

    /**
     * Get all comments for current pull request
     *
     * @return array
     * @throws \RuntimeException
     */
    public function allComments() : array
    {
        return $this->api->getJson('pullrequests/{id}/comments', ['id' => $this->id]);
    }

    /**
     * Get all user comments
     *
     * @param string $username The username
     *
     * @return array
     * @throws \RuntimeException
     */
    public function userComments(string $username) : array
    {
        return array_filter($this->allComments(), function (array $comment) use ($username) {
            return $comment['author_info']['username'] === $username;
        });
    }

    /**
     * Remove comments
     *
     * @param array $ids
     *
     * @return mixed
     */
    public function deleteComments(array $ids = [])
    {
        $promises = array_map(\Closure::bind(function (int $commentId) {
            return $this->api->deleteAsync('pullrequests/{id}/comments/{commentId}', [
                'id'        => $this->id,
                'commentId' => $commentId,
            ]);
        }, $this), $ids);

        return \GuzzleHttp\Promise\settle($promises)->wait();
    }

    /**
     * Publish comments
     *
     * @param array $comments The comments
     *
     * @return mixed
     */
    public function publishComments(array $comments = [])
    {
        $promises = array_map(\Closure::bind(function (array $comment) {
            return $this->api->postJsonAsync('pullrequests/{id}/comments', $comment, ['id' => $this->id]);
        }, $this), $comments);

        return \GuzzleHttp\Promise\settle($promises)->wait();
    }
}