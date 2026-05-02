# Security Policy

## 🔒 Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 0.1.x   | :white_check_mark: |

## Reporting a Vulnerability

We take the security of Laravel Domo seriously. If you discover a security vulnerability, please follow these steps:

### How to Report

1. **DO NOT** create a public GitHub issue
2. Send an email to [murksopps@gmail.com](mailto:murksopps@gmail.com)
3. Include:
   - Description of the vulnerability
   - Steps to reproduce
   - Potential impact
   - Suggested fix (if any)

### What to Expect

- **Initial Response**: Within 48 hours
- **Status Update**: Within 5 business days
- **Resolution Timeline**: Depends on severity
  - Critical: 24-48 hours
  - High: 1 week
  - Medium: 2 weeks
  - Low: 4 weeks

### Process

1. Security team will investigate the report
2. We'll work on a fix
3. A new release will be published
4. Security advisory will be published after 30 days (to give users time to update)

## Security Best Practices

When using Laravel Domo:

1. **API Keys**: Never commit API keys to version control
2. **Environment Variables**: Use `.env` files for sensitive configuration
3. **Access Control**: Restrict dashboard access with middleware
4. **Updates**: Keep the package updated to the latest version
5. **HTTPS**: Always use HTTPS in production

## Known Limitations

- AI drivers require external API calls - ensure proper network security
- MCP server should not be exposed to public networks without authentication

## Acknowledgments

We appreciate responsible disclosure and will credit security researchers (with permission) in our security advisories.
