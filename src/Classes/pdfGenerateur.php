<?php
namespace trdev\ContaoCantineBundle\Classes;

use Contao\System;
use Dompdf\Dompdf;
use Dompdf\Options;

class pdfGenerateur extends \Contao\Controller
{
    private $fileName  = "";
    private $variables = array();
    private $template  = '';

    private $filePath = '';
    private $folder   = 'files/tabularasa/';

    public function __construct($type = 'facture', $nomFic = 'monFichier', $vars = array())
    {
        $this->container  = System::getContainer();
        $this->logger     = $this->container->get('monolog.logger.contao');
        $this->projectDir = $this->container->getParameter('kernel.project_dir');
        $this->variables  = $vars;
        $this->twig       = $this->container->get('twig');
        $this->template   = sprintf('@Contao/pdf/%s.html.twig', $type);
        $this->fileName   = $nomFic . '.pdf';
        $this->filePath   = $this->folder . $this->fileName;

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        //ajout du twig
        $html = $this->twig->render(
            '@Contao/pdf/facture.html.twig',
            $this->variables
        );

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream($this->fileName,
            array(
                "Attachment" => false,
            )
        );
    }

}
