<?php
	
	require_once "mainModel.php";

	class clienteModelo extends mainModel{

		/*--------- Modelo agregar OBJETO ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function agregar_objeto_modelo($datos)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_insert_objeto(?,?,?,?,?,?,?);");

			$sql->bindParam(1,$datos['objeto']);
			$sql->bindParam(3,$datos['descripcion']);
			$sql->bindParam(2,$datos['tipo_objeto']);
			$sql->bindParam(4,$datos['creado_por']);
			$sql->bindParam(5,$datos['fecha_creacion']);
			$sql->bindParam(6,$datos['modificado_por']);
			$sql->bindParam(7,$datos['fecha_modificacion']);
			$sql->execute();
			return $sql;

											
		}

		/*--------- Modelo actualizar OBJETO ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function actualizar_objeto_modelo($dato,$id)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_update_objetos(?,?,?,?,?,?,?,?);");

			$sql->bindParam(1,$dato['objeto']);
			$sql->bindParam(3,$dato['descripcion']);
			$sql->bindParam(2,$dato['tipo_objeto']);
			$sql->bindParam(4,$dato['creado_por']);
			$sql->bindParam(5,$dato['fecha_creacion']);
			$sql->bindParam(6,$dato['modificado_por']);
			$sql->bindParam(7,$dato['fecha_modificacion']);
			$sql->bindParam(8,$id);
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



		 protected static function eliminar_objeto_modelo($accion,$id){
			if ($accion=='borrar'){
				$sql=mainModel::conectar()->prepare("CALL proc_delete_objetos(?);");
				$sql->bindParam(1,$id);
				$sql->execute();
				return $sql;
			}
		}

		
	}