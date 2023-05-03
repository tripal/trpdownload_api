
This directory should contain any small SQL files, mock classes or PHP files
supporting your tests. For more details specific to each type see the
categories below.

## SQL Files

You will often need small SQL files to setup your testing environment efficiently
or as test files for module functionality which uses files (e.g. test data files
for testing an importer implementation). These files should have very detailed
names. Ideally they will be specific to a given test and the file name will
be prefixed with the test name. For cases where you need to do more generic
setup that will be used for multiple tests, you will likely want to make a
base test class with a specialized setup and helper methods. Any SQL used in
such a base class should be prefixed with the base class name.

ONLY COMMIT VERY SMALL SQL FILES! Large files cause slowness in cloning the
repository EVEN AFTER THEY ARE DELETED and take up space for sites using your
module. Be mindful of this *smiles*.

## Mock Classes

Describing mock classes is beyond the scope of this file. That said, any class
created for use in a test that extends a class outside of the test should be
included in this directory and suffixed with the word `Fake`.

For example, when testing abstract classes, you will need to mock it. This can
sometimes be done using PHPUnit Test Doubles but other times, you will need to
create a non-abstract class in this directory that extends the original for testing
purposes.

The mocked class name should include the original class name. If the
mock class implements methods but each is able to return an obvious empty
default (i.e. a boolean or empty array) then you may be able to use the mock
for multiple tests. Otherwise, you should create a specific mock class for
each test and the mock class name should include the test method name.

## PHP files

This should only be needed in very rare cases! Always discuss with the group
before taking this approach.

In most cases you will add helper methods within your test case
(e.g. when setting up entities, etc to be used in tests).

One valid case for using PHP files is if you need to use a global function
implemented by Drupal in your test or anything you are testing uses a
global function. That is because they are not available in the testing
environment For more recommendations in this specific case, see
https://www.drupal.org/docs/automated-testing/phpunit-in-drupal/unit-testing-more-complicated-drupal-classes#function_calls
