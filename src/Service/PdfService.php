<?php

namespace App\Service;

use Dompdf\Dompdf;

class PdfService
{
    private $dompdf;
    public function __construct()
    {
        $this->dompdf = new Dompdf();
        $pdfoptions = new \Dompdf\Options();
        $pdfoptions->set('defaultFont', 'Arial');
        $this->dompdf->setOptions($pdfoptions);
    }

    private function loadAndRenderPdf($html): void
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->render();
    }

    public function generatePdf($html): void
    {
        $this->loadAndRenderPdf($html);
        $this->dompdf->stream("details.pdf", [
            "Attachment" => false
        ]);
    }

    public function generateBinaryPdf($html): void
    {
        $this->loadAndRenderPdf($html);
        $this->dompdf->output();
    }
}