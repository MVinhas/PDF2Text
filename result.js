// Select and copy text functionality
document.getElementById('copyTextBtn').addEventListener('click', function() {
    const preElement = document.querySelector('pre');
    const textToCopy = preElement.textContent;

    navigator.clipboard.writeText(textToCopy)
        .then(() => {
            showCopyMessage();
        })
        .catch((error) => {
            console.error('Failed to copy text: ', error);
        });
});

// Function to show "Text copied to clipboard" message
function showCopyMessage() {
    const copyMessage = document.getElementById('copyMessage');
    copyMessage.style.display = 'block';
    setTimeout(() => {
        copyMessage.style.display = 'none';
    }, 3000);
}