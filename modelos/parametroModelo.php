<?php
	
	require_once "mainModel.php";

	class parametroModelo extends mainModel{

		/*--------- Modelo agregar parametro ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function agregar_parametro_modelo($datos)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_insert_parametros(?,?,?,?,?);");

			$sql->bindParam(1,$datos['nombre']);
			$sql->bindParam(2,$datos['valor']);
			$sql->bindParam(3,$datos['id_usuario']);
			$sql->bindParam(4,$datos['creado']);
			$sql->bindParam(5,$datos['fecha_crea']);
			$sql->execute();
			return $sql;								
		}


		/*--------- Modelo actualizar parametro ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function actualizar_parametro_modelo($datos,$id)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_update_parametros(?,?,?,?,?,?);");
			$sql->bindParam(1,$datos['nombre']);
			$sql->bindParam(2,$datos['valor']);
			$sql->bindParam(3,$datos['id_usuario']);
			$sql->bindParam(4,$datos['modif']);
			$sql->bindParam(5,$datos['fecha_modif']);
			$sql->bindParam(6,$id);
			$sql->execute();
			return $sql;
		}

		/*--------- Modelo eliminar parametro ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		 protected static function eliminar_parametro_modelo($id){
				$sql=mainModel::conectar()->prepare("CALL proc_delete_parametros(?);");
				$sql->bindParam(1,$id);
				$sql->execute();
				return $sql;
		}
	}