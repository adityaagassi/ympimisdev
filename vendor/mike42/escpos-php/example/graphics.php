<?php
/* Print-outs using the newer graphics print command */

require __DIR__ . '/../autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

$connector = new WindowsPrintConnector("FLO Printer");
$printer = new Printer($connector);

try {
    $tux = EscposImage::load("resources/tux.png", false);
     $tul = EscposImage::load("resources/tulips.png", false);
    
    $printer -> graphics($tul);
    $printer -> text("Regular Tux.\n");
    $printer -> feed();
    
    $printer -> graphics($tux, Printer::IMG_DOUBLE_WIDTH);
    $printer -> text("Wide Tux.\n");
    $printer -> feed();
    
    $printer -> graphics($tux, Printer::IMG_DOUBLE_HEIGHT);
    $printer -> text("Tall Tux.\n");
    $printer -> feed();
    
    $printer -> graphics($tux, Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
    $printer -> text("Large Tux in correct proportion.\n");
    
    $printer -> cut();
} catch (Exception $e) {
    /* Images not supported on your PHP, or image file not found */
    $printer -> text($e -> getMessage() . "\n");
}

$printer -> close();
