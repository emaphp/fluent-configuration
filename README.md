fluent-configuration
====================

A fluent configuration trait for PHP

[![Build Status](https://travis-ci.org/emaphp/fluent-configuration.svg)](https://travis-ci.org/emaphp/fluent-configuration)

<br/>
###Installation

**composer.json**

```json
{
    "require": {
        "emaphp/fluent-configuration": "1.1.*"
    }
}
```
<br/>
###Usage

<br/>
**Include trait**
```php
namespace Demo;

class ConfigurationContainer {
    use \FluentConfiguration;
}
```

<br/>
**Examples**


```php
use Demo\ConfigurationContainer;

$config = new ConfigurationContainer();

//set option
$config->setOption('test1', 'value1');
$option = $config->getOption('test1'); // 'value1'

//fluent interface
$newInstance = $config->option('test2', 'value2')->option('test3', 'value3');
$newInstance->getOption('test2'); // 'value2'
$config->hasOption('test2'); //false
$config->hasOption('test3'); //false

//merge options
$config = $newInstance->merge(['test3' => 'new_value', 'test4' => 'value4']);
$config->getOption('test1'); // 'value1'
$config->getOption('test3'); // 'new_value'
$config->getOption('test4'); // 'value4'
$newInstance->hasOption('test4'); // false

//discard
$config = $config->discard('test1', 'test3');
$config->hasOption('test1'); // false
$config->hasOption('test3'); // false
$config->hasOption('test4'); // true

//append
$config = new ConfigurationContainer();
$config->setOption('list', 'item1');
$config = $config->append('list', 'item2', 'item3');
$config->getOption('list'); // ['item1', 'item2', 'item3']

//preserve instance
$config = new ConfigurationContainer();
$config->preserveInstance = true;
$config->setOption('test1', 'val1');
$newConf = $config->option('test2', 'val2');
$config->hasOption('test1'); // true
$config->hasOption('test2'); // true
$newConf->getOption('test2') == $config->getOption('test2'); // true
```
<br/>
###License

Released under the MIT license.