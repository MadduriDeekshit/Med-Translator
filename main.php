<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Set the character encoding to UTF-8 -->
    <meta charset="UTF-8">
    <!-- Specify the compatibility mode for Internet Explorer -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Set the viewport to control the layout on mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Set the title of the web page -->
    <title>PDF to Text Translator</title>
    <!-- Include the PDF.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js" integrity="sha512-ml/QKfG3+Yes6TwOzQb7aCNtJF4PUyha6R3w8pSTo/VJSywl7ZreYvvtUso7fKevpsI+pYVVwnu82YO0q3V6eg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- Add some styling for the web page -->
    <link rel="stylesheet" href="css/main.css">
    <style>
        /* Center align the heading */
        h1 {
            width: 100%;
            text-align: center;
        }
        /* Set the display and alignment for the main and result sections */
        .pdfwork, .afterupload {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            width: 50%;
            height: 50%;
        }
        /* Add margin-top to all elements inside .pdfwork */
        .pdfwork * {
            margin-top: 10px;
        }
        /* Hide the result section initially */
        .afterupload {
            display: none;
        }
        /* Hide the "Extract Another PDF" button initially */
        .another {
            display: none;
        }
        .afterupload{
            margin-top: 20%;
            background-color: white;
            width: 100vh;
            height: 100%;
            color: black
        }
        .btn btn-success download text-white bg-white border border-white {
            background-color: red;
        }

    </style>
</head>
<body>
<video autoplay muted loop id="myVideo">
  <source src="img/video.mp4" type="video/mp4">
</video>
<div class="afterupload">
<div id="google_translate_element"></div>
            <!-- Your translated content goes here -->
            <select class="form-select selectpage" onchange="afterProcess()"></select>
            <!-- Download link for the extracted text file -->
            <a href="" class="btn btn-success download text-white bg-white border border-white" download></a>

            <!-- Display the extracted text on the website -->
            <p class="pdftext"></p>
            <a href="" class="btn btn-success download text-white bg-white border border-white" download></a>
            <!-- Display the extracted text on the website -->
            <h1>Do You Need Diet ?</h1>
            <button><h4><a href="Diet.php">YES</a></h4></button>
            <button><h4><a href="main.php">NO</a></h4></button>

           
        </div>
    <!-- Create a div container for the file upload form and result section -->
    <div class="container pdfwork">
        <h1 class="display-4">PDF To Text translator</h1>
        <!-- Button to extract another PDF (hidden initially) -->
        <button class="btn btn-secondary another" onclick="location.reload()">Translate Another PDF</button>
        <!-- File input field for selecting the PDF file -->
        <input type="file" class="form-control-file selectpdf">
        <!-- Password input field (optional) -->
        <input type="password" class="form-control pwd" placeholder='Optional Password'>
        <!-- Button to upload the selected PDF -->
        <button class="btn btn-primary upload" onclick="onTranslateClick()">Translate</button>
        <!-- Result section (hidden initially) -->
    </div>
    <!-- JavaScript code -->
<script>

    // Set the worker source for PDF.js library
    pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js";
    
    // Get references to various elements

    let pdfinput = document.querySelector(".selectpdf"); // Reference to the PDF file input field
    let pwd = document.querySelector(".pwd"); // Reference to the password input field
    let upload = document.querySelector(".upload"); // Reference to the upload button
    let afterupload = document.querySelector(".afterupload"); // Reference to the result section
    let select = document.querySelector("select"); // Reference to the page selection dropdown
    let download = document.querySelector(".download"); // Reference to the download link
    let pdftext = document.querySelector(".pdftext"); // Reference to the paragraph for displaying extracted text
    
    // Event listener for the upload button click
    function onTranslateClick() {
        let file = pdfinput.files[0]; // Get the selected PDF file
        if (file != undefined && file.type == "application/pdf") {
            let fr = new FileReader(); // Create a new FileReader object
            fr.readAsDataURL(file); // Read the file as a data URL
            fr.onload = () => {
                let res = fr.result; // Get the result of file reading
                if (pwd.value == "") {
                    extractText(res, false); // Extract text without password
                } else {
                    extractText(res, true); // Extract text with a password
                }
            }
        } else {
            alert("Select a valid PDF file");
        }
    }
    
    let alltext = []; // Array to store all extracted text
    
    // Asynchronous function to extract text from the PDF
    async function extractText(url, pass) {
        try {
            let pdf;
            if (pass) {
                pdf = await pdfjsLib.getDocument({ url: url, password: pwd.value }).promise; // Get the PDF document with a password
            } else {
                pdf = await pdfjsLib.getDocument(url).promise; // Get the PDF document without a password
            }
            let pages = pdf.numPages; // Get the total number of pages in the PDF
            for (let i = 1; i <= pages; i++) {
                let page = await pdf.getPage(i); // Get the page object for each page
                let txt = await page.getTextContent(); // Get the text content of the page
                let text = txt.items.map((s) => s.str).join(""); // Concatenate the text items into a single string
                alltext.push(text); // Add the extracted text to the array
            }
            alltext.map((e, i) => {
                select.innerHTML += `<option value="${i+1}">${i+1}</option>`; // Add options for each page in the page selection dropdown
            });
            afterProcess(); // Display the result section
        } catch (err) {
            alert(err.message);
        }
    }
    
    // Function to handle the post-processing after text extraction
    function afterProcess() {
        pdftext.textContent = alltext[select.value - 1]; // Display the extracted text for the selected page
        download.href = "data:text/plain;charset=utf-8," + encodeURIComponent(alltext[select.value - 1]); // Set the download link URL for the extracted text
        afterupload.style.display = "flex"; // Display the result section
        document.querySelector(".another").style.display = "unset"; // Display the "Extract Another PDF" button
    }
</script>
<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({ pageLanguage: 'en', includedLanguages: 'te' }, 'google_translate_element');
    }
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<!-- Add Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
