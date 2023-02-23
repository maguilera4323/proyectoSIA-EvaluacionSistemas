<?php
	
	require_once "mainModel.php";

	class recetarioModelo extends mainModel{

		/*--------- Modelo agregar recetario------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function agregar_recetario_modelo($datos)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_insert_recetario (?,?,?);");

			$sql->bindParam(1,$datos['id_producto']);
			$sql->bindParam(2,$datos['id_insumo']);
			$sql->bindParam(3,$datos['cant_insumo']);
			$sql->execute();
			return $sql;								
		}


		/*--------- Modelo actualizar recetario ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function actualizar_recetario_modelo($datos,$id)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_update_recetario (?,?,?,?);");

			$sql->bindParam(1,$datos['id_producto']);
			$sql->bindParam(2,$datos['id_insumo']);
			$sql->bindParam(3,$datos['cant_insumo']);
			$sql->bindParam(4,$id);
			$sql->execute();
			return $sql;
		}

		/*--------- Modelo eliminar recetario ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		 protected static function eliminar_recetario_modelo($id){
				$sql=mainModel::conectar()->prepare("CALL proc_delete_recetario (?);");
				$sql->bindParam(1,$id);
				$sql->execute();
				return $sql;
		}
	}