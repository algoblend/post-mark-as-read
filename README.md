[![Build Plugin](https://github.com/algoblend/post-mark-as-read/actions/workflows/php.yml/badge.svg?branch=main)](https://github.com/algoblend/post-mark-as-read/actions/workflows/php.yml) [![CircleCI](https://circleci.com/gh/algoblend/post-mark-as-read/tree/main.svg?style=shield)](https://circleci.com/gh/algoblend/post-mark-as-read/tree/main)

# Post Mark as Read - WordPress Plugin

A comprehensive WordPress plugin that allows users to mark posts as read/unread, track reading progress, and manage reading history with advanced features.

## Features

### Core Features
- **Mark Posts as Read/Unread**: Simple button integration to mark posts as complete
- **Per-User Tracking**: Each user has their own reading progress tracked independently
- **Multiple Display Locations**: Show the button before content, after content, or use as a widget/shortcode
- **Customizable Button**: Configure button text, icon, and location

### Advanced Features (v2.0)
- **📊 Reading Statistics Dashboard**: View comprehensive reading statistics for all users
  - Total posts count
  - Per-user reading progress
  - Visual progress bars
  - Percentage completion

- **📚 Reading History**: Detailed history of all posts marked as read
  - Filterable by user
  - Shows date when post was marked as read
  - Quick links to posts and editing

- **⚡ Bulk Actions**: Efficiently manage reading status
  - Mark multiple posts as read/unread at once
  - Filter by category
  - Apply actions for specific users

- **💾 Export/Import**: Backup and transfer reading data
  - Export all reading data as JSON
  - Import reading data from backup
  - Perfect for site migrations

- **🔌 REST API**: Integrate with external applications
  - `GET /pmar/v1/posts/{id}/read` - Get read status of a post
  - `POST /pmar/v1/posts/{id}/read` - Set read status
  - `GET /pmar/v1/user/stats` - Get user statistics
  - `GET /pmar/v1/user/history` - Get user reading history

### Security Features
- AJAX requests protected with nonces
- Proper capability checks on all admin pages
- Input sanitization and validation
- Secure REST API endpoints with authentication

### UI/UX Improvements
- Modern, responsive design
- Gradient buttons with smooth transitions
- Mobile-friendly interface
- Improved admin panel layout

## Installation

1. Download the plugin files
2. Upload to `/wp-content/plugins/post-mark-as-read/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure settings under 'Post Mark as Read' menu

## Usage

### Basic Setup
1. Go to **Post Mark as Read > Settings**
2. Configure:
   - Button Title (default: "Complete")
   - Button Icon (HTML, e.g., FontAwesome icons)
   - Button Location (Before/After content, or Widget)
3. Save settings

### Using the Shortcode
To display the button anywhere using a shortcode:
```php
[pmar_btn]
```

### Viewing Statistics
Navigate to **Post Mark as Read > Statistics** to view:
- Total published posts
- Reading progress per user
- Visual progress indicators

### Managing Reading History
Go to **Post Mark as Read > Reading History** to:
- View all posts marked as read
- Filter by user
- See dates when posts were marked as read

### Bulk Actions
Access **Post Mark as Read > Bulk Actions** to:
1. Select a user
2. Choose category (optional)
3. Select action (Mark as Read/Unread)
4. Apply to multiple posts at once

### Export/Import Data
From the settings page:
- **Export**: Download all reading data as JSON
- **Import**: Upload a previously exported JSON file

## REST API Usage

### Authentication
All endpoints require user authentication via WordPress cookies or application passwords.

### Get Post Read Status
```bash
GET /wp-json/pmar/v1/posts/123/read
```

Response:
```json
{
  "post_id": 123,
  "status": "read",
  "user_id": 1
}
```

### Set Post Read Status
```bash
POST /wp-json/pmar/v1/posts/123/read
Content-Type: application/json

{
  "status": "read"
}
```

### Get User Statistics
```bash
GET /wp-json/pmar/v1/user/stats
```

Response:
```json
{
  "user_id": 1,
  "read_count": 45,
  "total_posts": 100,
  "percentage": 45.0
}
```

### Get User Reading History
```bash
GET /wp-json/pmar/v1/user/history
```

## Changelog

### Version 2.0
- Added reading statistics dashboard
- Implemented reading history page
- Added bulk actions functionality
- Created REST API endpoints
- Added export/import functionality
- Enhanced security with nonces and sanitization
- Per-user reading tracking
- Modern UI with responsive design
- Added uninstall cleanup script

### Version 1.1
- Initial release
- Basic mark as read functionality
- Button customization
- AJAX support

## Developer Information

### Filters
The plugin uses standard WordPress filters that can be modified:
- `the_content` - Where the button is injected

### Database Structure
Reading data is stored as post meta:
- `pmar_read_{user_id}` - Stores 'read' or 'unread' status
- `pmar_read_date_{user_id}` - Stores timestamp when marked as read

## Requirements
- WordPress 5.0 or higher
- PHP 7.0 or higher
- jQuery (included with WordPress)

## Testing

The plugin includes comprehensive unit tests using PHPUnit. See [TESTING.md](wp-content/plugins/post-mark-as-read/TESTING.md) for detailed testing documentation.

### Running Tests Locally

```bash
cd wp-content/plugins/post-mark-as-read
composer install
composer test
```

### Continuous Integration

Tests run automatically on:
- **GitHub Actions**: [![Build Plugin](https://github.com/algoblend/post-mark-as-read/actions/workflows/php.yml/badge.svg?branch=main)](https://github.com/algoblend/post-mark-as-read/actions/workflows/php.yml)
- **CircleCI**: [![CircleCI](https://circleci.com/gh/algoblend/post-mark-as-read/tree/main.svg?style=shield)](https://circleci.com/gh/algoblend/post-mark-as-read/tree/main)

#### How to Check CI/CD Status

**GitHub Actions:**
1. Go to the [Actions tab](https://github.com/algoblend/post-mark-as-read/actions) in the repository
2. Click on any workflow run to see detailed logs
3. Green checkmark ✅ = all tests passed
4. Red X ❌ = tests failed (click to see which tests)

**CircleCI:**
1. Visit [CircleCI Dashboard](https://app.circleci.com/pipelines/github/algoblend/post-mark-as-read)
2. View build history and test results
3. Click on any build for detailed logs
4. Success = green, Failure = red

### Test Coverage

Our test suite includes:
- Plugin activation and initialization
- Admin menu and submenu registration
- Settings and options management
- Per-user reading status tracking
- AJAX handler security and functionality
- REST API endpoint registration and authentication
- Content filters and shortcodes
- Script/style enqueueing
- Export/import functionality
- Uninstall cleanup

15+ test cases covering all major features.

## Support & Contributing
- Report issues on [GitHub Issues](https://github.com/algoblend/post-mark-as-read/issues)
- Submit pull requests for improvements

## License
GPL v2 or later

## Author
**Alok Verma**
- Website: [AlgoBlend](http://algoblend.com/alok-verma)
- Plugin URI: https://www.algoblend.in/wordpress/plugin/post-mark-as-read/

## Credits
Special thanks to all contributors and users who provided feedback for version 2.0 improvements.
