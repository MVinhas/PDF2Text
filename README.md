# PDF2Text - Secure PDF to Text Converter

A secure and robust PDF to text converter built with PHP. This application provides a simple web interface to convert PDF files to text while maintaining high security standards and following best practices.

## Features

- Secure file upload handling
- CSRF protection
- Rate limiting
- Comprehensive error handling
- Detailed logging
- Configurable text extraction limits
- Paragraph formatting
- Multiple PDF processing methods (pdftotext and PDF Parser)
- Modern, responsive UI with Tailwind CSS

## Security Features

- Input validation and sanitization
- Secure session handling
- Comprehensive security headers
- File type validation
- Size limits enforcement
- Rate limiting
- CSRF protection
- XSS prevention
- Secure file handling

## Requirements

- PHP 8.3 or higher
- Composer
- pdftotext (poppler-utils)
- Required PHP extensions:
  - mbstring
  - xml
  - gd
  - fileinfo

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/MVinhas/PDF2Text.git
   cd PDF2Text

2. Install dependencies:

   ```
   composer install

3. Install system dependencies:

   ```
   sudo apt-get install poppler-utils

4. Create and configure environment file:

   ```
   cp .env.example .env
   # Edit .env with your settings

5. Set up directory permissions:

   ```
   mkdir -p logs cache
   chmod 755 logs cache

## Configuration

The application can be configured through the `.env` file. Available options:

* `APP_ENV`: Application environment (development/production)
* `APP_DEBUG`: Enable/disable debug mode
* `APP_URL`: Application URL
* `SESSION_LIFETIME`: Session lifetime in minutes
* `MAX_FILE_SIZE`: Maximum file upload size in bytes
* `MAX_ALLOWED_CHARS`: Maximum characters to extract
* `LOG_LEVEL`: Logging level
* `RATE_LIMIT_ENABLED`: Enable/disable rate limiting
* `RATE_LIMIT_ATTEMPTS`: Number of allowed attempts
* `RATE_LIMIT_DECAY_MINUTES`: Rate limit decay time

## Usage

1. Start the development server:

   ```
   php -S localhost:8000
   ```

2. Open your browser and navigate to `http://localhost:8000`

3. Upload a PDF file and configure the extraction options:

   * Maximum characters to extract
   * Maximum characters per paragraph

4. View the extracted text with proper formatting

## Development

### Running Tests

```
composer test
```

### Code Style

```
composer cs-check
composer cs-fix
```

### Static Analysis

```
composer phpstan
composer psalm
```

## Security

This application implements several security measures:

* CSRF protection for all forms
* Rate limiting to prevent abuse
* Secure session handling
* Input validation and sanitization
* Secure file handling
* Comprehensive security headers
* XSS prevention
* File type validation

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the Apache License 2.0 - see the [LICENSE](LICENSE) file for details.

## Author

Micael Vinhas - [GitHub](https://github.com/MVinhas)

## Acknowledgments

* [Smalot PDF Parser](https://github.com/smalot/pdfparser)
* [Symfony Components](https://symfony.com/components)
* [Monolog](https://github.com/Seldaek/monolog)
* [Tailwind CSS](https://tailwindcss.com)
* **This code was created with the help of [Cursor](https://www.cursor.com/)**

## Components Used

The project makes use of several modern and reliable libraries and components to handle different functionalities:

* **PHP 8.3+** - The latest versions of PHP - this will be regularly updated.
* **Symfony Components** - Various Symfony components including `HttpFoundation`, `Security CSRF`, and `Rate Limiter` for security and session management.
* **Monolog** - For comprehensive logging capabilities.
* **Smalot PDF Parser** - A PDF parsing library.
* **Tailwind CSS** - A utility-first CSS framework for modern and responsive UI.
* **PHPStan, Psalm** - Static analysis tools to ensure code quality.
* **Mockery** - A PHP mocking framework for testing.

```
```