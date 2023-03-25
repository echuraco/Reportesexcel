<?php
//Base para receptar cualquier tipo de documento que se quiera imprimir;
//Realizado por Edison Churaco - Soporte glpi
//Grupo: Cabritos
session_start();
require_once  'modelos/ModGlpiTicket.php';
$id = $_GET['Id'];

    function InformacionTicket($id){
    $Ticket=ConsultasTickets::Tickets($id);
    $ConActores=ConsultasTickets::Actores($id);        
    $OrdSer= ConsultasTickets::Consulta($Ticket);
    $OrdSer["Actores"]=ConsultasTickets::Consulta($ConActores);
    $OrdSer["Elementos"]=ConsultasTickets::Consulta(ConsultasTickets::Elementos($id));
    $OrdSer["Origen"]=ConsultasTickets::Consulta(ConsultasTickets::Localizacion($OrdSer[0]->Ubicacion));
    $OrdSer["Documentos"]=ConsultasTickets::Consulta(ConsultasTickets::Documentos($id));
    $OrdSer["Categoria"]=ConsultasTickets::Consulta(ConsultasTickets::Categoria($OrdSer[0]->Categoria)); 
    $OrdSer["Solucion"]=ConsultasTickets::Consulta(ConsultasTickets::Solucion($id));
    $OrdSer["Historico"]=ConsultasTickets::Consulta(ConsultasTickets::Historico($id));
    $OrdSer["Cambio"]=ConsultasTickets::Consulta(ConsultasTickets::TicketsCambios($id));
                
    //var_dump($OrdSer);        
    return $OrdSer;
}

$listado=(InformacionTicket($id));
//Para ver resultado de consulta puede descomentar la siguiente linea de var_dump();
//var_dump(InformacionTicket($id));
//

if (!$listado   ) {
    echo "<h2>No existe el tickect: $id</h2>";
}else{
    $_SESSION["OS"]=$listado;
    include 'OrdenServicio.php';
}




?>