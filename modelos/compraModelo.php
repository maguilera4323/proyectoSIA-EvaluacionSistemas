<?php
	
	require_once "mainModel.php";

	class compraModelo extends mainModel{
		//Funciones para agregar una compra
		protected function agregar_compra_modelo($datos){
			$sql=mainModel::conectar()->prepare("CALL proc_insert_compras(?,?,?,?,?)");

			$sql->bindParam(1,$datos['prov']);
			$sql->bindParam(2,$datos['usuario']);
			$sql->bindParam(3,$datos['estado']);
			$sql->bindParam(4,$datos['fech_ent']);
			$sql->bindParam(5,$datos['total']);
			$sql->execute();
			return $sql;								
		}

		protected function agregar_detallecompra_modelo($datos){
			$sql=mainModel::conectar()->prepare("CALL proc_insert_detalle_compra(?,?,?,?,?,?)");

			$sql->bindParam(1,$datos['id_compra']);
			$sql->bindParam(2,$datos['ins']);
			$sql->bindParam(3,$datos['cant']);
			$sql->bindParam(4,$datos['prec']);
			$sql->bindParam(5,$datos['fech']);
			$sql->bindParam(6,$datos['estado']);
			$sql->execute();
			return $sql;						
		}


		//Funciones para actualizar una compra
		protected function actualizarCompraModelo($datos,$id){
			$sql=mainModel::conectar()->prepare("CALL proc_update_compras(?,?,?,?,?,?)");

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
			$sql=mainModel::conectar()->prepare("CALL proc_update_detalle_compra(?,?,?,?,?,?,?)");

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
			$sql=mainModel::conectar()->prepare("CALL proc_insert_movi_inventario(?,?,?,?,?,?)");

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
			$sql=mainModel::conectar()->prepare("CALL proc_anular_compra(?)");

			$sql->bindParam(1,$id);
			$sql->execute();
			return $sql;
		}


	//Funciones para actualizar el inventario 
	
		protected function sumarInventario($datos){
			$sql=mainModel::conectar()->prepare("CALL proc_sumar_inventario(?,?)" );

			$sql->bindParam(1,$datos['cantidad']);
			$sql->bindParam(2,$datos['id_insumo']);
			$sql->execute();
			return $sql;								
		}

		
		protected function restarInventario($datos){
			$sql=mainModel::conectar()->prepare("CALL proc_restar_inventario(?,?)");

			$sql->bindParam(1,$datos['cantidad']);
			$sql->bindParam(2,$datos['id_insumo']);
			$sql->execute();
			return $sql;								
		}
		
	}