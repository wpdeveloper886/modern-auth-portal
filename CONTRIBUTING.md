# Contributing to Modern Auth Portal

Thank you for your interest in contributing to Modern Auth Portal! We welcome contributions from the community.

## How to Contribute

### Reporting Bugs

Before creating a bug report, please check the issue list to see if the problem has already been reported. When creating a bug report, include:

- **Title**: Clear and descriptive
- **Description**: Detailed description of the issue
- **Steps to Reproduce**: Exact steps to reproduce the problem
- **Expected Behavior**: What you expected to happen
- **Actual Behavior**: What actually happened
- **Environment**: WordPress version, PHP version, plugin version, etc.

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, please include:

- **Title**: Clear and concise title
- **Description**: Detailed description of the suggested enhancement
- **Motivation**: Why this enhancement would be useful
- **Examples**: Examples of how it might work

### Pull Requests

1. Fork the repository
2. Create a branch for your feature: `git checkout -b feature/your-feature-name`
3. Make your changes
4. Commit your changes: `git commit -am 'Add some feature'`
5. Push to the branch: `git push origin feature/your-feature-name`
6. Open a pull request

#### Pull Request Guidelines

- Update the README.md with any new features
- Update the CHANGELOG.md with your changes
- Follow the existing code style and structure
- Test your changes thoroughly
- Ensure all code is properly documented

## Code Style

- Follow WordPress PHP coding standards
- Use meaningful variable and function names
- Add comments for complex logic
- Use proper sanitization for all user inputs
- Use proper escaping for all outputs

## Development Setup

1. Clone the repository
2. Install WordPress locally
3. Copy the plugin to `/wp-content/plugins/`
4. Activate the plugin in WordPress admin
5. Make your changes
6. Test thoroughly

## Testing

Before submitting a pull request:

1. Test all functionality
2. Check for PHP errors and warnings
3. Verify responsive design
4. Test with different user roles
5. Test security features (2FA, nonces, etc.)

## File Structure

Please maintain the existing file structure:

```
modern-auth-portal/
├── inc/                    # Core functions
│   ├── functions-database.php
│   ├── functions-utils.php
│   ├── functions-core.php
│   ├── functions-ajax.php
│   └── functions-admin.php
├── admin/                  # Admin pages
├── public/                 # Frontend shortcodes
├── assets/                 # CSS/JS files
└── languages/              # Translation files
```

## Licensing

By contributing to Modern Auth Portal, you agree that your contributions will be licensed under the GPL v2 or later license.

## Questions?

Feel free to reach out to the author for questions or clarifications.

Thank you for contributing! 🎉
