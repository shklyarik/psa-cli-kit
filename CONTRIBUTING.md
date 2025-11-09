# Contributing

Contributions are welcome! Here's how you can help improve this library.

## Development

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Add tests if applicable
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

## Code Style

Please follow PSR-12 coding standards. You can use PHP CS Fixer to format your code:

```bash
composer global require friendsofphp/php-cs-fixer
php-cs-fixer fix src/
```

## Testing

Make sure to run any existing tests before submitting a pull request:

```bash
# If there are tests
php vendor/bin/phpunit
```

## Documentation

Please update the documentation (README.md, PHPDoc comments) when making changes to the public API.