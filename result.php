<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Text Output</title>
    <link rel="stylesheet" href="styles.css">
    <script src="result.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Extracted Text</h1>
        <pre><?= $text ?></pre>
        <div class="convert-another">
            <a href="/">Convert another PDF</a>
        </div>
        <div class="copy-button">
            <button id="copyTextBtn">Select and Copy</button>
        </div>
        <div class="copy-message" id="copyMessage">Text copied to clipboard!</div>
    </div>
</body>
</html>