<?php
	
	require_once "mainModel.php";

	class insumoModelo extends mainModel{

		/*--------- Modelo agregar proveedor ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function agregar_insumo_modelo($datos){
			$sql=mainModel::conectar()->prepare("CALL proc_insert_insumos (?,?,?,?,?);");

			$sql->bindParam(1,$datos['nombre'], PDO::PARAM_STR, 4000);
			$sql->bindParam(2,$datos['cat'], PDO::PARAM_STR, 4000);
			$sql->bindParam(3,$datos['cantmax'], PDO::PARAM_STR, 4000);
			$sql->bindParam(4,$datos['cantmin'], PDO::PARAM_STR, 4000);
			$sql->bindParam(5,$datos['unidad'], PDO::PARAM_STR, 4000);
			$sql->execute();
			return $sql;

											
		}

		protected static function agregar_inv_insumo_modelo($datos){
			$sql=mainModel::conectar()->prepare("CALL proc_insert_inventario (?);");
			$sql->bindParam(1,$datos['cant'], PDO::PARAM_STR, 4000);
			$sql->execute();
			return $sql;						
		}



		/*--------- Modelo actualizar proveedor ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function actualizar_insumo_modelo($datos,$id)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_update_insumos (?,?,?,?,?,?);");
			$sql->bindParam(1,$datos['nombre']);
			$sql->bindParam(2,$datos['cat']);
			$sql->bindParam(3,$datos['cantmax']);
			$sql->bindParam(4,$datos['cantmin']);
			$sql->bindParam(5,$datos['unidad']);
			$sql->bindParam(6,$id);
			$sql->execute();
			return $sql;
		}


		 protected static function eliminar_insumo_modelo($id){
				$sql=mainModel::conectar()->prepare("CALL proc_delete_insumos (?);");
				$sql->bindParam(1,$id);
				$sql->execute();
				return $sql;
		}
	}