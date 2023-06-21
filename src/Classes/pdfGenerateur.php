<?php
namespace trdev\ContaoCantineBundle\Classes;

use Contao\System;
use Dompdf\Dompdf;
use Dompdf\Options;

class pdfGenerateur extends \Contao\Controller
{
    private $fileName  = '';
    private $variables = array();
    private $template  = '';
    private $filePath  = '';
    private $folder    = 'files/documents/%s/%s/%s/';

    public function __construct($type = 'coupon', $nomFic = 'monFichier', $vars = array())
    {
        $this->container  = System::getContainer();
        $this->logger     = $this->container->get('monolog.logger.contao');
        $this->projectDir = $this->container->getParameter('kernel.project_dir');
        $this->twig       = $this->container->get('twig');
        $this->template   = sprintf('@Contao/pdf/%s.html.twig', $type);
        $this->variables  = $vars;
        $this->fileName   = str_replace(' ', '_', $nomFic) . '.pdf';
        $this->folder     = sprintf($this->folder, $type, date('Y'), date('m'));
        $this->filePath   = $this->folder . $this->fileName;

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('enable_remote', true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        //Envoi du contenu CSS
        $http                        = ($_SERVER['REQUEST_SCHEME'] == '') ? 'https' : 'http';
        $css                         = file_get_contents('bundles/contaocantine/css/pdf.css');
        $this->variables['css']      = $css;
        $this->variables['h1']       = $nomFic;
        $this->variables['heureGen'] = time();

        $this->setFolder();

        // Retrieve the HTML generated in our twig file
        $html = $this->twig->render(
            $this->template,
            $this->variables
        );

        if (\Input::get('debug')) {
            echo $html;
            die();
        }

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        $output = $dompdf->output();

        $folderModel = new \Folder($this->folder);
        if ($folderModel->isEmpty()) {
            $vide = true;
            if (!$folderModel->isUnprotected()) {
                $folderModel->unprotect();
            }
        } else {
            $vide = false;
        }

        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }

        $f = fopen($this->filePath, 'w+');
        fwrite($f, $output);
        fclose($f);
        /*
        file_put_contents($this->filePath, $output);
        Output the generated PDF to Browser (force download)
        $dompdf->stream($this->fileName, [
        "Attachment" => true,
        ]);
         */

        $url = sprintf('%s://%s/%s', $http, $_SERVER['HTTP_HOST'], $this->filePath);

        header('Location: ' . $url);
        exit();
    }

    private function setFolder()
    {
        $filePath = sprintf('%s/%s', $this->folder, $this->filname);

        if (!file_exists($this->folder)) {
            mkdir($this->folder, 0777, true);
        }
    }

    private function error($erreur)
    {
        if (is_object($erreur)) {
            return array(
                'result'  => 'error',
                'details' => array(
                    'msg'   => $erreur->getMessage(),
                    'trace' => $erreur->getTrace(),
                ),
            );
        } else {
            return array(
                'result'  => 'error',
                'details' => array(
                    'msg' => $erreur,
                ),
            );
        }
    }

    public function saveLog($th, $fonction = '')
    {
        $this->logger->log(LogLevel::ERROR, $th->getMessage(), array('contao' => new ContaoContext($fonction, "PDF")));
    }
}
