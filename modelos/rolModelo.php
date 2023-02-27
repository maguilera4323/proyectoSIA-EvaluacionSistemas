<?php
	
	require_once "mainModel.php";

	class rolModelo extends mainModel{

		protected static function agregar_rol_modelo($datos)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_insert_roles(?,?,?,?);");

			$sql->bindParam(1,$datos['nombre']);
			$sql->bindParam(2,$datos['desc']);
			$sql->bindParam(3,$datos['creado']);
			$sql->bindParam(4,$datos['fecha_crea']);
			$sql->execute();
			return $sql;								
		}

		protected static function actualizar_rol_modelo($datos,$id)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_update_roles(?,?,?,?,?);");

			$sql->bindParam(1,$datos['nombre']);
			$sql->bindParam(2,$datos['desc']);
			$sql->bindParam(3,$datos['modif']);
			$sql->bindParam(4,$datos['fecha_modif']);
			$sql->bindParam(5,$id);
			$sql->execute();
			return $sql;
		}

		 protected static function eliminar_rol_modelo($id){
				$sql=mainModel::conectar()->prepare("CALL proc_delete_roles(?);");
				$sql->bindParam(1,$id);
				$sql->execute();
				return $sql;
		}
	}