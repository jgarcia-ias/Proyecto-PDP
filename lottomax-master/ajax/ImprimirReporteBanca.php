<html>
   <head><title>Impresion de Reporte Banca</title>
<!--   
   <script language="javascript" type="text/javascript" src="../jscripts/jquery-latest.js"></script>
   <script language="javascript" type="text/javascript" src="../jscripts/PluginPrint.js"></script>        
   <script language="javascript" type="text/javascript" src="../jscripts/DefaultPrinter.js"></script>    
   </head>
   <body id="content" bgcolor="#FFF380">
   
   <applet name="jzebra" code="jzebra.PrintApplet.class" archive="../jscripts/jzebra.jar" width="50px" height="50px">
	  <param name="printer" value="zebra">
   </applet>
-->
  
<?php
date_default_timezone_set("America/Caracas");

// Archivo de variables de configuracion
require_once('../config/config.php');
$obj_config= new ConfigVars();

// Archivo de mensajes
require_once('.'.$obj_config->GetVar('ruta_config').'mensajes.php');

// Clase Generica
require('.'.$obj_config->GetVar('ruta_libreria').'Generica.php');
$obj_generico= new Generica();

// Clase Date
require('.'.$obj_config->GetVar('ruta_libreria').'Fecha.php');
$obj_date= new Fecha();

// Conexion a la bases de datos
require('.'.$obj_config->GetVar('ruta_libreria').'Bd.php');
$obj_conexion= new Bd();
if( !$obj_conexion->ConnectDataBase($obj_config->GetVar('host'), $obj_config->GetVar('data_base'), $obj_config->GetVar('usuario_db'), $obj_config->GetVar('clave_db')) ){
	echo "sin_conexion_bd";
}

// Modelo asignado
require('.'.$obj_config->GetVar('ruta_modelo').'RCuadre_banca.php');

$obj_modelo= new RCuadre_banca($obj_conexion);
$agencia=$obj_modelo->getAgencia();

session_start();

//$id_taquilla=2;
// Obtenemos los datos de la taquilla
$id_taquilla= $obj_modelo->GetIdTaquilla();

$string=date('Y-m-d H:i:s');
$year = substr($string,0,4);
$month = substr($string,5,2);
$day = substr($string,8,2);
$hour = substr($string,11,2);
$minute = substr($string,14,2);
$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado");
//$fecha_hora=$day."-".$month."-".$year." ".$hour.":".$minute;
$fecha_hora=$day."-".$month."-".$year;

if($hour > 11){
	$formato="PM";
}else{
	$formato="AM";
}
$formato_militar= array("13","14","15","16","17","18","19","20","21","22","23","24");
$formato_civil= array("01","02","03","04","05","06","07","08","09","10","11","12");

$hour= str_replace($formato_militar,$formato_civil,$hour);
$hora=$hour.":".$minute." ".$formato;

$fecha_desde= $obj_generico->CleanText($_GET['fechadesde']);
$fecha_hasta= $obj_generico->CleanText($_GET['fechahasta']);

$fecha_desde=$obj_date->changeFormatDateII($fecha_desde);
$fecha_hasta=$obj_date->changeFormatDateII($fecha_hasta);
$comisionarreglo=$obj_modelo->GetComision($agencia['id_agencia']);
$comision=$comisionarreglo['comision_agencia'];
$tipo_comision=$comisionarreglo['tipo_comision'];
//echo $fecha_desde, $fecha_hasta;
$param_extra=" AND id_agencia=".$agencia['id_agencia'];

$data="";
         if( $result= $obj_modelo->GetBalance($fecha_desde, $fecha_hasta,$comision,$tipo_comision,$param_extra)){
            if ($obj_conexion->GetNumberRows($result)>0 ){
            	
				// ENCABEZADO DEL TICKET
				
            	$data.=" <table width='100%' border='0' ><tr><td colspan='3' align='center'><font face='arial' size='2' >";
            	$data.=$dias[date('w')]." ".$fecha_hora."&nbsp;&nbsp;".$hora;
            	$data.="<br>";
            	$data.="<br>";
            	$data.=" SISTEMA LOTTOMAX";
            	$data.=" </font></td> </tr>";

            	$data.="<tr height='2'><td colspan='2' align='center'></td></tr>";
            	 
            	$data.="<tr> <td colspan='2' align='center'><font face='arial' size='2' >";
            	$data.="Cuadre con Banca";
            	$data.="</font></td> </tr>";

            	$data.="<tr height='10'><td colspan='2' align='center'></td></tr>";
            	 
            	
				/*$data.="SISTEMA LOTTOMAX";
				$data.="<br>";
				$data.="CUADRE CON BANCA";
				$data.="<br>";
 	*/
            	//Cambio de tamano fuenta a 10 cpi
			/*	$data1="\\x1B\\x50";
				$data1.="SISTEMA LOTTOMAX";
				$data1.="\\n";
				$data1.="CUADRE CON BANCA";
				$data1.="\\n";										            
              */

            	$total_venta=0;
            	$total_comision=0;
            	$total_premios=0;
            	$total_balance=0;
                while($row= $obj_conexion->GetArrayInfo($result)){
                        
					
                	$data.="<tr><td width='50%' align='left'> <font face='Times News Roman' size='2' > Fecha: </font></td> ";
                	$data.="<td width='50%' align='center'><font face='Times' size='2' > ".$obj_date->changeFormatDateI($row['fecha'],0)."</font></td></tr>";
                	
                	$data.="<tr><td width='50%' align='left' > <font face='times' size='2' > Total de Ventas: </font></td> ";
                	$data.="<td width='50%' align='center'><font face='times' size='2' > ".round($row['total_ventas'], 2)." Bs.</font></td></tr>";
                	$total_venta= $total_venta + round($row['total_ventas'], 2);

                	$data.="<tr><td width='50%' align='left'> <font face='times' size='2' > Comisi&oacute;n: </font></td> ";
                	$data.="<td width='50%' align='center'><font face='times' size='2' > ".round($row['comision'], 2)." Bs.</font></td></tr>";
                	$total_comision= $total_comision + round($row['comision'], 2);

                	$data.="<tr><td width='50%' align='left'> <font face='times' size='2' > Total Premios: </font></td> ";
                	$data.="<td width='50%' align='center'><font face='times' size='2' > ".round($row['total_premiado'], 2)." Bs.</font></td></tr>";
                	$total_premios= $total_premios + round($row['total_premiado'], 2);
                	
                	
                	 
                	$data.="<tr><td width='40' align='left'> <font face='times' size='2' > Balance: </font></td> ";
                	$data.="<td width='50%' align='center'><font face='times' size='4' > ".round($row['balance'], 2)." Bs.</font></td></tr>";
                	$total_balance= $total_balance + round($row['balance'], 2);
					
                	$data.="<tr height='2'><td colspan='2' align='center'>&nbsp;</td></tr>";
					/*$data1.="\\nFECHA: ".$row['fecha'];
					$data1.="\\nTotal Ventas: ".$row['total_ventas'];					                									
					$data1.="\\nComision: ".$row['comision'];
					$data1.="\\nTotal Premios: ".$row['total_premiado'];					                									
					$data1.="\\nBalance: ".$row['balance'];
					$data1.="\\n";
					$data1.="-----------------------------";
					$data1.="\\n";					
				      */                  
                    
                }
                $data.="<tr height='10'><td colspan='2' align='center'>===========================</td></tr>";
                //$data.="<tr height='10'><td colspan='2' align='left'>Total Venta: ".$total_venta." Bs:</td></tr>";
                //$data.="<tr height='10'><td colspan='2' align='left'>Total Comision: ".$total_comision." Bs:</td></tr>";
                //$data.="<tr height='10'><td colspan='2' align='left'>Total Premios: ".$total_premios." Bs:</td></tr>";
                //$data.="<tr height='10'><td colspan='2' align='left'>Total Balance: ".$total_balance." Bs:</td></tr>";
                //echo $data;               
            } 

         }
         $data.="</table>";
// Obtenemos los datos de la taquilla
$ida_taquilla= $obj_modelo->GetIdTaquillabyNumero($id_taquilla);
//Determinar si va a imprimir incompletos y Agotados
$info_impresora= $obj_modelo->GetDatosImpresora($ida_taquilla);

$lineas_saltar_despues=$info_impresora["lineas_saltar_despues"];

//Saltos de linea para hacer FEED
for($i=1;$i<=$lineas_saltar_despues;$i++){
	//$data1.="\\x1B\\x0A";
	//$data1.="\\n";
	$data.=".<br>";
}

//echo $data1;
/*
echo "<script type='text/javascript'>";
echo "print('".$data1."')";
echo "</script>";

echo "<script language='javascript'>setTimeout('self.close();',5000)</script>"
*/

echo $data;
?>


<script type="text/javascript"> 
window.print();
</script>
<script language='javascript'>setTimeout('self.close();',5000)</script>
   </body>
</html> 