CHANGELOG
=========

0.5.0
-----

* dropped support for PHP < 8.1
* All dependencies from php-xapi/* are now loaded from forks at `https://github.com/evolution-job/`

0.4.0
-----

* dropped support for PHP < 5.6 and HHVM

* The `XApi\Repository\Doctrine\Mapping\Object` class was renamed to
  `XApi\Repository\Doctrine\Mapping\StatementObject` for compatibility with
  PHP 7.2.

* made the package compatible with `3.x` releases of `ramsey/uuid`

* allow `2.x` and `3.x` releases of the `php-xapi/model` package too

* added a new `ActivityRepository` class that implements `ActivityRepositoryInterface`.

* The required version of the `php-xapi/repository-api` package has been
  raised to `^0.4`.

* store and return statement's version

* the created and stored properties of the internal mapping `Statement` class
  are now instances of PHP's `\DateTime` class instead of integers representing
  UNIX timestamps.

0.3.0
-----

* Added mapping classes for all statement properties.

* The `MappedStatement` and `MappedVerb` classes have been removed from the
  `php-xapi/model` package. They have been replaced with the new `Statement`
  and `Verb` classes in the `XApi\Repository\Doctrine\Mapping` namespace of
  this package. Consequently, the `MappedStatementRepository` class has been
  removed. It was replaced with a new `StatementRepository` class in the
  `XApi\Repository\Doctrine\Repository\Mapping` namespace.

* The requirements for `php-xapi/model` and `php-xapi/test-fixtures` have
  been bumped to `^1.0` to make use of their stable releases.

* The required version of the `php-xapi/repository-api` package has been
  raised to `^0.3`.

0.2.1
-----

* fixed namespace for base unit test case class `MappedStatementRepositoryTest`

0.2.0
-----

* moved base functional `StatementRepositoryTest` test case class to the
  `XApi\Repository\Doctrine\Test\Functional` namespace

* changed base namespace of all classes from `Xabbuh\XApi\Storage\Doctrine` to
  `XApi\Repository\Doctrine`

* added compatibility for version 0.2 of `php-xapi/repository-api`

0.1.0
-----

First release providing common functions for Doctrine based xAPI learning
record store backends.

This package replaces the `xabbuh/xapi-doctrine-storage` package which is now
deprecated and should no longer be used.
