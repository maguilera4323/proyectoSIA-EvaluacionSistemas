<?php
	
	require_once "mainModel.php";

	class facturacionModelo extends mainModel{

		//Funciones para agregar un pedido
		protected function agregarPedidoModelo($datos,$isv){
			$sql=mainModel::conectar()->prepare("INSERT INTO TBL_pedidos(nom_cliente,dni_cliente, num_factura, fech_pedido,
			fech_entrega,sitio_entrega, id_estado_pedido,sub_total, isv, total,id_forma_pago,fech_facturacion,porcentaje_isv)
			VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");

			$sql->bindParam(1,$datos['ncliente']);
			$sql->bindParam(2,$datos['dni']);
			$sql->bindParam(3,$datos['nfac']);
			$sql->bindParam(4,$datos['fecha_p']);
			$sql->bindParam(5,$datos['fecha_e']);
			$sql->bindParam(6,$datos['sitio']);
			$sql->bindParam(7,$datos['est']);
			$sql->bindParam(8,$datos['subtotal']);
			$sql->bindParam(9,$datos['montoISV']);
			$sql->bindParam(10,$datos['total']);
			$sql->bindParam(11,$datos['fpago']);
			$sql->bindParam(12,$datos['fecha_f']);
			$sql->bindParam(13,$isv);
			$sql->execute();
			return $sql;								
		}


		protected function actualizarNumCAI($dato){
			$sql=mainModel::conectar()->prepare("UPDATE TBL_talonario_cai SET cai_actual=?");

			$sql->bindParam(1,$dato);
			$sql->execute();
			return $sql;	
		}


		protected function agregarDescuentoModelo($id_descuento, $montoDescuento,$id_pedido){
			$sql=mainModel::conectar()->prepare("INSERT INTO TBL_pedido_descuentos(id_descuentos,id_pedidos,total_descontado)
			 VALUES(?,?,?)");

			$sql->bindParam(1,$id_descuento);
			$sql->bindParam(2,$id_pedido);
			$sql->bindParam(3,$montoDescuento);
			$sql->execute();
			return $sql;
		}

		protected function agregar_detallecompra_modelo($datos){
			$sql=mainModel::conectar()->prepare("INSERT INTO TBL_detalle_compra(id_compra,id_insumos,cantidad_comprada,
			precio_costo,fecha_caducidad, estado_compra) VALUES(?,?,?,?,?,?)");

			$sql->bindParam(1,$datos['id_compra']);
			$sql->bindParam(2,$datos['ins']);
			$sql->bindParam(3,$datos['cant']);
			$sql->bindParam(4,$datos['prec']);
			$sql->bindParam(5,$datos['fech']);
			$sql->bindParam(6,$datos['estado']);
			$sql->execute();
			return $sql;						
		}


		protected function agregarDetallePedModelo($datos,$id){
			$sql=mainModel::conectar()->prepare("INSERT INTO TBL_detalle_pedido(id_pedido,id_producto,cantidad, precio_venta)
			 VALUES(?,?,?,?)");

			$sql->bindParam(1,$id);
			$sql->bindParam(2,$datos['producto']);
			$sql->bindParam(3,$datos['cantidad']);
			$sql->bindParam(4,$datos['precio']);
			$sql->execute();
			return $sql;
		}


		protected function agregarDetallePromModelo($datos,$id){
			$sql=mainModel::conectar()->prepare("INSERT INTO TBL_pedidos_promociones(id_promocion, id_pedido,cantidad,
			 precio_venta)VALUES(?,?,?,?)");

			$sql->bindParam(1,$datos['promocion']);
			$sql->bindParam(2,$id);
			$sql->bindParam(3,$datos['cantidad']);
			$sql->bindParam(4,$datos['precio']);
			$sql->execute();
			return $sql;
		}


		//Funciones para actualizar una compra
		protected function actualizarCompraModelo($datos,$id){
			$sql=mainModel::conectar()->prepare("UPDATE TBL_compras set id_proveedor=?, id_usuario=?,
			id_estado_compra=?,fech_compra=?, total_compra=? where id_compra=?");

			$sql->bindParam(1,$datos['prov']);
			$sql->bindParam(2,$datos['usuario']);
			$sql->bindParam(3,$datos['estado']);
			$sql->bindParam(4,$datos['fech_ent']);
			$sql->bindParam(5,$datos['total']);
			$sql->bindParam(6,$id);
			$sql->execute();
			return $sql;								
		}

		protected function actDetalleCompraModelo($datos,$id){
			$sql=mainModel::conectar()->prepare("UPDATE TBL_detalle_compra set id_compra=?, id_insumos=?,
			cantidad_comprada=?,precio_costo=?, fecha_caducidad=?, estado_compra=? where id_detalle_compra=?");

			$sql->bindParam(1,$datos['id_compra']);
			$sql->bindParam(2,$datos['ins']);
			$sql->bindParam(3,$datos['cant']);
			$sql->bindParam(4,$datos['prec']);
			$sql->bindParam(5,$datos['fech']);
			$sql->bindParam(6,$datos['estado']);
			$sql->bindParam(7,$id);
			$sql->execute();
			return $sql;								
		}


		

	//Funciones para anular una compra del sistema

		protected function insertarMovimientoInventario($datos){
			$sql=mainModel::conectar()->prepare("INSERT INTO TBL_movi_inventario (id_insumos, cant_movimiento, tipo_movimiento,
			 fecha_movimiento,id_usuario,comentario) values (?,?,?,?,?,?)");

			$sql->bindParam(1,$datos['id_insumo']);
			$sql->bindParam(2,$datos['cantidad']);
			$sql->bindParam(3,$datos['estado']);
			$sql->bindParam(4,$datos['fecha']);
			$sql->bindParam(5,$datos['usuario']);
			$sql->bindParam(6,$datos['coment']);
			$sql->execute();
			return $sql;
		}

		protected function actEstadoCompra($id){
			$sql=mainModel::conectar()->prepare("UPDATE TBL_compras set id_estado_compra=3 where id_compra=?");

			$sql->bindParam(1,$id);
			$sql->execute();
			return $sql;
		}


	//Funciones para actualizar el inventario 
	
		protected function sumarInventario($datos){
			$sql=mainModel::conectar()->prepare("UPDATE TBL_inventario set cant_existencia=cant_existencia + ?
			where id_insumo=?");

			$sql->bindParam(1,$datos['cantidad']);
			$sql->bindParam(2,$datos['id_insumo']);
			$sql->execute();
			return $sql;								
		}

		
		protected function restarInventario($datos){
			$sql=mainModel::conectar()->prepare("UPDATE TBL_inventario set cant_existencia=cant_existencia - ?
			where id_insumo=?");

			$sql->bindParam(1,$datos['cantidad']);
			$sql->bindParam(2,$datos['id_insumo']);
			$sql->execute();
			return $sql;								
		}
		
	}