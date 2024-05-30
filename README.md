
# PDF2Text

PDF2Text is a lightweight tool designed to extract text from PDF files. It provides a simple and efficient way to convert PDF documents into plain text format, making it easy to manipulate, analyze, and process the extracted text.

## Features

- Extract text content from PDF files.
- Preserve formatting and layout.
- Support for both single-page and multi-page PDFs.
- Web-browser interface

## Installation

### Requirements

- PHP 7.2 or higher
- Composer (for dependency management)

### Installation Steps

### 1. Install `pdftotext`

#### On Ubuntu/Debian

```sh
sudo apt-get update
sudo apt-get install poppler-utils
```
On macOS
If you have Homebrew installed:

```
brew install poppler
```
On Windows
Download the Poppler for Windows package from http://blog.alivate.com.au/poppler-windows/.
Extract the package to a directory, e.g., C:\Poppler.
Add the bin directory (e.g., C:\Poppler\bin) to your system's PATH environment variable.

2. Clone the repository:

   ```bash
   git clone https://github.com/MVinhas/PDF2Text.git
   ```
   
   
3. If necessary, update the max_upload_filesize and max_post_size settings in your php.ini file to accommodate large PDF files.

4. Navigate to the project directory:

   ```bash
   cd PDF2Text
   ```

5. Install dependencies using Composer:

   ```bash
   composer install
   ```

## Usage

1. Access the application through a web browser.

2. Choose a PDF file to upload.

3. Optionally, specify the maximum number of characters to extract from the PDF file. Enter 0 to extract all text.

4. Click the "Upload PDF" button to initiate the conversion process.

5. Once the conversion is complete, the extracted text will be displayed on the screen.

6. If necessary, adjust the maximum number of characters and upload another PDF file.


## Contributing

Contributions are welcome! If you have any ideas, suggestions, or bug fixes, feel free to open an issue or submit a pull request.

## License

This project is licensed under the GNU General Public License v3.0. See the LICENSE file for details.