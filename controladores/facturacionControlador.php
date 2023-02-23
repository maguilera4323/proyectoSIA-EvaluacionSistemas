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
		//verificando que se hayan seleccionado productos en la factura
			if(($_POST['nombreProducto'])[0]!=''){
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

		//tercer insert, para la tabla de Promociones de Pedidos
		//el ciclo es para insertar todas las promociones agregadas al pedido
		//verificando que se hayan seleccionado promociones en la factura

			if(($_POST['nombrePromocion'][0])!=''){
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
			}

		//validacion de que existan uno o mas productos o promociones ingresadas en el sistema
			if($i>0){
				$datos_bitacora = [
					"id_objeto" => 0,
					"fecha" => date('Y-m-d h:i:s'),
					"id_usuario" => $_SESSION['id_login'],
					"accion" => "Pedido Agregado",
					"descripcion" => "El usuario ".$_SESSION['usuario_login']." agregó un pedido en el sistema"
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


		public function actualizarPedido(){
			$dni_cliente=mainModel::limpiar_cadena($_POST['dni_pedido_act']);
			$nombre_cliente=mainModel::limpiar_cadena($_POST['cliente_pedido_act']);
			$sitio_entrega=mainModel::limpiar_cadena($_POST['sitio_entrega_act']);	
			$fecha_ped=mainModel::limpiar_cadena($_POST['fecha_pedido_act']);
			$fecha_ent=mainModel::limpiar_cadena($_POST['fecha_entrega_act']);
			$estado=mainModel::limpiar_cadena($_POST['estado_pedido_act']);
			$subtotal=mainModel::limpiar_cadena($_POST['subTotal']);
			$montoISV=mainModel::limpiar_cadena($_POST['taxAmount']);
			$total=mainModel::limpiar_cadena($_POST['totalAftertax']);
			
			

		//primer update, para la tabla de Pedidos
		//se valida si se ha enviado el id de un producto
			for ($i = 0; $i <1; $i++){
				$id_pedido=mainModel::limpiar_cadena($_POST['id_act_pedido'][$i]);

				$datos_pedido_act=[
					"dni"=>$dni_cliente,
					"ncliente"=>$nombre_cliente,
					"sitio"=>$sitio_entrega,
					"fecha_p"=>$fecha_ped,
					"fecha_e"=>$fecha_ent,
					"fecha_f"=>date("Y-m-d H:i:s"),
					"est"=>$estado,
					"subtotal"=>$subtotal,
					"montoISV"=>$montoISV,
					"total"=>$total
				];

				$actualizarPedido=facturacionModelo::actPedidoModelo($datos_pedido_act,$id_pedido);
			}

		//segundo update, para la tabla de DetallePedidos
		//el ciclo for para actualizar los productos agregados a la venta
		if (isset($_POST['nombreProducto'])) {
			for ($i = 0; $i < count($_POST['nombreProducto']); $i++) {
				$producto=mainModel::limpiar_cadena($_POST['nombreProducto'][$i]);
				$cantidad=mainModel::limpiar_cadena($_POST['cantidad'][$i]);
				$precio=mainModel::limpiar_cadena($_POST['precio'][$i]);
				$id_pedido=mainModel::limpiar_cadena($_POST['id_act_pedido'][$i]);
				$id_detalle=mainModel::limpiar_cadena($_POST['id_act_detallepedido'][$i]);

				$datos_detallepedido_act=[
					"producto"=>$producto,
					"cantidad"=>$cantidad,
					"precio"=>$precio,
					"id_pedido"=>$id_pedido
				]; 

				$actualizarDetPedido=facturacionModelo::actDetPedidoModelo($datos_detallepedido_act,$id_detalle);

			//validación para revisar si el estado del pedido es Realizado
			//de ser afirmativo se procede a restar insumos del inventario segun los datos del recetario
			//y a registrar las salidas de inventario en Movimientos de Inventario
			//este codigo se enfoca a realizar los registros del inventario y movimientos de los productos
				if($estado==2){
					//se crea un arreglo para recibir todos los insumos y las respectivas cantidades de los mismos
					//estos arreglos se llenan de los datos recibidos del recetario
					$id_insumo = array();
					$cantidad_insumo= array();
					$cont = 0;
				
				//select para obtener los datos de los insumos que componen el producto vendido
				$datosRecetario=mainModel::ejecutar_consulta_simple("SELECT * FROM TBL_recetario WHERE id_producto='$producto'");
				foreach($datosRecetario as $fila){
					$id_insumo[$cont]=$fila['id_insumo'];
					$cantidad_insumo[$cont]=$fila['cant_insumo'];
					$cont++;
				}																																																											
			
				//ciclo que se encarga de actualizar el inventario, restando los insumos consumidos por cada producto
				//y de insertar en la tabla de movimientos de inventario la cantidad de insumos usados y el tipo de movimiento
					for($j=0;$j<$cont;$j++){

				//para poder enviar los datos de un arreglo dentro de un objeto se pasan a otra variable
						$cant_ins=$cantidad_insumo[$j];
						$id_ins=$id_insumo[$j];

					//esta operacion es la multiplicacion de la cantidad de insumo que contiene un producto
					//por la cantidad de productos comprados
					//por ejemplo si un cafe tiene 0.3 litros de agua esto se multiplica por la cantidad de cafes comprados
						$total_insumo=$cant_ins * $cantidad;

						$datos_inventario=[
							"cant_insumo"=>$total_insumo,
							"id_insumo"=>$id_ins
						];

						$actInventario=facturacionModelo::actInventarioPedido($datos_inventario);
						
						$datos_movi_inventario=[
							"cant_insumo"=>$total_insumo,
							"id_insumo"=>$id_ins,
							"estado"=>2,
							"fecha"=>date("Y-m-d H:i:s"),
							"usuario"=>$_SESSION['id_login'], 
							"coment"=>'Salida de insumos por producto'
						];

						$agregarMoviInventario=facturacionModelo::insertarMovimientoInventario($datos_movi_inventario);
					} 

				}
			}
		}


		//tercer update, para la tabla de PedidoPromocion
		//el ciclo for para actualizar las promociones agregadas a la venta
		if (isset($_POST['idPromocion'])) {
			for ($i = 0; $i < count($_POST['idPromocion']); $i++) {
				$promocion=mainModel::limpiar_cadena($_POST['idPromocion'][$i]);
				$cantidad_promocion=mainModel::limpiar_cadena($_POST['cantidadpromo'][$i]);
				$precio=mainModel::limpiar_cadena($_POST['preciopromo'][$i]);
				$id_pedido=mainModel::limpiar_cadena($_POST['id_act_pedido'][$i]);
				$id_detalle=mainModel::limpiar_cadena($_POST['id_prom_detalleprom'][$i]);

				$datos_detalleprom_act=[
					"promocion"=>$promocion,
					"cantidad"=>$cantidad_promocion,
					"precio"=>$precio,
					"id_pedido"=>$id_pedido
				]; 

				$actualizarDetPromocion=facturacionModelo::actDetPromocionModelo($datos_detalleprom_act,$id_detalle);

				//validación para revisar si el estado del pedido es Realizado
				//de ser afirmativo se procede a restar insumos del inventario segun los datos del recetario
				//y a registrar las salidas de inventario en Movimientos de Inventario
				//este codigo se enfoca a realizar los registros del inventario y movimientos de los productos
				if($estado==2){
					//se crea un arreglo para recibir todos los productos y las respectivas cantidades de los mismos
					//estos arreglos se llenan de los datos recibidos del la tabla de promocion de productos
					//la cual contiene los productos relacionados con una promocion en especifico
					$id_producto = array();
					$cantidad_producto= array();
					$cont = 0;
				

				//select para obtener los datos de los productos que componen una promoción
				$datosPromProducto=mainModel::ejecutar_consulta_simple("SELECT * FROM TBL_promociones_productos WHERE id_promociones='$promocion'");
				foreach($datosPromProducto as $fila){
					$id_producto[$cont]=$fila['id_producto'];
					$cantidad_producto[$cont]=$fila['cantidad'];
					$cont++;
				}

				//se crea un arreglo para recibir todos los insumos y las respectivas cantidades de los mismos
				//estos arreglos se llenan de los datos recibidos del recetario
					$id_insumo = array();
					$cantidad_insumo= array();
					$cont = 0;
				
				//se crea una variable llamada parametroProd, esta contiene el valor actual del producto
				//según el indice del arreglo
					$parametroProd=$id_producto[$i];
				

				//select para obtener los datos de los insumos que componen el producto vendido
				//usando los productos obtenidos de la tabla Promociones de productos
				$datosRecetario=mainModel::ejecutar_consulta_simple("SELECT * FROM TBL_recetario WHERE id_producto='$parametroProd'");
				foreach($datosRecetario as $fila){
					$id_insumo[$cont]=$fila['id_insumo'];
					$cantidad_insumo[$cont]=$fila['cant_insumo'];
					$cont++;
				}																																																											
			
				//ciclo que se encarga de actualizar el inventario, restando los insumos consumidos por cada producto
				//y de insertar en la tabla de movimientos de inventario la cantidad de insumos usados y el tipo de movimiento
					for($j=0;$j<$cont;$j++){
						//para poder enviar los datos de un arreglo dentro de un objeto se pasan a otra variable
						$cant_ins=$cantidad_insumo[$j];
						$cant_prod=$cantidad_producto[$i];
						$id_ins=$id_insumo[$j];

						//esta operacion implica tres variables
						//$cant_ins es la cantidad de insumos que compone cada producto
						//$cant_prod es la cantidad de productos que componen la promocion
						//$cant_promocion es la cantidad de promociones adquiridas
						$total_insumo=($cant_ins * $cantidad_promocion)*$cant_prod;

						$datos_inventario=[
							"cant_insumo"=>$total_insumo,
							"id_insumo"=>$id_ins
						];

						$actInventario=facturacionModelo::actInventarioPedido($datos_inventario);
						
						$datos_movi_inventario=[
							"cant_insumo"=>$total_insumo,
							"id_insumo"=>$id_ins,
							"estado"=>2,
							"fecha"=>date("Y-m-d H:i:s"),
							"usuario"=>$_SESSION['id_login'], 
							"coment"=>'Salida de insumos por promoción'
						];

						$agregarMoviInventario=facturacionModelo::insertarMovimientoInventario($datos_movi_inventario);
					} 

				}
			}
		}



		if($i>0){
			$datos_bitacora = [
				"id_objeto" => 0,
				"fecha" => date('Y-m-d h:i:s'),
				"id_usuario" => $_SESSION['id_login'],
				"accion" => "Pedido Actualizado",
				"descripcion" => "El usuario ".$_SESSION['usuario_login']." actualizó un pedido en el sistema"
			];
			
			Bitacora::guardar_bitacora($datos_bitacora); 

			$alerta=[
				"Alerta"=>"redireccionar",
				"Titulo"=>"Pedido Actualizado",
				"Texto"=>"Los datos del pedido han sido actualizados en el sistema",
				"Tipo"=>"success",
				"URL"=>'../facturacion-list/'
			];
			echo json_encode($alerta);
		
		}else{
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"No hemos podido actualizar el pedido",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
		}
		
	}

	
	public function anularPedido(){
		$id_pedido=mainModel::limpiar_cadena($_POST['id_pedido_del']);

		$anularPedido=facturacionModelo::anularPedidoModelo($id_pedido);

		if($anularPedido->rowCount()==1){
		
			$datos_bitacora = [
				"id_objeto" => 0,
				"fecha" => date('Y-m-d h:i:s'),
				"id_usuario" => $_SESSION['id_login'],
				"accion" => "Pedido Anulado",
				"descripcion" => "El usuario ".$_SESSION['usuario_login']." anuló un pedido en el sistema"
			];
			
			Bitacora::guardar_bitacora($datos_bitacora); 

			$alerta=[
				"Alerta"=>"recargar",
				"Titulo"=>"Pedido Anulado",
				"Texto"=>"El pedido seleccionado ha sido anulado",
				"Tipo"=>"success",
				"URL"=>'../facturacion-list/'
			];
			echo json_encode($alerta);
		
		}else{
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"No hemos podido anular el pedido",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
		}
	}
}



