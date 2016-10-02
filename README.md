# Mocking MySQLi in PHPUnit

**TL;DR:** If you need to mock public properties as well, wrap MySQLi and mock
that wrapper instead. Check the code in the master branch for how to achieve
that. PHP 7 because it's da future yo.

**Long version:**

Recently I was given a test task where a class took a container object as its
dependency. I thought I was being clever switching it to mysqli_result so that I
could read in the results one by one and yield objects from the data on the fly
using generators. However, mocking mysqli objects turned out to be a bit more
difficult than I expected.

If your code uses only methods and not any public properties of mysqli objects,
mocking the objects works just fine. But, if you try to set any of the known
public properties for the mocks, e.g. ```$field_count``` or ```$num_rows``` in
```mysqli_result``` object, you'll run into trouble. This is because those are
by design read-only properties.

I tried assigning the ```mysqli_result::$num_row``` property both via the
Reflection API and by adding my own mocked magic-method getter to the mocked
class, with little success to either method. I didn't take a deep look into why
exactly the latter method fails but if you're using MySQLi extension and need to
mock it for unit tests, I might suggest creating a wrapper for MySQLi and
mocking the wrapper instead as an easier alternative.

The master branch of this repository uses a wrapper as dependency and mocks that
in the unit tests. It's not a full wrapper around MySQLi but it rather only
mocks the properties that the code uses, here ```fetch_array``` method and
```num_rows``` property. You'll have to create your own wrapper and mock for it
but hopefully this'll give you an idea how to do it.

## Running the tests

Requires PHP 7.

- Clone the repository
- [Download Composer](https://getcomposer.org/download/) into the repository
  directory.
- Run the following in the repository directory:

```sh
./composer.phar install
vendor/bin/phpunit tests
```

## Errors in mocking mysqli when properties are used

You can check out branch ```with-mysqli-dependency``` and modify the code in
*tests/app/MysqliMockTrait.php* and *src/services/UserService.php* as instructed
in the code's comments to reproduce the errors down below. Better yet if you can
fix them!

### With reflections

```php
$reflection = new \ReflectionClass($mysqliResult);
$numRowsProperty = $reflection->getProperty('num_rows');
$numRowsProperty->setAccessible(true);
$numRowsProperty->setValue($mysqliResult, count($results));
```

**Result:**

```PHP Fatal error: ReflectionProperty::setValue(): Cannot write property in /mysqli_mockup/tests/app/MysqliMockTrait.php```

### With __get override

```php
$mysqliResult
    ->expects($this->any())
    ->method('__get')
    ->with($this->equalTo('num_rows'))
    ->will($this->returnValue(count($results)));
```

**Result:**

```PHP Fatal error: Method Mock_mysqli_result_97bb34c9::__get() must take exactly 1 argument in /mysqli_mockup/vendor/phpunit/phpunit-mock-objects/src/Framework/MockObject/Generator.php(345) : eval()'d code```
