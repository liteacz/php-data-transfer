<?php

namespace Litea\DataTransfer\Examples\BasicUsage;

require_once __DIR__ . '/../vendor/autoload.php';

class Post extends \Litea\DataTransfer\DataObject
{
    public int $id;
    public int $userId;
    public string $title;
}

$client = new \GuzzleHttp\Client();
$response = $client->request('GET', 'https://jsonplaceholder.typicode.com/posts/1');
$postRaw = json_decode((string)$response->getBody(), true);

$post = Post::create($postRaw);

echo sprintf(
    'Post "%s" (#%d) was created by author %d' . PHP_EOL,
    $post->title,
    $post->id,
    $post->userId,
);