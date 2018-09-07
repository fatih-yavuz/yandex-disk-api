# Yandex Disk Api Client

This library is built to deal with Yandex Disk API.

### Installation

```sh
composer require siyahmadde/yandex-disk-api
```



# Usage
First you need to create an alias
```php
use Siyahmadde\Disk;
```
  - Get an Id from Yandex then use that id to create an object from the class
```php
$disk = new Disk('Your Id');
```
 - Now you need to obtain a token from Yandex. To get the url where you can obtain the token call getLoginToken method.
```php
$disk->getLoginToken();
```
 - The method call above, will return the url where you can get the token. The url will redirect you to the call back url specified while creating Yandex Disk API app. In your call back file you can call the following method. It will return the token.
```php
Disk::handleCallback();
```
 - Having obtained to token, you need to set it.
```php
$disk->setToken('your_token');
```

 - call this if you want PHP objects returned instead of default JSON strings
```php
$disk->setReturnDecoded();
```

 - Now, you are ready to use all methods. For example:
```php
$disk->aboutDisk();
```


You can also:
  - Upload files to your Yandex disk.
```php
$disk->uploadFile('your_file');
```
  - Download files from your Yandex disk
```php
$disk->downloadOwnFile('file_path');
```
  - Download files to your Yandex disk.
```php
$disk->downloadOthersFile('url','whatever_you_want_to_name_it');
```
  - Save public files from Yandex disk to your Yandex disk.
```php
$disk->saveToDisk('url');
```
 -- And many more, see [offical documentation]
 
 [offical documentation]: https://tech.yandex.com/disk/api/concepts/about-docpage/

