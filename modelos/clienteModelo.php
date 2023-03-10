<?php
	
	require_once "mainModel.php";

	class clienteModelo extends mainModel{

		/*--------- Modelo agregar cliente ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function agregar_cliente_modelo($datos)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_insert_clientes(?,?,?,?);");

			$sql->bindParam(1,$datos['nombre']);
			$sql->bindParam(3,$datos['dni']);
			$sql->bindParam(2,$datos['rtn']);
			$sql->bindParam(4,$datos['tel']);
			$sql->execute();
			return $sql;

											
		}

		/*--------- Modelo actualizar CLIENTE ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function actualizar_cliente_modelo($dato,$id)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_update_clientes(?,?,?,?,?);");

			$sql->bindParam(1,$dato['nombre']);
			$sql->bindParam(3,$dato['dni']);
			$sql->bindParam(2,$dato['rtn']);
			$sql->bindParam(4,$dato['tel']);
			$sql->bindParam(5,$id);
			$sql->execute();
			return $sql;
		}



		protected static function datos_cliente_modelo($tipo,$id){
			if($tipo=='unico'){
				$sql=mainModel::conectar()->prepare("SELECT * FROM TBL_Clientes where id_clientes=?");
				$sql->bindParam(1,$id);
			}
			$sql->execute();
			return $sql;
		}



		 protected static function eliminar_cliente_modelo($accion,$id){
			if ($accion=='borrar'){
				$sql=mainModel::conectar()->prepare("CALL proc_delete_clientes(?);");
				$sql->bindParam(1,$id);
				$sql->execute();
				return $sql;
			}
		}

		
	}