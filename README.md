# PHPUnit Code Assertions

<p>
    <a href="https://github.com/jpeters8889/phpunit-code-assertions/actions"><img src="https://github.com/jpeters8889/phpunit-code-assertions/actions/workflows/laravel-pint.yml/badge.svg" alt="Build Status"></a>
    <a href="https://github.com/jpeters8889/phpunit-code-assertions/actions"><img src="https://github.com/jpeters8889/phpunit-code-assertions/actions/workflows/phpstan.yml/badge.svg" alt="Build Status"></a>
    <a href="https://github.com/jpeters8889/phpunit-code-assertions/actions"><img src="https://github.com/jpeters8889/phpunit-code-assertions/actions/workflows/phpunit.yml/badge.svg" alt="Build Status"></a>
</p>

**PHPUnit Code Assertions** is a set of code assertions to use in a PHPUnit test suite, heavily inspired by PestPHP Arch testing, but in a PHPUnit code style.

Simply extend the `CodeAssertionsTestCase` in your test file and you're ready to go!

```php
<?php

class CodeQuality extends CodeAssertionsTestCase
{
    #[Test]
    public function the_code_doesnt_have_any_forbidden_functions()
    {
        $this->assertCodeIn('app')->doesNotUseFunctions(['dd', 'dump']);
    }
    
    #[Test]
    public function all_controllers_follow_the_same_pattern()
    {
        $this->assertClassesIn('app/Http/Controllers')
            ->extend(Controller::class)
            ->areOnlyInvokable()
            ->areFinal()
            ->haveSuffix('Controller')
    }
}
```

## Installation

> **Requires [PHP 8.3+](https://php.net/releases/)**.

⚡️ Get started by requiring the package using [Composer](https://getcomposer.org):

```bash
composer require jpeters8889/phpunit-code-assertions:^0.1
```

## Usage

`CodeAssertionsTestCase` exposes two methods to chain your tests onto, `assertCodeIn` and `assertClassesIn`

### assertCodeIn

This method will run any of the chained assertables against all code in the given directory.

At the end of your assertable chain, all of the assertions will be executed against all found files in the given directory, alternately, you can also call `executeAssertions()` to manually trigger the assertions.

#### Available Methods

`usesFunctions(array $functions)` - Asserts that all code in the directory uses each of these functions

`doesNotUseFunctions(array $functions)` - Asserts that all the code in the directory does not use any of these functions.

`toUseStrictTypes()` - Asserts that all the code in the directory has strict types declared.

`toNotUseStrictTypes()` - Asserts that all the code in the directory does not have strict types declared.

`hasStrictTypes()` - Alias for `toUseStrictTypes()`

`usesStrictTypes()` - Alias for `toUseStrictTypes()`
 
### assertClassesIn

This method will run any of the chained assertables against all classes/traits/interfaces etc in the given directory

This method also supports all assertables available in `assertCodeIn`, in addition to class-specific assertions.

#### Available Methods

##### Interfaces

`areContracts()` - Alias for areInterfaces()

`areNotContracts()` - Alias for areNotInterfaces()

`areInterfaces()` - Asserts that all files in the given directory are interfaces.

`areNotInterfaces()` - Asserts that files in the given directory are not interfaces.

`implement(string $interface)` - Alias for toImplement()

`implements(string $interface)` - Alias for toImplement()

`toImplement(string $interface)` - Asserts that all files in the given directory implement the given interface.

`toNotImplement(string $interface)` - Asserts that files in the given directory do not implement the given interface.

##### Traits

`areTraits()` - Asserts that all files in the given directory are traits.

`areNotTraits()` - Asserts that files in the given directory are not traits.

`toUse(string $trait)` - Asserts that all files in the given directory use the given trait.

`toNotUse(string $trait)` - Asserts that files in the given directory do not use the given trait.

`uses(string $trait)` - Alias for toUse()

##### Classes

`areClasses()` - Asserts that all files in the given directory are classes.

`areNotClasses()` - Asserts that files in the given directory are not classes.

###### Extending

`toExtend(string $class)` - Asserts that all files in the given directory extend the given class.

`toNotExtend(string $class)` - Asserts that files in the given directory do not extend the given class.

`extend(string $class)` - Alias for toExtend()

`extends(string $class)` - Alias for toExtend()

###### Abstract

`toBeAbstract()` - Asserts that all files in the given directory are abstract classes.

`toNotBeAbstract()` - Asserts that files in the given directory are not abstract classes.

`isAbstract()` - Alias for toBeAbstract()

###### Final

`toBeFinal()` - Asserts that all files in the given directory are final classes.

`toNotBeFinal()` - Asserts that files in the given directory are not final classes.

`isFinal()` - Alias for toBeFinal()

`areFinal()` - Alias for toBeFinal()

###### Read Only

`toBeReadOnly()` - Asserts that all files in the given directory are read only.

`toNotBeReadOnly()` - Asserts that files in the given directory are not read only.

`areReadOnly()` - Alias for toBeReadOnly()

##### Generic

###### Methods

`toHaveMethods(array $methods)` - Asserts that all files in the given directory have the given methods.

`toNotHaveMethods(array $methods)` - Asserts that files in the given directory do not have any of the given methods.

`toOnlyHaveMethod()` - Asserts that all files in the given directory only has the given method.

`toNotOnlyHaveMethod()` - Asserts that files in the given directory does not only have the given method.

`hasMethods(array $methods)` - Alias for toHaveMethods()

`toHaveMethod(string $method)` - Wrapper for toHaveMethods but with one method.

`hasMethod(string $method)` - Wrapper for toHaveMethods but with one method.

`toNotHaveMethod(string $method)` - Wrapper for toNotHaveMethods but with one method.

###### Invokable

`toBeInokable()` - Wrapper for toHaveMethods but passing \['__invoke'\].

`toNotBeInokable()` - Wrapper for toNotHaveMethods but passing \['__invoke'\].

`isInvokable()` - Wrapper for toHaveMethods but passing \['__invoke'\].

`areInvokable()` - Wrapper for toHaveMethods but passing \['__invoke'\].

`toBeOnlyInvokable()` - Wrapper for toOnlyHaveMethod but passing \['__invoke'\].

`toNotBeOnlyInvokable()` - Wrapper for toNotOnlyHaveMethod but passing \['__invoke'\].

###### Suffix

`toHaveSuffix(string $suffix)` - Asserts that all files in the given directory are have the given suffix.

`toNotHaveSuffix(string $suffix)` - Asserts that files in the given directory do not have the given suffix.

`hasSuffix()` - Alias for toHaveSuffix()

`haveSuffix()` - Alias for toHaveSuffix()

### Excluding Files from an assertion

All of the assertable methods above can have `except(string|array $fqns)` chained onto them to exclude the given class or classes in that assertion, for example

```php
<?php

class CodeQuality extends CodeAssertionsTestCase
{
    #[Test]
    public function all_controllers_follow_the_same_pattern()
    {
        $this->assertClassesIn('app/Http/Controllers')
            ->extend(App\Http\Controllers\BaseController::class)->except(App\Http\Controllers\BaseController::class)
    }
}
```

In the above example, all classes in `app/Http/Controllers` _must_ extend the BaseController class, _except_ for the BaseController itself.

### Real life example

PHPUnit Code Assertions is used in its own test suite, see `tests/Code/CodeTest.php`.

## License

**PHPUnit Code Assertions** was created by **[Jamie Peters](https://www.jamie-peters.co.uk)** under the **[MIT license](https://opensource.org/licenses/MIT)**.
