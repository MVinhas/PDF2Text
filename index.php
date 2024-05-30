<?php
session_start();
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
    <link rel="stylesheet" href="styles.css">
    <script src="index.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Textify My PDF - PDF to Text Converter</h1>
        <form action="PDF2Text.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <input type="file" id="file" name="file" accept="application/pdf" required onchange="displayFileName()">
            <label for="file">Choose a PDF file</label>
            <input type="number" id="max_chars" name="max_chars" value="<?= $_SESSION['max_chars'] ?? 0?>" min="0" placeholder="Enter maximum characters" required>
            <div class="helper-text">Specify the maximum number of characters to extract from the PDF. Enter 0 to extract all text.</div>
            <input type="number" id="paragraph_size" name="paragraph_size" value="<?= $_SESSION['paragraph_size'] ?? 500?>" min="0" placeholder="Enter maximum characters per paragraph" required>
            <div class="helper-text">Specify the maximum number of characters per paragraph. Enter 0 if you don't want to create paragraphs.</div>
            <input type="submit" value="Upload PDF">
        </form>
        <div id="selectedFile"></div>
        </div>

    <footer>
        <p>&copy; <?= date("Y"); ?> Micael Vinhas</p>
        <p>|</p>
        <p>Source Code: <a href="https://github.com/MVinhas/PDF2Text">GitHub</a></p>
    </footer>

</body>
</html>
