<?php

namespace Litea\DataTransfer\Examples\NestedDTOs;

use Litea\DataTransfer\DataObject;

require_once __DIR__ . '/../vendor/autoload.php';

class User extends DataObject
{
    public int $id;
    public string $username;

    /**
     * @var \Litea\DataTransfer\Examples\NestedDTOs\Post[]
     * PHP does not support generic types,
     * so we need to improve the type hint annotation
     * via DocComment.
     *
     * Note that we MUST use FQCN here.
     */
    public array $posts;
}

class Post extends DataObject
{
    public int $id;
    public int $userId;
    public string $title;
}

// Imagine having resource User, that contains list of their posts
$rawData = [
    [
        'id' => 1,
        'username' => 'John Doe',
        'posts' => [
            [
                'id' => 42,
                'userId' => 1,
                'title' => 'Lorem ipsum'
            ],

            [
                'id' => 43,
                'userId' => 1,
                'title' => 'Dolor sit amet'
            ],
        ]
    ],
    [
        'id' => 2,
        'username' => 'Jane Doe',
        'posts' => [
            [
                'id' => 7,
                'userId' => 2,
                'title' => 'Dolor sit amet'
            ],
            [
                'id' => 8,
                'userId' => 2,
                'title' => 'Lorem ipsums'
            ]
        ]
    ]
];

/** @var User[] $users */
$users = array_map(fn ($user) => User::create($user), $rawData);

foreach ($users as $user) {
    echo "Author $user->username (#$user->id)" . PHP_EOL;
    echo "Posts by the author:" . PHP_EOL;

    foreach ($user->posts as $post) {
        echo "-- #$post->id: $post->title" . PHP_EOL;
    }

    echo str_repeat('-', 20) . PHP_EOL;
}
