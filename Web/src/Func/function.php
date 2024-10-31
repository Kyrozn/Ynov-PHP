<?php  
require '/vendor/autoload.php'; 

function guidv4(): string {
    $data = null;
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

use Dompdf\Dompdf;

// Vérifie si le contenu HTML a été envoyé
if (isset($_POST['html'])) {
    $html = $_POST['html'];
    htmltopdf($html);
}

function htmltopdf($html)
{
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    echo $html;
    // (Optional) Set paper size and orientation (e.g., A4, portrait)
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF (downloadable as "document.pdf")
    $dompdf->stream("test.pdf", array("Attachment" => 0));
}