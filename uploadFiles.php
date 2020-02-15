<?php
error_reporting(E_ALL); // Error engine - always E_ALL!

$target_dir = "uploads/";
$target_file_pdf = $target_dir . basename($_FILES["filePDF"]["name"]);
$target_file_xml = $target_dir . basename($_FILES["fileXML"]["name"]);
$uploadOk = 1;
$fileTypePDF = strtolower(pathinfo($target_file_pdf,PATHINFO_EXTENSION));
$fileTypeXML = strtolower(pathinfo($target_file_xml,PATHINFO_EXTENSION));

// // Check if PDF file already exists
// if (file_exists($target_file_pdf)) {
//     // echo '<script>alert("Lo siento, el archivo PDF ya existe en el servidor. Favor de renombrarlo"); location.replace(document.referrer);</script>';
//     echo 'Archivo PDF Ya existe';
//     $uploadOk = 0;
// }
// // Check if XML file already exists
// if (file_exists($target_file_xml)) {
//     // echo '<script>alert("Lo siento, el archivo XML ya existe en el servidor. Favor de renombrarlo"); location.replace(document.referrer);</script>';
//     echo 'Archivo XML Ya existe';
//     $uploadOk = 0;
// }
// // Check PDF file size
// if ($_FILES["filePDF"]["size"] > 500000) {
//     echo '<script>alert("Archivo PDF supera los 5MB."); location.replace(document.referrer);</script>';

//     $uploadOk = 0;
// }
// // Check XML file size
// if ($_FILES["fileXML"]["size"] > 500000) {
//     echo '<script>alert("Archivo XML supera los 5MB."); location.replace(document.referrer);</script>';

//     $uploadOk = 0;
// }
// // Allow certain file formats
// if($fileTypePDF != "pdf" && $fileTypePDF != "xml") {
//     echo '<script>alert("Lo siento, solamente se pueden subir archivos PDF o XML"); location.replace(document.referrer);</script>';

//     $uploadOk = 0;
// }
// // Check if $uploadOk is set to 0 by an error
// if ($uploadOk == 0) {
//     // echo '<script>alert("Lo siento, hubo un error al subir los archivos XML y PDF"); location.replace(document.referrer);</script>';
//     echo "Upload Flag = 0";
// // if everything is ok, try to upload file
// } else {
//     if (move_uploaded_file($_FILES["filePDF"]["tmp_name"], $target_file_pdf)) {
//         //echo '<script>alert("Descargando archivo"); location.replace(document.referrer);</script>';
//         echo "Exito PDF";
//     } else {
//         echo '<script>alert("Lo siento, hubo un error al subir el archivo PDF"); location.replace(document.referrer);</script>';
//     }
//     if (move_uploaded_file($_FILES["fileXML"]["tmp_name"], $target_file_xml)) {
//         //echo '<script>alert("Descargando archivo"); location.replace(document.referrer);</script>';
//         echo "Exito XML";
//         readXML($target_file_xml);
//     } else {
//         echo '<script>alert("Lo siento, hubo un error al subir el archivo XML"); location.replace(document.referrer);</script>';
//     }
// }

if (move_uploaded_file($_FILES["fileXML"]["tmp_name"], $target_file_xml)) {
    //echo '<script>alert("Descargando archivo"); location.replace(document.referrer);</script>';
    echo "Exito XML \n\n";
    // readXML($target_file_xml);

    echo "New method";

    readInvoice($target_file_xml);
    
    // $xf = file_get_contents($target_file_xml);
    // $xml = simplexml_load_string($xf);

    // $xml = simplexml_load_file($target_file_xml);
    // foreach ($xml->nodos->item as $elemento) 
    //     {
    //         echo $elemento;
    //     // echo "El título es" .$elemento->title. "<br>";
    //     // echo "El link es" .$elemento->description. "<br>";
    //     // echo "El description es" .$elemento->description. "<br>";
        
    //     // //saco los namespaces
    //     // $namespaces = $elemento->getNameSpaces(true);
    //     // $media = $elemento->children($namespaces['media']);
    //     // echo "El thumbnail es:" .$media->thumbnail."<br>";
    //     }

    // displayNode($xml, 0);
} else {
    echo '<script>alert("Lo siento, hubo un error al subir el archivo XML"); location.replace(document.referrer);</script>';
}

function readPDF($target_file_pdf){
    // Code to be executed

}

function readXML($target_file_xml){
    // Code to be executed
    $xml=simplexml_load_file($target_file_xml) or die("Error: Cannot create XML object");
    if ($xml === false) {
        echo "Failed loading XML: ";
        foreach(libxml_get_errors() as $error) {
            echo "<br>", $error->message;
        }
    } else {
        print_r($xml);
        $list = $xml->record;
    }
}





function displayNode($node, $offset) {

    if (is_object($node)) {
        $node = get_object_vars($node);
        foreach ($node as $key => $value) {
            echo str_repeat(" ", $offset) . "-" . $key . "\n";
            displayNode($value, $offset + 1);
        }
    } elseif (is_array($node)) {
        foreach ($node as $key => $value) {
            if (is_object($value))
                displayNode($value, $offset + 1);
            else
                echo str_repeat(" ", $offset) . "-" . $key . "\n";
        }
    }
}




function readInvoice($target_file_xml){
    $xml = simplexml_load_file($target_file_xml); 
    echo $xml;
    $ns = $xml->getNamespaces(true);
    $xml->registerXPathNamespace('c', $ns['cfdi']);
    $xml->registerXPathNamespace('t', $ns['tfd']);
    
    echo "Entre al metodo readInvoice";
    
    //EMPIEZO A LEER LA INFORMACION DEL CFDI E IMPRIMIRLA 
    foreach ($xml->xpath('//cfdi:Comprobante') as $cfdiComprobante){ 
        echo $cfdiComprobante['Version']; 
        echo "<br />"; 
        echo $cfdiComprobante['Fecha']; 
        echo "<br />"; 
        echo $cfdiComprobante['Sello']; 
        echo "<br />"; 
        echo $cfdiComprobante['total']; 
        echo "<br />"; 
        echo $cfdiComprobante['subTotal']; 
        echo "<br />"; 
        echo $cfdiComprobante['certificado']; 
        echo "<br />"; 
        echo $cfdiComprobante['formaDePago']; 
        echo "<br />"; 
        echo $cfdiComprobante['noCertificado']; 
        echo "<br />"; 
        echo $cfdiComprobante['tipoDeComprobante']; 
        echo "<br />"; 
    } 
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor') as $Emisor){ 
        echo $Emisor['rfc']; 
        echo "<br />"; 
        echo $Emisor['nombre']; 
        echo "<br />"; 
    } 
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:DomicilioFiscal') as $DomicilioFiscal){ 
        echo $DomicilioFiscal['pais']; 
        echo "<br />"; 
        echo $DomicilioFiscal['calle']; 
        echo "<br />"; 
        echo $DomicilioFiscal['estado']; 
        echo "<br />"; 
        echo $DomicilioFiscal['colonia']; 
        echo "<br />"; 
        echo $DomicilioFiscal['municipio']; 
        echo "<br />"; 
        echo $DomicilioFiscal['noExterior']; 
        echo "<br />"; 
        echo $DomicilioFiscal['codigoPostal']; 
        echo "<br />"; 
    } 
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:ExpedidoEn') as $ExpedidoEn){ 
        echo $ExpedidoEn['pais']; 
        echo "<br />"; 
        echo $ExpedidoEn['calle']; 
        echo "<br />"; 
        echo $ExpedidoEn['estado']; 
        echo "<br />"; 
        echo $ExpedidoEn['colonia']; 
        echo "<br />"; 
        echo $ExpedidoEn['noExterior']; 
        echo "<br />"; 
        echo $ExpedidoEn['codigoPostal']; 
        echo "<br />"; 
    } 
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Receptor') as $Receptor){ 
        echo $Receptor['rfc']; 
        echo "<br />"; 
        echo $Receptor['nombre']; 
        echo "<br />"; 
    } 
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Receptor//cfdi:Domicilio') as $ReceptorDomicilio){ 
        echo $ReceptorDomicilio['pais']; 
        echo "<br />"; 
        echo $ReceptorDomicilio['calle']; 
        echo "<br />"; 
        echo $ReceptorDomicilio['estado']; 
        echo "<br />"; 
        echo $ReceptorDomicilio['colonia']; 
        echo "<br />"; 
        echo $ReceptorDomicilio['municipio']; 
        echo "<br />"; 
        echo $ReceptorDomicilio['noExterior']; 
        echo "<br />"; 
        echo $ReceptorDomicilio['noInterior']; 
        echo "<br />"; 
        echo $ReceptorDomicilio['codigoPostal']; 
        echo "<br />"; 
    } 
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $Concepto){ 
        echo "<br />"; 
        echo $Concepto['unidad']; 
        echo "<br />"; 
        echo $Concepto['importe']; 
        echo "<br />"; 
        echo $Concepto['cantidad']; 
        echo "<br />"; 
        echo $Concepto['descripcion']; 
        echo "<br />"; 
        echo $Concepto['valorUnitario']; 
        echo "<br />";   
        echo "<br />"; 
    } 
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslados//cfdi:Traslado') as $Traslado){ 
        echo $Traslado['tasa']; 
        echo "<br />"; 
        echo $Traslado['importe']; 
        echo "<br />"; 
        echo $Traslado['impuesto']; 
        echo "<br />";   
        echo "<br />"; 
    } 
    
    //ESTA ULTIMA PARTE ES LA QUE GENERABA EL ERROR
    foreach ($xml->xpath('//t:TimbreFiscalDigital') as $tfd) {
        echo $tfd['selloCFD']; 
        echo "<br />"; 
        echo $tfd['FechaTimbrado']; 
        echo "<br />"; 
        echo $tfd['UUID']; 
        echo "<br />"; 
        echo $tfd['noCertificadoSAT']; 
        echo "<br />"; 
        echo $tfd['version']; 
        echo "<br />"; 
        echo $tfd['selloSAT']; 
    } 
}


?>