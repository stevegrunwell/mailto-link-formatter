# Mailto: Link Formatter

[![Build Status](https://travis-ci.org/stevegrunwell/mailto-link-formatter.svg?branch=develop)](https://travis-ci.org/stevegrunwell/mailto-link-formatter)
[![Packagist](https://img.shields.io/packagist/v/stevegrunwell/mailto-link-formatter.svg)](https://packagist.org/packages/stevegrunwell/mailto-link-formatter)

This package defines a `MailTo` class with a simple API for easily generating [RFC 6068-compliant `mailto:` links](https://www.rfc-editor.org/info/rfc2368) in your markup.

## Installation

You may install the Mailto: Link Formatter package via [Composer](https://getcomposer.org):

```sh
$ composer require stevegrunwell/mailto-link-formatter
```

If you're already including the Composer-generated autoloader in your project, there's nothing more to do. Otherwise, you'll need to include `src/MailTo.php` into your project.

## Usage

The `MailTo` class collects information about the `mailto:` link, then generates the link itself via its `getLink()` method:

```php
$mailto = new MailTo;
$mailto->setRecipients('test@example.com');
$mailto->setHeaders([
    'subject' => 'Hello World!',
    'cc'      => 'foo@example.com',
]);
$mailto->setBody('Some message.');

$mailto->getLink();
# => mailto:test@example.com?subject=Hello%20World!&cc=foo%40example.com&body=Some%20message.
```

### Specifying multiple recipients

If the `mailto:` link should have multiple recipients, they can be set either by passing an array or a comma-separated string to `setRecipients()`:

```php
$mailto = new MailTo;
$mailto->setRecipients([
    'foo@example.com',
    'bar@example.com',
]);

$mailto->getLink();
# => mailto:foo@example.com,bar@example.com
```

The same can be done for headers that might have more than one value, such as `cc` or `bcc`.

### List of headers

While [all headers defined in RCF 822](https://tools.ietf.org/html/rfc822) are considered valid, the most common headers used in `mailto:` links are:

<dl>
    <dt>to</dt>
    <dd>The intended recipient(s) of the message. </dd>
    <dt>subject</dt>
    <dd>The subject line of the message</dd>
    <dt>cc</dt>
    <dd>One or more email address to "carbon-copy" (CC) on the message.</dd>
    <dt>bcc</dt>
    <dd>One or more emails to "blind carbon-copy" (BCC) on the message.</dd>
</dl>

All headers passed to the package will automatically be made lower-cased.

### Setting arguments via the constructor

The setters and getters defined by the `MailTo` class are rather conventional, but the class also accepts the most common arguments via its constructor:

```php
use SteveGrunwell\MailToLinkFormatter\MailTo;

$mailto = new MailTo('test@example.com', [
    'subject' => 'Hello World!',
    'cc'      => 'foo@example.com',
], 'This is the message body.');

// This is equivalent to:
$mailto = new MailTo;
$mailto->setRecipients('test@example.com');
$mailto->setHeaders([
    'subject' => 'Hello World!',
    'cc'      => 'foo@example.com',
]);
$mailto->setBody('This is the message body.');
```

## License

Copyright 2018 Steve Grunwell

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
