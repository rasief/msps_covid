<?php

require_once './vendor/autoload.php';
/**
 * Header file
*/
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Shape\RichText;

$objPHPPresentation = new PhpPresentation();

$currentSlide = $objPHPPresentation->getActiveSlide();

$shape = $currentSlide
        ->createRichTextShape()
        ->setHeight(300)
        ->setWidth(600)
        ->setOffsetX(170)
        ->setOffsetY(180);

$textRun = $shape->createTextRun('Hola mundo');
/*$shape = $currentSlide->createDrawingShape();
$shape->setPath('./RangosIMC.png')
    ->setHeight(300)
    ->setOffsetX(100)
    ->setOffsetY(100);*/


$textRun
    ->getFont()
    ->setBold(true)
    ->setSize(18);

$write = IOFactory::createWriter($objPHPPresentation, 'PowerPoint2007');
$write->save('export.pptx');


?>