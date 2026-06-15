# How to Check CircleCI Build Status

This guide explains how to check if your CircleCI tests are passing for the Post Mark as Read plugin.

## Quick Check - Badges

The README.md file includes badges that show the current build status:

- ✅ **Green badge** = Tests are passing
- ❌ **Red badge** = Tests are failing

## Detailed CircleCI Dashboard

### 1. Access CircleCI Dashboard

Visit: [https://app.circleci.com/pipelines/github/algoblend/post-mark-as-read](https://app.circleci.com/pipelines/github/algoblend/post-mark-as-read)

### 2. View Pipeline Status

You'll see a list of recent builds with:
- **Branch name** (e.g., `cursor/enhanced-features-e338`, `main`)
- **Commit message**
- **Status**: 
  - 🟢 Success (green)
  - 🔴 Failed (red)
  - 🟡 Running (yellow)
  - ⚪ Queued (gray)

### 3. Click on a Build

Click any build to see:
- **Duration**: How long tests took to run
- **Jobs**: Click "build" to see details
- **Steps**: Each step of the build process
  - Checkout code
  - Install dependencies (Composer)
  - Run PHPUnit tests

### 4. View Test Results

Inside the "build" job:
1. Scroll to the **"Run tests with phpunit"** step
2. Click to expand and see test output
3. Look for:
   ```
   OK (15 tests, 25 assertions)
   ```
   This means all tests passed!

### 5. If Tests Fail

If you see failures:
1. Look for **FAILURES!** in the output
2. Find which test failed
3. Read the error message
4. The output will show:
   - Test name that failed
   - Expected vs actual values
   - File and line number

## Example Output

### Successful Build
```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

...............                                                   15 / 15 (100%)

Time: 00:01.234, Memory: 10.00 MB

OK (15 tests, 25 assertions)
```

### Failed Build
```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

...F...........                                                   15 / 15 (100%)

Time: 00:01.234, Memory: 10.00 MB

There was 1 failure:

1) Post_Mark_As_Read_Access_Test::test_plugin_activated
Failed asserting that false is true.

/path/to/test.php:10

FAILURES!
Tests: 15, Assertions: 24, Failures: 1.
```

## CircleCI Configuration

The build is configured in `.circleci/config.yml`:

```yaml
jobs:
  build:
    docker:
      - image: circleci/php:7.4
    
    steps:
      - checkout
      - run: composer install
      - run: ./vendor/bin/phpunit --colors=always
```

## What Gets Tested

Every push to GitHub triggers:
1. **Checkout code** from the repository
2. **Install PHP dependencies** via Composer
3. **Run PHPUnit tests**:
   - Plugin activation
   - Admin menus
   - Settings
   - Per-user tracking
   - AJAX handlers
   - REST API endpoints
   - Security checks
   - Export/import
   - And more...

## Troubleshooting

### Build Not Starting
- Check if CircleCI is connected to your GitHub account
- Verify the project is being followed on CircleCI
- Check if the webhook is configured

### Tests Pass Locally But Fail on CircleCI
- Check PHP version differences (CircleCI uses PHP 7.4)
- Verify all dependencies are in `composer.json`
- Check for environment-specific issues

### Build Stuck on "Queued"
- CircleCI may be processing other jobs
- Free tier has limited parallelism
- Wait a few minutes or check CircleCI status page

## GitHub Actions Alternative

If CircleCI is unavailable, GitHub Actions also runs tests:

1. Go to your repository on GitHub
2. Click the **"Actions"** tab
3. See the same build/test information
4. Configuration is in `.github/workflows/php.yml`

## Need Help?

- CircleCI Documentation: [https://circleci.com/docs/](https://circleci.com/docs/)
- GitHub Actions: [https://docs.github.com/en/actions](https://docs.github.com/en/actions)
- Plugin Testing Docs: See `TESTING.md`

## Summary

✅ Check badges in README for quick status  
✅ Visit CircleCI dashboard for detailed logs  
✅ Click on builds to see test results  
✅ Tests run automatically on every push  
✅ Both CircleCI and GitHub Actions available
