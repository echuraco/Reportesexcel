<?php
//Constructor de Ordenes de servicio 
//Utiliza bibliteca PhpOffice\PhpSpreadsheet
//Realizado por Edison Churaco - Soporte glpi
//Grupo: Cabritos
    use PhpOffice\PhpSpreadsheet\Calculation\Information\Value;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    require 'vendor/autoload.php';
    $OS=($_SESSION['OS']);
    $Extension='Xlsx';
    $Archivo='./Plantillas/OS.xlsx';
    $Solicitante="  ";
    $Especialista="  ";
    $Observador="  ";
    $Solucion="";
    $tTpo="";
    $Origen="";
    $Documento="";
    $Elemento="";

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($Archivo);
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->getCell('C2')->setValue($OS[0]->Nombre);
$worksheet->getCell('C4')->setValue("# - ". $OS[0]->Id);
foreach ($OS["Actores"] as $key => $value) {
    if($value->Tipo==1){$Solicitante=$Solicitante.$value->Especialista."\n".$value->Cargo."\n";}
    if($value->Tipo==2){$Especialista=$Especialista.$value->Especialista."\n".$value->Cargo."\n";}
    if($value->Tipo==3){$Observador=$Observador.$value->Especialista."\n".$value->Cargo."\n";}
}
$worksheet->getCell('C5')->setValue($Especialista);
$worksheet->getCell('B33')->setValue($Especialista);
$worksheet->getCell('D33')->setValue($Solicitante);
$worksheet->getCell('F33')->setValue($Observador);
$worksheet->getCell('B9')->setValue($OS[0]->Fcreacion);
$worksheet->getCell('C9')->setValue(date("d-m-Y", time()));
$worksheet->getCell('D9')->setValue($OS[0]->Resuelto);
$worksheet->getCell('E9')->setValue($OS[0]->Resuelto);
$worksheet->getCell('F9')->setValue($OS[0]->Tsolucion);
$worksheet->getCell('G9')->setValue($OS[0]->Tatencion);
if ($OS[0]->Tipo==1){$Tipo="Incidente";}
if ($OS[0]->Tipo==2){$Tipo="Requerimiento";}


foreach($OS["Solucion"]as $key2 => $value2){
    $Solucion=(htmlspecialchars_decode($value2->content));
}

$worksheet->getCell('B12')->setValue(strip_tags($Solucion));

foreach($OS["Categoria"]as $key3 => $value3){
    $Categoria=(htmlspecialchars_decode($value3->Completo));
}
$worksheet->getCell('F4')->setValue(strip_tags($Tipo));
$worksheet->getCell('F5')->setValue(strip_tags($Categoria));

foreach($OS["Origen"]as $key4 => $value4){
    $Origen=(htmlspecialchars_decode($value4->Raiz));
}
$worksheet->getCell('C6')->setValue(strip_tags($Origen));

foreach($OS["Documentos"]as $key5 => $value5){
    $Documento=$Documento.(htmlspecialchars_decode($value5->Nombre)."\n");
}
$worksheet->getCell('B37')->setValue(strip_tags($Documento));

foreach($OS["Elementos"]as $key6 => $value6){
    $Elemento=$Elemento.(htmlspecialchars_decode($value6->Equipo))."\n";
}
$worksheet->getCell('B30')->setValue(strip_tags($Elemento));
$filename="OS_".$OS[0]->Id.".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
$objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
ob_end_clean();
$objWriter->save('php://output');
?>