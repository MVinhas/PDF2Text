<?php

namespace PDF2Text\Security;

use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Symfony\Component\RateLimiter\Limit;
use PDF2Text\Core\Config;

class SecurityService
{
    private CsrfTokenManager $csrfManager;
    private RateLimiterFactory $rateLimiter;
    private array $securityHeaders = [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'DENY',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' cdn.tailwindcss.com; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:;",
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
    ];

    public function __construct(?TokenStorageInterface $tokenStorage = null)
    {
        $config = Config::getInstance();
        
        $storage = $tokenStorage ?? new NativeSessionTokenStorage();
        $this->csrfManager = new CsrfTokenManager(new UriSafeTokenGenerator(), $storage);

        $this->rateLimiter = new RateLimiterFactory(
            [
                'id' => 'pdf_upload',
                'policy' => 'sliding_window',
                'limit' => 10,
                'interval' => '1 minute',
            ],
            new InMemoryStorage()
        );
    }


    public function generateCsrfToken(string $tokenId): string
    {
        $token = $this->csrfManager->getToken($tokenId)->getValue();
        
        // In test environment, store the token directly in session
        if (Config::getInstance()->isTestEnvironment()) {
            $_SESSION['csrf_tokens'][$tokenId] = $token;
        }
        
        return $token;
    }

    public function validateCsrfToken(string $tokenId, string $token): bool
    {
        // In test environment, validate against session storage
        if (Config::getInstance()->isTestEnvironment()) {
            return isset($_SESSION['csrf_tokens'][$tokenId]) && $_SESSION['csrf_tokens'][$tokenId] === $token;
        }
        
        return $this->csrfManager->isTokenValid($this->csrfManager->getToken($tokenId));
    }

    public function checkRateLimit(string $ip): bool
    {
        $limiter = $this->rateLimiter->create($ip);
        $limit = $limiter->consume(1);
        return $limit->isAccepted();
    }

    public function setSecurityHeaders(): void
    {
        if (!headers_sent()) {
            foreach ($this->securityHeaders as $header => $value) {
                header("$header: $value");
            }
        }
    }

    public function secureSession(): void
    {
        if (headers_sent($file, $line)) {
            throw new \RuntimeException("Cannot start session: headers already sent in $file on line $line");
        }

        if (session_status() === PHP_SESSION_NONE) {
            if (!Config::getInstance()->isTestEnvironment()) {
                session_set_cookie_params([
                    'lifetime' => 120,
                    'path' => '/',
                    'domain' => '',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            }
            session_start();
        }
    }


    public function validateFile(array $file): bool
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        return in_array($mimeType, ['application/pdf'], true);
    }

    public function sanitizeOutput(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
} 