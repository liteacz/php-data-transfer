<?php

namespace Litea\DataTransfer\Examples\CollectionOfDTOs;

require_once __DIR__ . '/../vendor/autoload.php';

class Post extends \Litea\DataTransfer\DataObject
{
    public int $id;
    public int $userId;
    public string $title;
}

$client = new \GuzzleHttp\Client();
$response = $client->request('GET', 'https://jsonplaceholder.typicode.com/posts');
$postsRaw = json_decode((string)$response->getBody(), true);

/** @var Post[] $posts */
$posts = array_map(fn ($post) => Post::create($post), $postsRaw);

foreach ($posts as $post) {
    echo sprintf(
        'Post #%d was created by author %d' . PHP_EOL,
        $post->id,
        $post->userId,
    );
}