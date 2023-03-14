<?php
	
	require_once "mainModel.php";

	class productoModelo extends mainModel{

		/*--------- Modelo agregar producto ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function agregar_producto_modelo($datos)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_insert_productos(?,?,?,?,?)");

			$sql->bindParam(1,$datos['nombre']);
			$sql->bindParam(2,$datos['id_tipo_producto']);
			$sql->bindParam(3,$datos['descripcion']);
			$sql->bindParam(4,$datos['precio']);
			$sql->bindParam(5,$datos['foto']);
			$sql->execute();
			return $sql;

											
		}



		/*--------- Modelo actualizar producto ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function actualizar_producto_modelo($dato,$id)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_update_productos(?,?,?,?,?,?)");

			$sql->bindParam(1,$dato['nombre']);
			$sql->bindParam(2,$dato['id_tipo_producto']);	
			$sql->bindParam(3,$dato['descripcion']);			
			$sql->bindParam(4,$dato['precio']);			
			$sql->bindParam(5,$dato['foto']);
			$sql->bindParam(6,$id);
			$sql->execute();
			return $sql;
		}


		 protected static function eliminar_producto_modelo($id){
				$sql=mainModel::conectar()->prepare("call proc_delete_productos(?)");
				$sql->bindParam(1,$id);
				$sql->execute();
				return $sql;
		
		}
	}