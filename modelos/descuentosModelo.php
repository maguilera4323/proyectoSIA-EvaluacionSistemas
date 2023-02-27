<?php
	
	require_once "mainModel.php";

	class descuentosModelo extends mainModel{

		/*--------- Modelo agregar descuentos ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function agregar_descuento_modelo($datos)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_insert_descuentos(?,?);");

			$sql->bindParam(1,$datos['nombre']);
			$sql->bindParam(2,$datos['porc']);
			$sql->execute();
			return $sql;

											
		}



		/*--------- Modelo actualizar descuento ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function actualizar_descuento_modelo($dato,$id)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_update_descuentos(?,?,?);");

			$sql->bindParam(1,$dato['nombre']);
			$sql->bindParam(2,$dato['porc']);			
			$sql->bindParam(3,$id);
			$sql->execute();
			return $sql;

				/* VERIFICAR SU GUARDA DEL DATO EN BITACORA */
				$datos_bitacora = 
				[
					"id_objeto" => 0,
					"fecha" => date('Y-m-d h:i:s'),
					"id_tipo_producto" => null,
					"accion" => "Actualizacion de descuento",
					"descripcion" => "Registro de un nuevo descuento"
				];
				Bitacora::guardar_bitacora($datos_bitacora);
			
		}

		protected static function datos_descuentos_modelo($tipo,$id){
			if($tipo=='unico'){
				$sql=mainModel::conectar()->prepare("SELECT * FROM TBL_descuentos where id_descuentos=?");
				$sql->bindParam(1,$id);
			}
			$sql->execute();
			return $sql;
		}

		 protected static function eliminar_descuentos_modelo($accion,$id){
			
			if ($accion=='borrar'){
				$sql=mainModel::conectar()->prepare("CALL proc_delete_descuentos(?);");
				$sql->bindParam(1,$id);
				$sql->execute();
				return $sql;
			}
			
		
		
		}
	}