<?php
//Consultas MYSQL
//Clases de construccion de consultas");
//Realizado por Edison Churaco - Soporte glpi
//Grupo: Cabritos
require_once('ConBdd.php');
class ConsultasTickets{
    public static  function Tickets($id){
        //echo "Revisando script";
        $consulta ="SELECT t.id 'Id', t.name 'Nombre', t.itilcategories_id 'Categoria', t.type 'Tipo', 
                date_format(t.date_creation,'%d/%m/%Y')'Fcreacion',sec_to_time(solve_delay_stat )'Tsolucion',
                date_format(t.solvedate,'%d/%m/%Y')'Resuelto',sec_to_time(takeintoaccount_delay_stat) 'Tatencion',
                date_format(t.closedate,'%d/%m/%Y')'Cerrado',t.content 'Descripcion',
                t.locations_id 'Ubicacion',
                case 
                    when t.status =1 then 'Nuevo'
                    when t.status =2 then 'Escalado a T.I'
                    when t.status =3 then 'Asignado especialista'
                    when t.status =4 then 'En espera'
                    when t.status =5 then 'Resuelto'
                    when t.status =6 then 'Cerrado'
                end 'Estado'
                from glpi_tickets as t
                where  id=$id;";
        //echo $consulta;
        return ($consulta);
    }

    public static  function Actores($id){
        $consulta = " SELECT t.type 'Tipo', concat(u.realname ,'_', u.firstname) 'Especialista', comment 'Cargo'
        from glpi_tickets_users as t
        inner join glpi_users as u
        on t.users_id = u.id 
        where t.tickets_id=$id;";
        return $consulta;

    }

    public static function Elementos($id){
        $consulta="SELECT case itemtype
                when 'Peripheral' then (select  concat_ws(' ','Dispositivo','Id=',Id,'Nombre',name,'Lugar=',(select name from glpi_locations where id= e.locations_id),'Serie=',serial,'Auditor=',otherserial) 
                                        from glpi_peripherals as e where e.id=it.items_id)
                when 'Computer' then (select  concat_ws(' ','Computador','Id=',Id,'Nombre',name,'Lugar=',(select name from glpi_locations where id= e.locations_id) ,'Serie=',serial,'Auditor=',otherserial)  
                                        from glpi_computers as e where e.id=it.items_id)
                when 'Printer' then (select  concat_ws(' ','Impresora','Id=',Id,'Nombre',name,'Lugar=',(select name from glpi_locations where id= e.locations_id),'Serie=',serial,'Auditor=',otherserial)  
                                        from glpi_printers as e where e.id=it.items_id)
                when 'Monitor' then (select  concat_ws(' ','Monitor','Id=',Id,'Nombre',name,'Lugar=',(select name from glpi_locations where id= e.locations_id),'Serie=',serial,'Auditor=',otherserial)  
                                        from glpi_monitors as e where e.id=it.items_id)
                when 'Phone' then (select  concat_ws(' ','Telefono','Id=',Id,'Nombre',name,'Lugar=',(select name from glpi_locations where id= e.locations_id),'Serie=',serial,'Auditor=',otherserial)
                                        from glpi_phones as e where e.id=it.items_id)
                when 'Software' then (select concat_ws(' ','Software','Id=','Id','Nombre',name,'Lugar=',(select name from glpi_locations where id= e.locations_id),'Serie=','Sin serie','Auditor=','otherserial')
                                        from glpi_softwares as e where e.id=items_id)
                                        
                when 'NetworkEquipment' then (select concat_ws(' ','Equipo de red','Id=',Id,'Nombre',name,'Lugar=',(select name from glpi_locations where id= e.locations_id),'Serie=',serial,'Auditor=',otherserial)
                                        from glpi_networkequipments as e where e.id=items_id)
                end as 'Equipo'
                from glpi_items_tickets as it
                where tickets_id=$id;";
        return $consulta;
    }

    public static function Localizacion($id){
        $consulta ="SELECT l.name 'Nombre',l.completename 'Raiz' from  glpi_locations as l where id =$id";
        return $consulta;
    }

    public static function Documentos($id){
        $consulta="SELECT d.filename 'Nombre'
        from glpi_documents  d
        inner join glpi_documents_items i
        on i.documents_id = d.id
        where i.items_id=$id";
        return$consulta;
    }

    public static function Categoria($id){
        $consulta="SELECT c.name 'Nombre', c.completename 'Completo' from glpi_itilcategories as c where c.id = $id;";
        return $consulta;
    }

    public static function Solucion($id){
        $consulta="SELECT content  from glpi_itilsolutions where itemtype='Ticket' and items_id = $id;";
        return $consulta;
    }

    public static function Historico($id){
        $consulta="SELECT S.content 'Detalle', S.date_creation 'Modificado', S.date_mod 'Anexo',
        (select concat(U.realname,' ',U.firstname) from glpi_users as  U where U.id =S.users_id) 'Responsable' ,'Seguimiento' Tipo
        from glpi_itilfollowups as S 
        where  S.items_id=$id and S.itemtype ='Ticket'
        union
        select TT.content 'Detalle', TT.date_creation 'Modificado', TT.date_mod 'Anexo',
        (select concat(U.realname,' ',U.firstname) from glpi_users as  U where U.id =TT.users_id) 'Responsable','Tarea' Tipo
        from glpi_tickettasks as TT 
        where  TT.tickets_id=$id
        union
        select SO.content 'Detalle', SO.date_creation 'Modificado', SO.date_mod 'Anexo',
        (select concat(U.realname,' ',U.firstname) from glpi_users as  U where U.id =SO.users_id) 'Responsable','Solucion' Tipo
        from glpi_itilsolutions as SO
        where  SO.items_id=$id  and SO.itemtype='Ticket'
        Union
        select D.filename 'Detalle',D.date_creation'Modificado',D.filepath 'Anexo',
        (select concat(U.realname,' ',U.firstname) from glpi_users as  U where U.id =D.users_id) 'Responsable','Adjunto' Tipo
        from glpi_documents_items as I 
        Inner join glpi_documents as D
        on D.id =I.documents_id
        inner join glpi_users as U
        on D.users_id = U.id
        where I.itemtype like 'Ticket' and I.items_id =$id
        Union
        select D.filename 'Detalle',D.date_creation'Modificado',D.filepath 'Anexo',
        (select concat(U.realname,' ',U.firstname) from glpi_users as  U where U.id =D.users_id) 'Responsable','Adjunto' Tipo
        from glpi_tickettasks as TT
        inner join glpi_documents_items as DI
        on TT.id = DI.items_id and DI.itemtype='TicketTask'
        inner join glpi_documents as D
        on DI.documents_id = D.id 
        inner join glpi_users as U
        on D.users_id = U.id
        where TT.tickets_id=$id
        order by Modificado;";
        return $consulta;
    }
    
    public static function TicketsCambios($id)
    { $consulta="SELECT  T.id 'Id', T.name 'Titulo'
        from glpi_changes_tickets CT
        inner join glpi_tickets T
        on  CT.tickets_id = T.id
        where CT.tickets_id=$id;";
        return ($consulta);
    }

    public static function Consulta($consulta){
        //echo "preparando script";
        $stmt=Conexion::conectar()->prepare($consulta);
        //echo $stmt;
        if($stmt->execute()){
            //var_dump($stmt);           

		}else{

			//print_r(Conexion::conectar()->errorInfo());
            //echo "Conexion incorrecta";    

		}    
    
        return $stmt->fetchall(PDO::FETCH_CLASS);
    }
}