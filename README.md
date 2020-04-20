# PHP Data Transfer Library

We tried to solve this conundrum:

```php
<?php
$user = $client->getUser();

// How do you access user's name?
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

## Documentation

Bellow you will find the basic usage of this library. For more details see the
documentation located in the [docs](./docs/README.md) directory of this repository.

## Table of contents
- [About](#about)
- [Installation](#installation)
- [Basic usage](#basic-usage)

## About

When dealing with an external data, it usually comes in very generic and dynamic
form which is hard to reason about when returning to your code after a while.

This can be solved by so-called Data Transfer Objects (DTOs), which provide
object wrapper with static and hopefully typed properties through which you can
access the underlying values.

These objects get repetitive very quickly and after second or third DTO you reach
for good old CTRL+C, CTRL+V which can be very error prone.

This is a real-life example of DTO object that helps you to give you better control over the incoming data:

```php
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

TODO

## Basic usage

TODO