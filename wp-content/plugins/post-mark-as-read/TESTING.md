# Testing Guide for Post Mark as Read Plugin

## Overview

This document describes how to run tests for the Post Mark as Read WordPress plugin.

## Test Framework

We use **PHPUnit 9.5** for unit testing with WordPress test library integration.

## Prerequisites

### Local Testing

1. **PHP 7.4 or higher**
```bash
php --version
```

2. **Composer**
```bash
composer --version
```

3. **WordPress Test Library**
The WordPress test library is included in the `tests/phpunit/includes` directory.

## Installation

Install dependencies using Composer:

```bash
cd wp-content/plugins/post-mark-as-read
composer install
```

## Running Tests

### Run All Tests

```bash
composer test
# or
./vendor/bin/phpunit
```

### Run with Colors

```bash
./vendor/bin/phpunit --colors=always
```

### Run with Coverage Report

```bash
composer test-coverage
```

This will generate an HTML coverage report in the `coverage/` directory.

### Run Specific Test

```bash
./vendor/bin/phpunit --filter test_plugin_activated
```

## Test Structure

```
tests/
├── bootstrap.php                  # Main test bootstrap
└── phpunit/
    ├── bootstrap.php             # PHPUnit bootstrap
    ├── includes/                 # WordPress test library
    └── tests/
        └── post-mark-as-read-access-test.php  # Main test file
```

## Test Coverage

Our test suite covers:

### Core Functionality
- ✅ Plugin activation
- ✅ Admin menu registration
- ✅ Settings registration
- ✅ Shortcode registration

### Reading Status Features
- ✅ Post meta read/unread status
- ✅ Date tracking when marking as read
- ✅ Per-user isolation (users have separate reading data)

### AJAX & API
- ✅ AJAX handler registration
- ✅ REST API routes registration
  - `/pmar/v1/posts/{id}/read` - Get/set read status
  - `/pmar/v1/user/stats` - User statistics
  - `/pmar/v1/user/history` - Reading history

### UI Components
- ✅ Content filter registration
- ✅ Script/style enqueuing
- ✅ Button location options
- ✅ Button customization

### Security & Administration
- ✅ Admin capability checks
- ✅ Export handler registration
- ✅ Import handler registration
- ✅ Uninstall file existence

## Continuous Integration

### CircleCI

Tests run automatically on CircleCI for every push. Configuration: `.circleci/config.yml`

View results: [CircleCI Dashboard](https://circleci.com/gh/algoblend/post-mark-as-read)

### GitHub Actions

Tests also run on GitHub Actions. Configuration: `.github/workflows/php.yml`

View results: Go to the [Actions tab](https://github.com/algoblend/post-mark-as-read/actions) on GitHub

## CI/CD Pipeline

1. **On Push/PR**: Tests automatically run
2. **PHP 7.4 environment** is used
3. **Composer install** runs first
4. **PHPUnit** executes all tests
5. **Results** are reported in the CI dashboard

## Writing New Tests

### Test Class Template

```php
<?php
class My_New_Test extends WP_UnitTestCase {
    
    public function test_my_feature() {
        // Arrange
        $user_id = $this->factory->user->create();
        
        // Act
        $result = my_function($user_id);
        
        // Assert
        $this->assertEquals('expected', $result);
    }
}
```

### Best Practices

1. **Use factories** for creating test data:
   - `$this->factory->post->create()`
   - `$this->factory->user->create()`
   
2. **Follow AAA pattern**:
   - **Arrange**: Set up test data
   - **Act**: Execute the code being tested
   - **Assert**: Verify the results

3. **Clean, descriptive names**:
   - `test_user_can_mark_post_as_read()`
   - `test_admin_menu_is_registered()`

4. **Test one thing** per test method

5. **Use assertions**:
   - `$this->assertTrue()`
   - `$this->assertEquals()`
   - `$this->assertArrayHasKey()`
   - `$this->assertNotEmpty()`

## Troubleshooting

### Issue: "Class WP_UnitTestCase not found"

Make sure the WordPress test library is properly loaded in `bootstrap.php`.

### Issue: "Unable to find bootstrap.php"

Check that the test includes directory exists and contains the WordPress test files.

### Issue: Tests fail in CI but pass locally

Ensure your local PHP version matches the CI environment (PHP 7.4).

## Manual Testing Checklist

While automated tests cover functionality, manual testing should verify:

- [ ] Button appears on single post pages for logged-in users
- [ ] Clicking button marks post as read (visual feedback)
- [ ] Button state persists after page reload
- [ ] Statistics dashboard shows correct numbers
- [ ] Reading history displays posts with dates
- [ ] Bulk actions work correctly
- [ ] Export downloads valid JSON
- [ ] Import restores data correctly
- [ ] REST API endpoints return proper responses
- [ ] Mobile responsive design works
- [ ] Different user roles see their own data

## Test Results

After running tests, you should see output like:

```
PHPUnit 9.5.x by Sebastian Bergmann and contributors.

...............                                                   15 / 15 (100%)

Time: 00:01.234, Memory: 10.00 MB

OK (15 tests, 25 assertions)
```

## Contributing

When adding new features:

1. **Write tests first** (TDD approach recommended)
2. **Ensure all tests pass** before committing
3. **Maintain test coverage** above 70%
4. **Document complex test scenarios**

## Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [WordPress Plugin Unit Tests](https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/)
- [WP_UnitTestCase Reference](https://developer.wordpress.org/reference/classes/wp_unittestcase/)
