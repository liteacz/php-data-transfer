# 🔀 PHP Data Transfer Library

No need to read the details regarding the motivation behind the DTO?

🚀 [Jump right to action!](#with-dto)

We tried to solve this conundrum:

```php
<?php
$user = $client->getUser();

// How do we access user's name?
// Like this?
echo $user['name'];

// Or is it more like this?
echo $user['first_name'];

// I don't remember, it's been while...
// Is it something like this?
echo $user['full_name'];

// Or was it camelCased?!
echo $user['fullName'];
```

And now, with the Data Transfer Library:

```php
<?php

use \Litea\DataTransfer\DataObject;

class User extends DataObject
{
    /**
     * @var string
     * @dto-property first_name
     * So it was snake_case after all...
     */
    public $fistName;

    /**
     * @var string
     * @dto-property LastName
     * And last name is PascalCased? What?
     * It's weird API we're dealing with, indeed.
     * Fortunately we can easily rename it for our purposes.
     */
    public $lastName;
}

$response = $client->getUser();
$user = User::create($response);

// There we go!
// Now, we've got an auto-completion and everything.
echo $user->firstName;
echo $user->lastName;
```

## 📖 Documentation

Bellow you will find the basic usage of this library. For more details see the
documentation located in the [docs](./docs/README.md) directory of this repository.

## Table of contents
- [ℹ️ About](#about)
- [🔌 Installation](#installation)
- [🏁 Basic usage](#basic-usage)

## About

When dealing with an external data, it usually comes in very generic and dynamic
form which is hard to reason about when returning to your code after a while.

This can be solved by so-called Data Transfer Objects (DTOs), which provide
object wrapper with static and hopefully typed properties through which you can
access the underlying values.

These objects get repetitive very quickly and after second or third DTO you reach
for good old CTRL+C, CTRL+V which can be very error prone and hard to maintain.

This is a real-life example of DTO object that helps you to give you better control over the incoming data:

```php
<?php

class InitCallResponse
{
    public const REDIRECT_URL = 'redirectURL';
    public const REQUEST_ID = 'requestID';

    /**
     * @var string
     */
    public $redirectUrl;

    /**
     * @var string
     */
    public $requestId;

    /**
     * InitCallResponse constructor.
     * @param string $redirectUrl
     * @param string $requestId
     */
    public function __construct(string $redirectUrl, string $requestId)
    {
        $this->redirectUrl = $redirectUrl;
        $this->requestId = $requestId;
    }

    /**
     * @param array $data
     * @return self
     * @throws MissingDataTransferPropertyException
     */
    public static function create(array $data): self
    {
        $required = [
            self::REDIRECT_URL,
            self::REQUEST_ID
        ];

        foreach ($required as $field) {
            if (!array_key_exists($field, $data)) {
                throw new MissingDataTransferPropertyException(sprintf(
                    'Mandatory field %s is missing in response data',
                    $field
                ));
            }
        }

        return new self(
            $data[self::REDIRECT_URL],
            $data[self::REQUEST_ID]
        );
    }
}

```

As you can see when you need to maintain dozens of those it becomes very self-evident that
there is a place for improvement. That's why we decided to extract some of the common DTO features
and provide them in the form of this package.

## Installation

The easiest way to install this library is using [composer](https://getcomposer.org):

```bash
composer require liteacz/dto
```

## Basic usage

### Without DTO

```php
<?php

// Include composer's auto-loading features
require_once __DIR__ . '/vendor/autoload.php';

$client = new \GuzzleHttp\Client();
$response = $client->request('GET', 'https://jsonplaceholder.typicode.com/posts/1');
$post = json_decode((string)$response->getBody(), true);

// Now we access the data:
$postId = $post['id'];
$authorId = $post['authorId'];
$title = $post['tilte'];

// And the result:
// Notice: Undefined index: authorId in index.php on line 18
// Notice: Undefined index: tilte in index.php on line 19
// What?!
// We need to refer to the API documentation or run the code and
// debug it to find out what does the response body actually look like.
//
// As you can see, this is very error prone. First we find out, that 
// the field for accessing author's id is actually named `userId`.
// Then we might discover the typo we made and correct `tilte` to `title`.
```

Now imagine you use this service multiple times across your application.
You need to pay extra attention to not to make mistakes or typos regarding the
field namings. Your IDE can't help you here.

### With DTO

First of all we need an object, that will represent the data:
```php
<?php
namespace MyApp\DTO;

class Post extends \Litea\DataTransfer\DataObject
{
    public string $id;
    public int $userId;
}
```

Then we fetch the data as usual:

```php
<?php

// ...
// first lines are the same
// then we get the post:
$postRaw = json_decode((string)$response->getBody(), true);

// Now we wrap it up with the DTO magic:
$post = MyApp\DTO\Post::create($postRaw);

// Now we know the structure we are dealing with:
$postId = $post->id;
$authorId = $post->userId; 

// And then, they lived happily ever after...
// The end
```

That's it. As you can see, in the simplest form using the DTO object is the
matter of defining one class that extends `Litea\DataTransfer\DataObject` and then calling `MyObject::create`.

To learn more about available configuration options see the [documentation](./docs/README.md) or the working [examples](./examples/README.md).