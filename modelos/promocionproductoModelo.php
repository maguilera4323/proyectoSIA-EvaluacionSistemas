<?php
	
	require_once "mainModel.php";

	class promocionproductoModelo extends mainModel{

		/*--------- Modelo agregar promocionproducto------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function agregar_promocionproducto_modelo($datos)
		{
			$$sql=mainModel::conectar()->prepare("CALL proc_insert_promo_produ(?;");

			$sql->bindParam(1,$datos['cantidad']);
			$sql->execute();
			return $sql;								
		}


		/*--------- Modelo actualizar promocionproducto------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function actualizar_promocionproducto_modelo($datos,$id)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_update_promo_produ(?);");

			$sql->bindParam(1,$datos['pregunta']);
			$sql->bindParam(6,$id);
			$sql->execute();
			return $sql;
		}

		/*--------- Modelo eliminar promocionproducto ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		 protected static function eliminar_promocionproducto_modelo($id){
				$sql=mainModel::conectar()->prepare("DELETE FROM TBL_promociones_productos where id_promociones_productos=?");
				$sql->bindParam(1,$id);
				$sql->execute();
				return $sql;
		}
	}