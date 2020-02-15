<?php
    error_reporting(E_ALL); // Error engine - always E_ALL!

    include 'vendor/autoload.php';
    
    $target_dir = "uploads/";
    $file_name = basename($_FILES["filePDF"]["name"]);
    $target_file_pdf = $target_dir . basename($_FILES["filePDF"]["name"]);
    $target_file_xml = $target_dir . basename($_FILES["fileXML"]["name"]);
    $uploadOk = 1;
    $fileTypePDF = strtolower(pathinfo($target_file_pdf,PATHINFO_EXTENSION));
    $fileTypeXML = strtolower(pathinfo($target_file_xml,PATHINFO_EXTENSION));

// Check PDF file size
    if ($_FILES["filePDF"]["size"] > 500000) {
        echo '<script>alert("Archivo PDF supera los 5MB."); location.replace(document.referrer);</script>';

        $uploadOk = 0;
    }
    // Check XML file size
    if ($_FILES["fileXML"]["size"] > 500000) {
        echo '<script>alert("Archivo XML supera los 5MB."); location.replace(document.referrer);</script>';

        $uploadOk = 0;
    }
// // Allow certain file formats
    if($fileTypePDF != "pdf" && $fileTypePDF != "xml") {
        echo '<script>alert("Lo siento, solamente se pueden subir archivos PDF o XML"); location.replace(document.referrer);</script>';

        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        $arrayClaves = array();
        $arrayCantidad = array();
        $arrayValorUnitario = array();
        $arrayPartida = array();

        if (move_uploaded_file($_FILES["filePDF"]["tmp_name"], $target_file_pdf)) {
            // echo '<script>alert("Descargando archivo"); location.replace(document.referrer);</script>';
            $arrayClaves = readPDF($target_file_pdf);
        } else {
            // echo '<script>alert("Lo siento, hubo un error al subir el archivo PDF"); location.replace(document.referrer);</script>';
        }

        if (move_uploaded_file($_FILES["fileXML"]["tmp_name"], $target_file_xml)) {
            //echo '<script>alert("Descargando archivo"); location.replace(document.referrer);</script>';
            $tmpArray = readXML($target_file_xml);
            $arrayCantidad = $tmpArray[0];  
            $arrayValorUnitario = $tmpArray[1];  
            $arrayPartida = $tmpArray[2];        
        } else {
            // echo '<script>alert("Lo siento, hubo un error al subir el archivo XML"); location.replace(document.referrer);</script>';
        }
        generateMod($file_name, $arrayClaves, $arrayCantidad, $arrayValorUnitario, $arrayPartida);
    }else{
        echo "Problems while converting";
    }

function generateMod($file_name,$arrayClaves, $arrayCantidad, $arrayValorUnitario, $arrayPartida){
    $file_name = substr($file_name, 0, -4);
    echo $file_name;
    $myfile = fopen($file_name.".mod", "w");
    $txt = "John Doe\n";
    fwrite($myfile, $txt);
    $txt = "Jane Doe\n";
    fwrite($myfile, $txt);
    fclose($myfile);
}

function readPDF($target_file_pdf){
    $parser = new \Smalot\PdfParser\Parser();
    $pdf    = $parser->parseFile($target_file_pdf);
    $pdfText = $pdf->getText();
    $pdfArray = explode(" ", $pdfText);
    $arrayClaves = array();

    for ($i=0; $i < count($pdfArray); $i++) { 
        $compareString = substr($pdfArray[$i], -3);
        if($compareString == ".00"){
            $firstLetter = $pdfArray[$i+1][0];
            if(!is_numeric($firstLetter)){
                // echo $pdfArray[$i+1];
                array_push($arrayClaves, $pdfArray[$i+1]);
                // echo "</br>-----</br>";
            }
        }
    }
    // echo count($arrayClaves);
    return $arrayClaves;
}

function readXML($target_file_xml){
    $xml = simplexml_load_file($target_file_xml); 
    echo $xml;
    $ns = $xml->getNamespaces(true);
    $xml->registerXPathNamespace('c', $ns['cfdi']);
    $xml->registerXPathNamespace('t', $ns['tfd']);
    
    $arrayGeneral = array();
    $arrayCantidad = array();
    $arrayValorUnitario = array();
    $arrayPartida = array();

    //Del PDF solamente se necesita la clave y va despues de PZ y un espacio vacio.
    //Voy a necesitar del XML: Valor Unitario, Cantidad, Num_Partida
    $numPartida = 1;

    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $Concepto){
        $valUnit = (float)$Concepto['ValorUnitario'];
        $tempValor = number_format($valUnit, 5, '.', '');
        array_push($arrayCantidad, $Concepto['Cantidad']);
        array_push($arrayValorUnitario, $tempValor);
        array_push($arrayPartida,str_pad($numPartida, 3, '0', STR_PAD_LEFT));
        $numPartida++;
    } 

    array_push($arrayGeneral, $arrayCantidad);
    array_push($arrayGeneral, $arrayValorUnitario);
    array_push($arrayGeneral, $arrayPartida);

    return $arrayGeneral;
}

?>