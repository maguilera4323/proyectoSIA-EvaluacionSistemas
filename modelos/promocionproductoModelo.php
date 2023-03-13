<?php
	
	require_once "mainModel.php";

	class promocionproductoModelo extends mainModel{

		/*--------- Modelo agregar promocionproducto------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function agregar_promocionproducto_modelo($datos)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_insert_promo_produ(?,?,?);");

			$sql->bindParam(1,$datos['id_promociones']);
			$sql->bindParam(2,$datos['id_producto']);
			$sql->bindParam(3,$datos['cantidad']);
			$sql->execute();
			return $sql;								
		}


		/*--------- Modelo actualizar promocionproducto------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function actualizar_promocionproducto_modelo($datos,$id)
		{
			$sql=mainModel::conectar()->prepare("CALL proc_update_promo_produ(?,?,?,?);");

			$sql->bindParam(1,$datos['id_promociones']);
			$sql->bindParam(2,$datos['id_producto']);
			$sql->bindParam(3,$datos['cantidad']);
			$sql->bindParam(4,$id);
			$sql->execute();
			return $sql;
		}

		/*--------- Modelo eliminar promocionproducto ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		 protected static function eliminar_promocionproducto_modelo($id){
				$sql=mainModel::conectar()->prepare("CALL proc_delete_promo_produ(?);");
				$sql->bindParam(1,$id);
				$sql->execute();
				return $sql;
		}
	}