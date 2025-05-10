<?php
require_once 'config.php';
secureSession();
setSecurityHeaders();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Textify My PDF - PDF to Text Converter</title>
    <meta name="description" content="Upload PDF files and convert them into text format with Textify My PDF - PDF to Text Converter. Specify the maximum number of characters to extract from the PDF file.">
    <meta name="keywords" content="PDF, text, converter, upload, convert, extract">
    <meta name="author" content="Micael Vinhas">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="index.js" defer></script>
    <script>
    function displayFileName() {
        const fileInput = document.getElementById('file');
        const selectedFile = document.getElementById('selectedFile');
        const maxSize = 256 * 1024 * 1024; // 256MB in bytes
        
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const fileSize = file.size;
            const fileSizeMB = (fileSize / (1024 * 1024)).toFixed(2);
            
            if (fileSize > maxSize) {
                selectedFile.innerHTML = `<span class="text-red-600">File is too large (${fileSizeMB}MB). Maximum size is 256MB.</span>`;
                fileInput.value = ''; // Clear the file input
            } else {
                selectedFile.innerHTML = `Selected file: ${file.name} (${fileSizeMB}MB)`;
            }
        } else {
            selectedFile.innerHTML = '';
        }
    }
    </script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
    <div class="flex-grow flex items-center justify-center">
        <div class="max-w-lg w-full bg-white p-8 rounded-lg shadow-lg">
            <h1 class="text-3xl font-bold text-center mb-8 text-indigo-600">Textify My PDF</h1>
            <form action="PDF2Text.php" method="post" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                
                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700">Choose a PDF file</label>
                    <input type="file" id="file" name="file" accept="application/pdf" required onchange="displayFileName()" class="mt-2 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm leading-5 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="max_chars" class="block text-sm font-medium text-gray-700">Maximum Characters</label>
                    <input type="number" id="max_chars" name="max_chars" value="<?= $_SESSION['max_chars'] ?? 0 ?>" min="0" placeholder="Enter maximum characters" required class="mt-2 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm leading-5 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-2 text-sm text-gray-500">Specify the maximum number of characters to extract from the PDF. Enter 0 to extract all text.</p>
                </div>
                
                <div>
                    <label for="paragraph_size" class="block text-sm font-medium text-gray-700">Maximum Characters per Paragraph</label>
                    <input type="number" id="paragraph_size" name="paragraph_size" value="<?= $_SESSION['paragraph_size'] ?? 500 ?>" min="0" placeholder="Enter maximum characters per paragraph" required class="mt-2 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm leading-5 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-2 text-sm text-gray-500">Specify the maximum number of characters per paragraph. Enter 0 if you don't want to create paragraphs.</p>
                </div>
                
                <div>
                    <label for="line_length" class="block text-sm font-medium text-gray-700">Line Length</label>
                    <input type="number" id="line_length" name="line_length" value="<?= $_SESSION['line_length'] ?? 80 ?>" min="40" max="120" step="10" placeholder="Enter line length" required class="mt-2 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm leading-5 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-2 text-sm text-gray-500">Specify the maximum number of characters per line (between 40 and 120).</p>
                </div>
                
                <div>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Upload PDF</button>
                </div>
            </form>
            <div id="selectedFile" class="mt-4 text-sm text-gray-500"></div>
        </div>
    </div>

    <footer class="bg-gray-200 text-center py-4 mt-auto">
        <p class="text-sm text-gray-600">&copy; 2024 - <?= date("Y"); ?> Micael Vinhas</p>
        <p class="text-sm text-gray-600">Source Code: <a href="https://github.com/MVinhas/PDF2Text" class="text-indigo-600 hover:text-indigo-900">GitHub</a></p>
    </footer>
</body>
</html>