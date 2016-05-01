<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use ZendPdf as Pdf;
use ZendPdf\Color;
use ZendPdf\Resource\Image;
use ZendPdf\Resource\Font;
use ZendPdf\InternalType;
use Zend\Barcode;
use Zend\Config\Config;

class PrepareController extends Controller
{
    public function getQrcodes()
    {
        return view('prepare.qrcodes');
    }

    public function getQrcodesPrint()
    {
        $pdf = new Pdf\PdfDocument();

        // Your font (path might differ)
        \Zend\Barcode\Barcode::setBarcodeFont(app_path() . '/../resources/fonts/AverageSans-Regular.ttf');

        for ($i = 1; $i <= 32; $i++) {
            $renderer = new \Zend\Barcode\Renderer\Pdf();
            //$renderer->setTopOffset($i*1750);

            $config = new \Zend\Config\Config(
                array(
                    'barcode' => 'ean8',
                    'barcodeParams' => array('text' => $i, 'factor' => 1.9, 'withChecksum' => true, 'drawText' => false),
                    'renderer' => 'pdf', // here is your new renderer
                    'rendererParams' => array('drawText' => false), // you can define position offset here
                )
            );

            $pdfWithBarcode = \Zend\Barcode\Barcode::factory($config);
            //$pdfWithBarcode->drawText(false);
            $pdfWithBarcode->setTopOffset(($i-1)%8*80+50)
                ->setLeftOffset(floor(($i-1)/8)*140)
                ->setResource($pdf)->draw(); // your new barcode renderer is defined here, from now on to add things to your pdf you need to use the new variable ($pdfWithBarcode)
        }

        header('Content-Type: application/pdf');
        echo $pdf->render();

    }
}
