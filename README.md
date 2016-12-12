Plumbok
=======

Runtime Code Generator like Lombok for PHP.

[![Build Status](https://travis-ci.org/plumbok/plumbok.svg?branch=master)](https://travis-ci.org/plumbok/plumbok)

---

---

## Features

This library can create objects like:

* **Uri** [RFC3986](https://tools.ietf.org/html/rfc3986) - includes wide abstraction with: **Scheme**, **Authority**, **Path**, **Query**, **Fragment**
* **UriReference** [RFC3986](https://tools.ietf.org/html/rfc3986) - can be resolved with valid **Uri** _(eg. `$resolvedUri = $uriReference->resolve($uri);`)_
* **UriTemplate** [RFC6570](https://tools.ietf.org/html/rfc6570) - produces **Uri** or **UriReference** objects _(depends on template)_


## Installation

Install with Composer

```
composer require madkom/uri
```

## Requirements

This library requires *PHP* in `~7` version.

## Usage

Registering additional autoloader:

```php
require 'vendor/autoload.php';

Plumbok\Autoload::register('Plumbok\\Test');
```

Using annotations in class:

```php
namespace Plumbok\Test;

class ValueObject
{
    /**
     * Holds age
     * @var int
     * @Getter @Setter
     */
    private $age;

    /**
     * @var \DateTime
     * @Getter @Setter
     */
    private $date;

    /**
     * @var int[]
     * @Getter @Setter
     */
    private $days;

    /**
     * @var array
     */
    private $names = [];
}
```

After first run your original code will be little modified with 
additional docblock ennotations (tags) in PhpDocumentor style.

```php
namespace Plumbok\Test;

/**
 * @method int getAge() 
 * @method void setAge(int $age) 
 * @method \DateTime getDate() 
 * @method void setDate(\DateTime $date) 
 * @method int[] getDays() 
 * @method void setDays(int[] $days) 
 */
class ValueObject
{
    /**
     * Holds age
     * @var int
     * @Getter @Setter
     */
    private $age;

    /**
     * @var \DateTime
     * @Getter @Setter
     */
    private $date;

    /**
     * @var int[]
     * @Getter @Setter
     */
    private $days;

    /**
     * @var array
     */
    private $names = [];
}
```

This preprocessing step allows IDE to recognise generated methods from docblock.
Second step is including generated code which looks like:

```php
namespace Plumbok\Test;

class ValueObject
{
    /**
     * Holds age
     * @var int
     * @Getter() @Setter
     */
    private $age;
    /**
     * @var \DateTime
     * @Getter @Setter
     */
    private $date;
    /**
     * @var int[]
     * @Getter @Setter
     */
    private $days;
    /**
     * @var array
     */
    private $names = [];
    /**
     * Retrieves age
     *
     * @return int 
     */
    public function getAge() : int
    {
        return $this->age;
    }
    /**
     * Sets age
     *
     * @param int $age
     * @return void 
     */
    public function setAge(int $age)
    {
        $this->age = $age;
    }
    /**
     * Retrieves date
     *
     * @return \DateTime 
     */
    public function getDate() : \DateTime
    {
        return $this->date;
    }
    /**
     * Sets date
     *
     * @param \DateTime $date
     * @return void 
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }
    /**
     * Retrieves days
     *
     * @return int[] 
     */
    public function getDays() : array
    {
        return $this->days;
    }
    /**
     * Sets days
     *
     * @param int[] $days
     * @return void 
     */
    public function setDays(array $days)
    {
        $this->days = $days;
    }
}
```

## TODO

* [ ] Replace eval with save to file and `include_once`
* [ ] Add generated code caching for performance improvements
* [ ] Add warmup command generating code for deployment
* [x] Implement `@Getter` annotation
* [x] Implement `@Setter` annotation
* [ ] Implement `@ToString` annotation
* [ ] Implement `@AllArgsConstructor`, `@NoArgsConstructor` annotation
* [ ] Implement `@Equal` annotation
* [ ] Implement `@Value`, `@Data` annotations

## License

The MIT License (MIT)

Copyright (c) 2016 Michał Brzuchalski <michal.brzuchalski@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.