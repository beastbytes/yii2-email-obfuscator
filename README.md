# yii2-emailobfuscator
Yii2 Widget to obfuscate email addresses to help prevent harvesting by spam bots.

The widget outputs either a message or an obfuscated version of the address as
the text into the document. If JavaScript is enabled that text is replaced with
a _mailto_ link.

For license information see the [LICENSE](LICENSE.md) file.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist beastbytes/yii2-microformats
```

or add

```json
"beastbytes/yii2-emailobfuscator": "~1.0.0"
```

to the require section of your composer.json.

## Usage

Use this extension in a view.

To output the default message ("This e-mail address is protected to prevent harvesting by spam-bots")
```php
$emailAddress = EmailObfuscator::widget([
    'address' => 'my.address@example.com'
]);

To output the email address as an obfuscated version: "my dot address at example dot com"
```php
$emailAddress = EmailObfuscator::widget([
    'address' => 'my.address@example.com',
    'obfuscators' => [' dot ', ' at ']
]);
```
