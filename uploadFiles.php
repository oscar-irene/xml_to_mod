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

    readXML($target_file_xml);
    
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
    $xml = simplexml_load_file($target_file_xml); 
    echo $xml;
    $ns = $xml->getNamespaces(true);
    $xml->registerXPathNamespace('c', $ns['cfdi']);
    $xml->registerXPathNamespace('t', $ns['tfd']);
    
    echo "Entre al metodo readInvoice";
    
    //Del PDF solamente se necesita la clave y va despues de PZ y un espacio vacio.
    //Voy a necesitar del XML: Valor Unitario, Cantidad, Num_Partida
    $numPartida = 1;

    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $Concepto){ 
        echo "<br />"; 
        echo $Concepto['Cantidad']; 
        echo "<br />"; 
        echo $Concepto['ValorUnitario']; 
        echo "<br />";    
        echo str_pad($numPartida, 3, '0', STR_PAD_LEFT);;
        echo "<br />"; 
        $numPartida++;
    } 
}

?>