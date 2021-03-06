<?php
/**
 * Archivo del controlador para modulo de Reporte de Cuadre con Banca
 * @package cRCuadre_banca.php
 * @author Brenda Batista B. - <Brebatista@gmail.com>
 * @copyright Grupo de empresas Voila
 * @license BSD License
 * @version v 1.0 Junio - 2013
 */

// Vista asignada
$obj_xtpl->assign_file('contenido', $obj_config->GetVar('ruta_vista').'RCuadre_banca'.$obj_config->GetVar('ext_vista'));

// Modelo asignado
require($obj_config->GetVar('ruta_modelo').'RCuadre_banca.php');

$obj_modelo= new RCuadre_banca($obj_conexion);
$obj_date= new Fecha();
$agencia=$obj_modelo->getAgencia();
$comisionarreglo=$obj_modelo->GetComision($agencia['id_agencia']);
$comision=$comisionarreglo['comision_agencia'];
$tipo_comision=$comisionarreglo['tipo_comision'];
require('./fpdf/fpdf.php');
switch (ACCION){
        // Ruta actual
    case 'listar_resultados':
        $_SESSION['Ruta_Lista']= $obj_generico->RutaRegreso();
        // Ruta regreso
        $obj_xtpl->assign('ruta_regreso', $_SESSION['Ruta_Form']);
        $fecha_desde= $obj_generico->CleanText($_GET['fechadesde']);
        $fecha_hasta= $obj_generico->CleanText($_GET['fechahasta']);
        $obj_xtpl->assign('fechadesde', $fecha_desde);
        $obj_xtpl->assign('fechahasta', $fecha_hasta);
       	$fecha_desde=$obj_date->changeFormatDateII($fecha_desde);
       	$fecha_hasta=$obj_date->changeFormatDateII($fecha_hasta);
        $i=0; $total_cuadre=0;
        $group_extra=0;
        if(isset($_GET['agencia']))
        if($_GET['agencia']!=''){
	        if($_GET['agencia']!=0)
	        $param_extra=" AND id_agencia=".$_GET['agencia'];
	        else
	        $group_extra=1;
        }
	    else
		$param_extra="";
	    if($group_extra){
	    	$result3=$obj_modelo->getAgencias();
	    	while($roww=$obj_conexion->GetArrayInfo($result3)){
	    		$cuadre_agencia=0;
	    		$sw=0;
	    		$param_extra=" AND id_agencia=".$roww['id_agencia'];
		        if( $result= $obj_modelo->GetBalance($fecha_desde, $fecha_hasta,$comision,$tipo_comision,$param_extra)){
		        	if ($obj_conexion->GetNumberRows($result)>0 ){
		                while($row= $obj_conexion->GetArrayInfo($result)){
		                    if( ($i % 2) >0){
		                        if ($row['balance']<0){
		                            $obj_xtpl->assign('estilo_fila', 'evenred');
		                        }else{
		                            $obj_xtpl->assign('estilo_fila', 'even');
		                        }
		                    }
		                    else{
		                         if ($row['balance']<0){
		                            $obj_xtpl->assign('estilo_fila', 'oddred');
		                         }else{
		                            $obj_xtpl->assign('estilo_fila', 'odd');
		                         }
		                    }
		                    $obj_xtpl->assign('fecha', $obj_date->changeFormatDateI($row['fecha'], 0));
		                    $obj_xtpl->assign('total_ventas', number_format($row['total_ventas'], 2) );
		                    $obj_xtpl->assign('comision', number_format($row['comision'],2));
		                    $obj_xtpl->assign('total_premiado', number_format($row['total_premiado'],2));
		                    $obj_xtpl->assign('balance', number_format($row['balance'],2));
		                    // Parseo del bloque de la fila
		                    $obj_xtpl->parse('main.contenido.lista_resultados.lista_max.lista');
		                    $total_cuadre= $total_cuadre + $row['balance'];
		                    $cuadre_agencia= $cuadre_agencia + $row['balance'];
		                    $i++;
		                    $sw=1;
		                }
		                 // Parseo del bloque de la fila
		              
		        	}else{
		                // Mensaje
		             //   echo "pasa";
		               // $obj_xtpl->assign('no_info',"No hay datos de la Agencia ".$roww['nombre_agencia']);
		               // $obj_xtpl->parse('main.contenido.lista_resultados.no_lista');
					}
		        }
		        
		        if($group_extra ){
		        	if($sw==1)
		        $obj_xtpl->assign('cuadre_agencia',"El cuadre de la Agencia es ".round($cuadre_agencia, 2));
		        	else
		        		$obj_xtpl->assign('cuadre_agencia',"No hay datos de la Agencia ".$roww['nombre_agencia']);
		        		
		        }
		        	
		        //echo "pasa".
				$obj_xtpl->assign('nombre_agencia',$roww['nombre_agencia']);
				$obj_xtpl->assign('id_agencia',$roww['id_agencia']);
				        
		        $obj_xtpl->parse('main.contenido.lista_resultados.lista_max');
		        
	    	}
	    	
	    	if($i>0)
	    	$obj_xtpl->assign('total_cuadre', ' El Total de Cuadre Bs. '.round($total_cuadre, 2));
	    	
	    	$obj_xtpl->parse('main.contenido.lista_resultados');
	    	
	   }
	   else
	   {
	   	if(!isset($param_extra))
	   		$param_extra='';
	   	$cuadre_agencia=0;
	   	 
	   //	if(empty($param_extra))
	   	//$param_extra=" AND id_agencia=".$roww['id_agencia'];
	   	if( $result= $obj_modelo->GetBalance($fecha_desde, $fecha_hasta,$comision,$tipo_comision,$param_extra)){
	   		//echo "PaSA";
	   		if ($obj_conexion->GetNumberRows($result)>0 ){
	   			while($row= $obj_conexion->GetArrayInfo($result)){
	   				
//	   				echo "KK";
	   				if( ($i % 2) >0){
	   					if ($row['balance']<0){
	   						$obj_xtpl->assign('estilo_fila', 'evenred');
	   					}else{
	   						$obj_xtpl->assign('estilo_fila', 'even');
	   					}
	   				}
	   				else{
	   					if ($row['balance']<0){
	   						$obj_xtpl->assign('estilo_fila', 'oddred');
	   					}else{
	   						$obj_xtpl->assign('estilo_fila', 'odd');
	   					}
	   				}
	   				$obj_xtpl->assign('fecha', $obj_date->changeFormatDateI($row['fecha'], 0));
	   				$obj_xtpl->assign('total_ventas', number_format($row['total_ventas'], 2) );
	   				$obj_xtpl->assign('comision', number_format($row['comision'],2));
	   				$obj_xtpl->assign('total_premiado', number_format($row['total_premiado'],2));
	   				$obj_xtpl->assign('balance', number_format($row['balance'],2));
	   				// Parseo del bloque de la fila
	   				$obj_xtpl->parse('main.contenido.lista_resultados.lista_max.lista');
	   				$total_cuadre= $total_cuadre + $row['balance'];
	   				$sw=1;
	   				$cuadre_agencia= $cuadre_agencia + $row['balance'];
	   				$i++;
	   			}
	   			// Parseo del bloque de la fila
	   			$obj_xtpl->assign('nombre_agencia',$agencia['nombre_agencia']);
	   			$obj_xtpl->assign('id_agencia',$agencia['id_agencia']);
	   	
	   			if($group_extra)
	   				$obj_xtpl->assign('cuadre_agencia',"El cuadre de la Agencia es ".round($cuadre_agencia, 2));
	   			$obj_xtpl->parse('main.contenido.lista_resultados.lista_max');
	   		}else{
	   			// Mensaje
	   			$obj_xtpl->assign('no_info',"No hay datos de la Agencia ".$roww['nombre_agencia']);
	   			$obj_xtpl->parse('main.contenido.lista_resultados.no_lista');
	   	
	   	
	   		}
	   	}
	   	
	   	if($sw==1)
	   		$obj_xtpl->assign('total_cuadre', ' El Total de Cuadre Bs. '.round($total_cuadre, 2));
	   	
	   	$obj_xtpl->parse('main.contenido.lista_resultados');
	   	
	   }
	   	
        break;

    case 'ver_resultados':

        // Creamos el PDF

   

        //Creación del objeto de la clase heredada
        $pdf=new FPDF();
        
        $pdf->AliasNbPages();
        
        //Primera página
        $pdf->AddPage();

        $fecha_desde= $obj_generico->CleanText($_GET['fechadesde']);
        $fecha_hasta= $obj_generico->CleanText($_GET['fechahasta']);
        
        $fecha_desde=$obj_date->changeFormatDateII($fecha_desde);
        $fecha_hasta=$obj_date->changeFormatDateII($fecha_hasta);
        
        
        
        // Imagen  de encabezado
        $pdf->Image("./images/banner4.jpg" , 10 ,0, 180 ,40  , "JPG" ,"");
        
        // Titulo del Reporte
            $pdf->SetFont('Arial','B',20);
            $pdf->SetY(45);
            $pdf->Cell(50,10,'Cuadre con Banca desde '.$obj_date->changeFormatDateI($fecha_desde, 0).' hasta '.$obj_date->changeFormatDateI($fecha_hasta, 0));


            
        // Configuracion de colores
            $pdf->SetY(60);
            $pdf->SetFillColor(224,235,255);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(128,0,0);
            $pdf->SetLineWidth(.3);
            $pdf->SetFont('','B');

        
         if( $result= $obj_modelo->GetBalance($fecha_desde, $fecha_hasta,$comision)){
            if ($obj_conexion->GetNumberRows($result)>0 ){
                // Establecemos la cabecera de la tabla
                $pdf->SetFont('Arial','B',10);
                $pdf->SetTextColor(128,0,0);
                $pdf->Cell(40,7,'Fecha',1,0,'C',true);
                $pdf->Cell(30,7,'Total Ventas',1,0,'C',true);
                $pdf->Cell(30,7,'Comision',1,0,'C',true);
                $pdf->Cell(30,7,'Total Premiados',1,0,'C',true);
                $pdf->Cell(30,7,'Balance',1,0,'C',true);

                $pdf->SetFont('Arial','',8);
               
              
                while($row= $obj_conexion->GetArrayInfo($result)){
                        
                        $pdf->Ln();
                        if ($row['balance']<0){
                          $pdf->SetTextColor(255,0,0);
                           $pdf->SetFont('Arial','B',8);
                        }else{
                            $pdf->SetTextColor(0);
                             $pdf->SetFont('Arial','',8);
                        }
                        
                        
                        $pdf->Cell(40,7,$obj_date->changeFormatDateI($row['fecha'], 0),1,0,'C');
                        $pdf->Cell(30,7,number_format($row['total_ventas'],2), 1,0,'C');
                        $pdf->Cell(30,7,number_format($row['comision'],2), 1,0,'C');
                        $pdf->Cell(30,7,number_format($row['total_premiado'],2), 1,0,'C');
                        $pdf->Cell(30,7,number_format($row['balance'],2), 1,0,'C');
                    
                }
                
            }else{  
                $pdf->SetFont('Arial','B',14);
                $pdf->SetTextColor(0);
                $pdf->SetY(80);
                $pdf->Cell(10,10,'No hay informacion');
            }

         }
  
        $pdf->Output();

        break;
        
    default:
    		$id_perfil = $_SESSION['id_perfil'];
            // Ruta actual
            $_SESSION['Ruta_Form']= $obj_generico->RutaRegreso();
            $obj_xtpl->assign('fecha', $obj_date->FechaHoy2());
            $ayer= date('d/m/Y', strtotime('-1 day')) ;
            if( date ( 'l' , strtotime($ayer ))=='Sunday')
            $ayer= date('d/m/Y', strtotime('-1 day')) ;
            $obj_xtpl->assign('ruta_ayer', $obj_generico->RutaRegreso()."&accion=listar_resultados&fechadesde=".$ayer."&fechahasta=".$ayer);
            // Parseo del bloque
            //echo "pasa1";
            if($id_perfil==1) // OJO Cableado el perfil 1 significa administrador
            {
            	$result=$obj_modelo->getAgencias();
            	//echo "pasa";
            	while($row=$obj_conexion->GetArrayInfo($result)){
            		$obj_xtpl->assign('id_agencia', $row['id_agencia']);
            		$obj_xtpl->assign('nombre_agencia', $row['nombre_agencia']);
            		$obj_xtpl->parse('main.contenido.buscar_tickets.id_agencia.lista_agencias');
            	};
            	$obj_xtpl->assign('id_agencia', 0);
            	$obj_xtpl->assign('nombre_agencia', 'Todas');
            	$obj_xtpl->parse('main.contenido.buscar_tickets.id_agencia.lista_agencias');
            	$obj_xtpl->parse('main.contenido.buscar_tickets.id_agencia');
            }
            $obj_xtpl->parse('main.contenido.buscar_tickets');
            break;
}
$obj_xtpl->parse('main.contenido');
?>