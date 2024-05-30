
document.getElementById('file').addEventListener('change', function() {
    const fileInput = document.getElementById('file');
    const selectedFile = document.getElementById('selectedFile');
    const fileName = fileInput.files[0].name;
    selectedFile.textContent = `Selected file: ${fileName}`;
});

