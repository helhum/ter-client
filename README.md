# TER Client

TYPO3 Extension Repository (TER) client library and Symfony Console commands.
This has been built on the shoulders of [namelesscoder/typo3-repository-client](https://packagist.org/packages/namelesscoder/typo3-repository-client).
Thanks a lot Claus Due for the idea and the foundation.

## Installation

Use composer to install the TER Client: `composer require helhum/ter-client`

## Usage

Each command which can be executed has a corresponding class, for example `Helhum\TerClient\Uploader` and a CLI script which acts as a wrapper for said class. The parameters which should be passed to each CLI script *must be the same arguments and in the same order as required by the class' method*.

### Uploader

As component:

```php
$uploadPacker = new ExtensionUploadPacker();
$connection = Connection::create($wsdUrl);
$result = $connection->upload(
    new UsernamePasswordCredentials($username, $password),
    $uploadPacker->pack($extensionKey, $directory, $comment)
);
```

And as CLI command:

```bash
ter-client upload ext_key /path/to/extension -u myusername -p mypassword -m "Upload Comment"
```

### Version Deleter (admins only)

As component:

```php
$deleter = new Deleter(Connection::create($wsdUrl));
$result = $deleter->deleteExtensionVersion($extensionKey, $version, $username, $password);
```

And as CLI command:

```bash
ter-client remove-version extensionkey 1.2.3 -u myusername -p mypassword
```
