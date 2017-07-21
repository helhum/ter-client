TYPO3 Repository Client API/CLI
===============================

TYPO3 Extension Repository (TER) client library and CLI commands

Usage
-----

Each command which can be executed has a corresponding class, for example `NamelessCoder\TYPO3RepositoryClient\Uploader` and a CLI script which acts as a wrapper for said class. The parameters which should be passed to each CLI script *must be the same arguments and in the same order as required by the class' method*.

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
ter-client upload ext_key /path/to/extension -u myusername -p mypassword -m
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
