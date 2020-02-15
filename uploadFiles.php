<?php
    error_reporting(E_ALL); // Error engine - always E_ALL!

    include 'vendor/autoload.php';
    $num_almacen = $_POST["almacen"];
    $cliente = $_POST["cliente"];
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
        
        generateModXML($cliente, $num_almacen, $file_name, $arrayClaves, $arrayCantidad, $arrayValorUnitario, $arrayPartida);
        
        $file_to_download = "uploads/".substr($file_name, 0, -4).".mod";
        downloadFile($file_to_download);
        
    }else{
        echo "Problems while converting";
    }
function downloadFile($file_to_download){
    $file_path = $file_to_download;
    header("Content-Type: application/octet-stream");
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"" . basename($file_to_download) . "\"");
    readfile($file_to_download);
}
function generateMod($cliente, $num_almacen,$file_name,$arrayClaves, $arrayCantidad, $arrayValorUnitario, $arrayPartida){
    $file_name = substr($file_name, 0, -4);
    echo $file_name;
    $myfile = fopen("uploads/".$file_name.".mod", "w");

    fwrite($myfile,"[Cabeza]\n");
    fwrite($myfile,"RPROVEE=.\n");
    fwrite($myfile,"PROVEE=    ".$cliente."\n");
    fwrite($myfile,"ENTRE=.\n");
    fwrite($myfile,"ALMA=".$num_almacen."\n");
    fwrite($myfile,"IMPU=10\n");
    fwrite($myfile,"DESC=.\n");
    fwrite($myfile,"DESCFIN=.\n");
    fwrite($myfile,"OBSDOC=.\n");
    fwrite($myfile,"MONEDA=1\n");
    fwrite($myfile,"TIPCAM=        1.00000\n");
    fwrite($myfile,"FLETE=.\n");

    for ($i=0; $i < count($arrayCantidad); $i++) { 
        $cantidad = $arrayCantidad[$i];
        $producto = $arrayClaves[$i];
        $partida = $arrayPartida[$i];
        $costo = $arrayValorUnitario[$i];

        fwrite($myfile,"[Partida" . $partida . "]\n");
        fwrite($myfile,'CANTI=             ' . $cantidad . "\n");
        fwrite($myfile,"PROD=" . $producto . "\n");
        fwrite($myfile,"DESC 1=.". "\n");
        fwrite($myfile,"ESQ_IMP=10". "\n");
        fwrite($myfile,"IMPU 1=.". "\n");
        fwrite($myfile,"IMPU 2=.". "\n");
        fwrite($myfile,"IMPU 3=.". "\n");
        fwrite($myfile,"IMPU 4=16.000". "\n");
        fwrite($myfile,"COSTO=             " . $costo. "\n");
        fwrite($myfile,"UNIDAD=pz". "\n");
        fwrite($myfile,"FACTUNI=      1.000". "\n");
        fwrite($myfile,"OBSPAR=.". "\n");
    }

    fclose($myfile);
}

function generateModXML($cliente, $num_almacen, $file_name,$arrayClaves, $arrayCantidad, $arrayValorUnitario, $arrayPartida){
    $file_name = substr($file_name, 0, -4);
    $myfile = fopen("uploads/".$file_name.".mod", "w");
    $refer = substr($file_name, -11);
    fwrite($myfile,'<?xml version="1.0" encoding="UTF-8"?>');
    fwrite($myfile,'<DATAPACKET Version="2.0">');
    fwrite($myfile,"<METADATA>");
    fwrite($myfile,'<FIELDS>');
    fwrite($myfile,'<FIELD attrname="CVE_CLPV" fieldtype="string" WIDTH="10" />');
    fwrite($myfile,'<FIELD attrname="NUM_ALMA" fieldtype="i4" />');
    fwrite($myfile,'<FIELD attrname="CVE_PEDI" fieldtype="string" WIDTH="20" />');
    fwrite($myfile,'<FIELD attrname="ESQUEMA" fieldtype="i4" />');
    fwrite($myfile,'<FIELD attrname="DES_TOT" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="DES_FIN" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="CVE_VEND" fieldtype="string" WIDTH="5" />');
    fwrite($myfile,'<FIELD attrname="COM_TOT" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="NUM_MONED" fieldtype="i4" />');
    fwrite($myfile,'<FIELD attrname="TIPCAMB" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="STR_OBS" fieldtype="string" WIDTH="255" />');
    fwrite($myfile,'<FIELD attrname="ENTREGA" fieldtype="string" WIDTH="25" />');
    fwrite($myfile,'<FIELD attrname="SU_REFER" fieldtype="string" WIDTH="20" />');
    fwrite($myfile,'<FIELD attrname="TOT_IND" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="MODULO" fieldtype="string" WIDTH="4" />');
    fwrite($myfile,'<FIELD attrname="CONDICION" fieldtype="string" WIDTH="25" />');
    fwrite($myfile,'<FIELD attrname="dtfield" fieldtype="nested">');
    fwrite($myfile,'<FIELDS>');
    fwrite($myfile,'<FIELD attrname="CANT" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="CVE_ART" fieldtype="string" WIDTH="20" />');
    fwrite($myfile,'<FIELD attrname="DESC1" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="DESC2" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="DESC3" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="IMPU1" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="IMPU2" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="IMPU3" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="IMPU4" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="COMI" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="PREC" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="NUM_ALM" fieldtype="i4" />');
    fwrite($myfile,'<FIELD attrname="STR_OBS" fieldtype="string" WIDTH="255" />');
    fwrite($myfile,'<FIELD attrname="REG_GPOPROD" fieldtype="i4" />');
    fwrite($myfile,'<FIELD attrname="REG_KITPROD" fieldtype="i4" />');
    fwrite($myfile,'<FIELD attrname="NUM_REG" fieldtype="i4" />');
    fwrite($myfile,'<FIELD attrname="COSTO" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="TIPO_PROD" fieldtype="string" WIDTH="1" />');
    fwrite($myfile,'<FIELD attrname="TIPO_ELEM" fieldtype="string" WIDTH="1" />');
    fwrite($myfile,'<FIELD attrname="MINDIRECTO" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="TIP_CAM" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="FACT_CONV" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="UNI_VENTA" fieldtype="string" WIDTH="10" />');
    fwrite($myfile,'<FIELD attrname="IMP1APLA" fieldtype="i4" />');
    fwrite($myfile,'<FIELD attrname="IMP2APLA" fieldtype="i4" />');
    fwrite($myfile,'<FIELD attrname="IMP3APLA" fieldtype="i4" />');
    fwrite($myfile,'<FIELD attrname="IMP4APLA" fieldtype="i4" />');
    fwrite($myfile,'<FIELD attrname="PREC_SINREDO" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="COST_SINREDO" fieldtype="r8" />');
    fwrite($myfile,'<FIELD attrname="LOTE" fieldtype="string" WIDTH="16" />');
    fwrite($myfile,'<FIELD attrname="PEDIMENTO" fieldtype="string" WIDTH="16" />');
    fwrite($myfile,'<FIELD attrname="FECHCADUC" fieldtype="dateTime" />');
    fwrite($myfile,'<FIELD attrname="FECHADUANA" fieldtype="dateTime" />');
    fwrite($myfile,'</FIELDS>');
    fwrite($myfile,'<PARAMS />');
    fwrite($myfile,'</FIELD>');
    fwrite($myfile,'</FIELDS>');
    fwrite($myfile,'<PARAMS />');
    fwrite($myfile,'</METADATA>');
    fwrite($myfile,'<ROWDATA>');
    $clienteAlign = str_pad($cliente, 10, " ", STR_PAD_LEFT);
    
    fwrite($myfile,'<ROW CVE_CLPV="'.$clienteAlign.'" NUM_ALMA="'.$num_almacen.'" ESQUEMA="1" DES_TOT="0" DES_FIN="0" NUM_MONED="1" TIPCAMB="1" STR_OBS="" ENTREGA="" SU_REFER="'.$refer.'" TOT_IND="0" MODULO="COMP">');

    fwrite($myfile,'<dtfield>');

    for ($i=0; $i < count($arrayCantidad); $i++) { 
        $cantidad = $arrayCantidad[$i];
        $clave = $arrayClaves[$i];
        $partida = $arrayPartida[$i];
        $costo = $arrayValorUnitario[$i];

        fwrite($myfile,'<ROWdtfield CANT="'.$cantidad.'" CVE_ART="'.$clave.'" DESC1="0" IMPU1="0" IMPU2="0" IMPU3="0" IMPU4="16" PREC="0" NUM_ALM="'.$num_almacen.'" STR_OBS="" REG_GPOPROD="0" COSTO="'.$costo.'" TIPO_PROD="P" TIPO_ELEM="N" MINDIRECTO="0" TIP_CAM="1" FACT_CONV="1" UNI_VENTA="pz" IMP1APLA="4" IMP2APLA="4" IMP3APLA="4" IMP4APLA="0" PREC_SINREDO="0" COST_SINREDO="'.$costo.'" />');
    }

    fwrite($myfile,'</dtfield>');
    fwrite($myfile,'</ROW>');
    fwrite($myfile,'</ROWDATA>');
    fwrite($myfile,'</DATAPACKET>');

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
        $tempValor = number_format($valUnit, 2, '.', '');
        $lastDigits = substr($tempValor, -3);

        if($lastDigits == ".00"){
            $tempValor = intval($tempValor);
        }

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