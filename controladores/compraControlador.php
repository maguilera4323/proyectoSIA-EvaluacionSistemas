<?php
 if (session_status() == PHP_SESSION_NONE) {
	session_start();
} 

if($peticionAjax){
	require_once "../modelos/compraModelo.php";
	require_once "../pruebabitacora.php";
}else{
	require_once "./modelos/compraModelo.php";
	require_once "./pruebabitacora.php";
}


class compraControlador extends compraModelo{

	
	public function agregarCompra(){
		$proveedor=mainModel::limpiar_cadena($_POST['proveedor_compra']);
		$estado=mainModel::limpiar_cadena($_POST['estado_compra']);
		$fecha_entrega=mainModel::limpiar_cadena($_POST['fecha_compra']);
		$total=mainModel::limpiar_cadena($_POST['subTotal']);
		$usuario=mainModel::limpiar_cadena($_POST['userId']);
					
			//arreglo enviado al modelo para ser usado en una sentencia INSERT
			$datos_compra_reg=[
				"prov"=>$proveedor,
				"estado"=>$estado,
				"fech_ent"=>$fecha_entrega,
				"total"=>$total,
				"usuario"=>$usuario
			];

			$agregar_compra=compraModelo::agregar_compra_modelo($datos_compra_reg);
	} 

	public function agregarDetalleCompra(){
		for ($i = 0; $i <count($_POST['productName']); $i++) {
		$insumo=mainModel::limpiar_cadena($_POST['productName'][$i]);
		$cantidad=mainModel::limpiar_cadena($_POST['quantity'][$i]);
		$precio=mainModel::limpiar_cadena($_POST['price'][$i]);
		$fecha_cad=mainModel::limpiar_cadena($_POST['fechaCaducidad'][$i]);
		$id_compra=mainModel::limpiar_cadena($_POST['productCode']);
		$estado=mainModel::limpiar_cadena($_POST['estado_compra']);
				
 			//arreglo enviado al modelo para ser usado en una sentencia INSERT
			$datos_detallecompra_reg=[
				"ins"=>$insumo,
				"cant"=>$cantidad,
				"prec"=>$precio,
				"fech"=>$fecha_cad,
				"id_compra"=>$id_compra,
				"estado"=>$estado
			];

			$agregar_detalle_compra=compraModelo::agregar_detallecompra_modelo($datos_detallecompra_reg); 
	} 

		if($i>0){
			$datos_bitacora = [
				"id_objeto" => 0,
				"fecha" => date('Y-m-d h:i:s'),
				"id_usuario" => $_SESSION['id_login'],
				"accion" => "Compra Agregada",
				"descripcion" => "Se agrego una nueva compra en el sistema"
			];
			
			Bitacora::guardar_bitacora($datos_bitacora); 

			$alerta=[
				"Alerta"=>"redireccionar",
				"Titulo"=>"Compra Registrada",
				"Texto"=>"Los datos de la compra han sido registrados en el sistema",
				"Tipo"=>"success",
				"URL"=>'../compra-list/'
			];
			echo json_encode($alerta);
		
		}else{
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"No hemos podido guardar la compra",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
		}
		
	} 


	public function anularCompra(){
		$id_compra=mainModel::limpiar_cadena($_POST['id_compra_del']);
		$id_estadocompra=mainModel::limpiar_cadena($_POST['id_estado_del']);
		$id_proveedor=mainModel::limpiar_cadena($_POST['id_proveedor_del']);

		//se crea un arreglo para recibir todos los insumos y las cantidades adquiridas de los mismos
		$id_insumo = array();
		$cantidad= array();
		$cont = 0;
		//validación para comprobar que el estado de compra sea igual a Realizada
		//en caso afirmativo se realiza la actualizacion del inventario, restando los insumos de la compra seleccionada
		//y el registro de los insumos ingresados en los movimientos de inventario

		if($id_estadocompra==2){
		//select para obtener los datos de los insumos comprados desde el detalle de compraS
		$datosDetalleCompra=mainModel::ejecutar_consulta_simple("SELECT * FROM TBL_detalle_compra WHERE 
		id_compra='$id_compra'");
	
			foreach ($datosDetalleCompra as $fila){
				$id_insumo[$cont]=$fila['id_insumos'];
				$cantidad[$cont]=$fila['cantidad_comprada'];
				$cont++;
			}	
			
		//ciclo para guardar los datos de la compra a restar en el inventario
			for($j=0;$j<$cont;$j++){
				$datos_act_inventario=[
					"cantidad"=>$cantidad[$j],
					"id_insumo"=>$id_insumo[$j],
				];
		
		//objeto que guarda la informacion de la compra anulada que ira a la bitacora
				$datos_movi_inventario=[
					"cantidad"=>$cantidad[$j],
					"id_insumo"=>$id_insumo[$j],
					"usuario"=>$_SESSION['id_login'],
					"estado"=>2,
					"coment"=>'Salida de insumos por compra anulada',
					"fecha"=>date('Y-m-d H:i:s')
				];

				$restarInv=compraModelo::restarInventario($datos_act_inventario); 
				$insertarMovi=compraModelo::insertarMovimientoInventario($datos_movi_inventario); 
			}
			//se actualiza el estado de la compra a Anulado
			$actualizarCompra=compraModelo::actEstadoCompra($id_compra);


			//se guarda la operacion en bitacora y se envia un mensaje de respuesta siempre que el contador de insumos
			//sea mayor que cero
			if($j>0){
				$datos_bitacora = [
                    "id_objeto" => 0,
                    "fecha" => date('Y-m-d H:i:s'),
                    "id_usuario" => $_SESSION['id_login'],
                    "accion" => "Compra Anulada",
                    "descripcion" => "El usuario ".$_SESSION['usuario_login']." anuló una compra en el sistema"
                ];
				
				Bitacora::guardar_bitacora($datos_bitacora); 
	
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"Compra Anulada",
					"Texto"=>"Se anuló la compra seleccionada",
					"Tipo"=>"success"
				];
				echo json_encode($alerta);
			
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido anular la compra",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
			}
			

			


		}

	}

	
}