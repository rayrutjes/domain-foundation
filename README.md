
What is this package?
---------------------

Php framework which allows you to roll out an event sourced domain layer with ease.

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/RayRutjes/domain-foundation/master.svg?style=flat-square)](https://travis-ci.org/RayRutjes/domain-foundation)
[![Quality Score](https://img.shields.io/scrutinizer/g/RayRutjes/domain-foundation.svg?style=flat-square)](https://scrutinizer-ci.com/g/RayRutjes/domain-foundation)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/RayRutjes/domain-foundation.svg?style=flat-square)](https://scrutinizer-ci.com/g/RayRutjes/domain-foundation/code-structure)


Installation
------------
```bash
composer require rayrutjes/domain-foundation
```

We will use strict [Semantic Versioning](http://semver.org/) starting from version 1.0.0.

Until we reach that stage, we will allow BC breaks in minor releases.


Requirements
------------

This package is supported on PHP 5.5/5.6, but also PHP-HHVM.

Php xdebug extension is required to run phpspec tests.


Contributing
------------

Feel free to make pull requests, and open issues either to ask questions, suggest implementations and features, or simply to discuss some points.

### Code style

Run the [PHP Coding Standards Fixer](http://cs.sensiolabs.org/) before submitting your code.
```bash
$ php-cs-fixer fix
```

### Run the tests

This library is entirely tested with phpspec, which enforces good architecture.
```
$ vendor/bin/phpspec run
```
**Note that phpspec will need xdebug extension to be enabled in order to produce the code coverage files.**
