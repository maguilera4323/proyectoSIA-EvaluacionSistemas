<?php
	
	require_once "mainModel.php";

	class objetoModelo extends mainModel{

		/*--------- Modelo agregar OBJETO ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function agregar_objeto_modelo($datos)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_insert_objeto(?,?,?,?,?);");

			$sql->bindParam(1,$datos['nombre']);
			$sql->bindParam(2,$datos['desc']);
			$sql->bindParam(3,$datos['tipo']);
			$sql->bindParam(4,$datos['creado']);
			$sql->bindParam(5,$datos['fecha_crea']);
			$sql->execute();
			return $sql;

											
		}

		/*--------- Modelo actualizar OBJETO ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function actualizar_objeto_modelo($dato,$id)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_update_objetos(?,?,?,?,?,?);");

			$sql->bindParam(1,$dato['nombre']);
			$sql->bindParam(2,$dato['desc']);
			$sql->bindParam(3,$dato['tipo']);
			$sql->bindParam(4,$dato['modif']);
			$sql->bindParam(5,$dato['fecha_modif']);
			$sql->bindParam(6,$id);
			$sql->execute();
			return $sql;
		}



		protected static function datos_objeto_modelo($tipo,$id){
			if($tipo=='unico'){
				$sql=mainModel::conectar()->prepare("SELECT * FROM TBL_objetos where id_objeto=?");
				$sql->bindParam(1,$id);
			}
			$sql->execute();
			return $sql;
		}



		 protected static function eliminar_objeto_modelo($id){
				$sql=mainModel::conectar()->prepare("CALL proc_delete_objetos(?);");
				$sql->bindParam(1,$id);
				$sql->execute();
				return $sql;
		}

		
	}