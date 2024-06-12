<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Text Output</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="result.js" defer></script>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen">
    <div class="container p-4">
        <div class="max-w-3xl w-full bg-white p-8 rounded-lg shadow-lg mx-auto">
            <h1 class="text-2xl font-bold text-center mb-6 text-indigo-600">Extracted Text</h1>
            <pre class="bg-gray-200 p-4 rounded-lg overflow-auto text-sm"><?= $text ?></pre>
            <div class="mt-6 flex justify-between items-center">
                <a href="/" class="text-indigo-600 hover:text-indigo-900 font-medium">Convert another PDF</a>
                <button id="copyTextBtn" class="bg-indigo-600 text-white py-2 px-4 rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Select and Copy</button>
            </div>
            <div id="copyMessage" class="text-green-600 text-sm mt-4 hidden">Text copied to clipboard!</div>
        </div>
    </div>
</body>
</html>
