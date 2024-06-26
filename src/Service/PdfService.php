<?php

// Dans src/Service/PdfService.php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PdfService
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function generatePdf(
        string $view,
        array $parameters = [],
        string $filename = 'document.pdf',
        string $size = 'A4',
        string $orientation = 'portrait',
    ): Response {
        // Configure Dompdf selon vos besoins
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instancie Dompdf avec nos options
        $dompdf = new Dompdf($pdfOptions);

        // Récupère le HTML généré par notre fichier Twig
        $html = $this->twig->render($view, $parameters);

        // Charge le HTML dans Dompdf
        $dompdf->loadHtml($html);

        // (Optionnel) Configure le papier et l'orientation
        $dompdf->setPaper($size, $orientation);

        // Génère le PDF
        $dompdf->render();

        // Génère la réponse
        $pdfOutput = $dompdf->output();
        $response = new Response($pdfOutput);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="'.$filename.'"');

        return $response;
    }
}
