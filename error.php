<?php

use PDF2Text\Core\Config;

require_once __DIR__ . '/../vendor/autoload.php';

$config = Config::getInstance();
$isDev = $config->get('app.debug', false);

session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded shadow-lg text-center max-w-2xl w-full">
        <h1 class="text-3xl font-bold mb-4">Oops! Something went wrong 😢</h1>
        <p class="text-gray-600 mb-8">We are sorry for the inconvenience. Please try again later.</p>

        <?php if ($isDev && isset($_SESSION['last_error'])): ?>
            <div class="bg-red-50 border border-red-200 rounded p-4 mb-8 text-left">
                <h2 class="text-red-800 font-semibold mb-2">Error Details:</h2>
                <pre class="text-sm text-red-600 whitespace-pre-wrap"><?= htmlspecialchars($_SESSION['last_error']) ?></pre>
            </div>
        <?php endif; ?>

        <a href="/" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Back to Homepage
        </a>
    </div>
</body>
</html>