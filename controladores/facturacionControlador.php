<?php
 if (session_status() == PHP_SESSION_NONE) {
	session_start();
} 

if($peticionAjax){
	require_once "../modelos/facturacionModelo.php";
	require_once "../pruebabitacora.php";
}else{
	require_once "./modelos/facturacionModelo.php";
	require_once "./pruebabitacora.php";
}

class facturacionControlador extends facturacionModelo{
	
	//función para crear y guardar una factura
	public function agregarPedido(){
		$dni_cliente=mainModel::limpiar_cadena($_POST['dni_pedido_nuevo']);
		$num_factura=mainModel::limpiar_cadena($_POST['num_factura_nuevo']);
		$forma_pago=mainModel::limpiar_cadena($_POST['forma_pago_nuevo']);
		$nombre_cliente=mainModel::limpiar_cadena($_POST['cliente_pedido_nuevo']);
		$sitio_entrega=mainModel::limpiar_cadena($_POST['sitio_entrega_nuevo']);	
		$fecha_fact=mainModel::limpiar_cadena($_POST['fecha_nuevo']);
		$fecha_ped=mainModel::limpiar_cadena($_POST['fecha_pedido_nuevo']);
		$fecha_ent=mainModel::limpiar_cadena($_POST['fecha_entrega_nuevo']);
		$estado=1;
		$subtotal=mainModel::limpiar_cadena($_POST['subTotal']);
		$montoISV=mainModel::limpiar_cadena($_POST['taxAmount']);
		$total=mainModel::limpiar_cadena($_POST['totalAftertax']);
		$id_descuento=mainModel::limpiar_cadena($_POST['nombredescuento_1']);
		

		$montoDescuento=mainModel::limpiar_cadena($_POST['montodescuento']);
		$id_pedido=mainModel::limpiar_cadena($_POST['numPedido_nuevo']);
		
		$valorISV=mainModel::ejecutar_consulta_simple("SELECT valor FROM TBL_ms_parametros WHERE id_parametro=17");
		foreach($valorISV as $fila){
			$isv=$fila['valor']/100;
		}

		//arreglo enviado al modelo para ser usado en una sentencia INSERT
		$datos_pedido_reg=[
			"dni"=>$dni_cliente,
			"nfac"=>$num_factura,
			"fpago"=>$forma_pago,
			"ncliente"=>$nombre_cliente,
			"sitio"=>$sitio_entrega,
			"fecha_f"=>$fecha_fact,
			"fecha_p"=>$fecha_ped,
			"fecha_e"=>$fecha_ent,
			"est"=>$estado,
			"subtotal"=>$subtotal,
			"montoISV"=>$montoISV,
			"total"=>$total
		];

		$agregarPedido=facturacionModelo::agregarPedidoModelo($datos_pedido_reg,$isv);
		$actualizarCAI=facturacionModelo::actualizarNumCAI($num_factura);

		if($montoDescuento!=''){
			$montoDescuento=$subtotal-$montoDescuento;
			$agregarDescuento=facturacionModelo::agregarDescuentoModelo($id_descuento,$montoDescuento,$id_pedido);
		}

		//segundo insert, para la tabla de Detalle Pedidos
		//el ciclo es para insertar todos los productos agregados al pedido
		if(isset($_POST['nombreProducto'])){
			for ($i = 0; $i < count($_POST['nombreProducto']); $i++) {
				$producto=mainModel::limpiar_cadena($_POST['nombreProducto'][$i]);
				$cantidad=mainModel::limpiar_cadena($_POST['cantidad'][$i]);
				$precio=mainModel::limpiar_cadena($_POST['precio'][$i]);

				$datos_detallepedido_reg=[
					"producto"=>$producto,
					"cantidad"=>$cantidad,
					"precio"=>$precio,
				];

				$agregarDetallePedido=facturacionModelo::agregarDetallePedModelo($datos_detallepedido_reg,$id_pedido);
	
			} 
		}


		if(isset($_POST['nombrePromocion'])){
			for ($i = 0; $i < count($_POST['nombrePromocion']); $i++) {
				$promocion=mainModel::limpiar_cadena($_POST['nombrePromocion'][$i]);
				$cantidad=mainModel::limpiar_cadena($_POST['cantidadpromo'][$i]);
				$precio=mainModel::limpiar_cadena($_POST['preciopromo'][$i]);

				$datos_detalleprom_reg=[
					"promocion"=>$promocion,
					"cantidad"=>$cantidad,
					"precio"=>$precio,
				];

				$agregarDetalleProm=facturacionModelo::agregarDetallePromModelo($datos_detalleprom_reg,$id_pedido);
			} 


			if($i>0){
				$datos_bitacora = [
					"id_objeto" => 0,
					"fecha" => date('Y-m-d h:i:s'),
					"id_usuario" => $_SESSION['id_login'],
					"accion" => "Pedido Agregado",
					"descripcion" => "Se agrego un nuevo pedido en el sistema"
				];
				
				Bitacora::guardar_bitacora($datos_bitacora); 

				$alerta=[
					"Alerta"=>"redireccionar",
					"Titulo"=>"Pedido Registrado",
					"Texto"=>"Los datos del pedido han sido registrados en el sistema",
					"Tipo"=>"success",
					"URL"=>'../facturacion-list/'
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
	}
}



