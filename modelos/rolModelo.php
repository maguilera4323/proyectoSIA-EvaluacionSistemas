<?php
	
	require_once "mainModel.php";

	class rolModelo extends mainModel{

		protected static function agregar_rol_modelo($datos)
		{
			$sql=mainModel::conectar()->prepare("INSERT INTO tbl_ms_roles(rol,descripcion,
			creado_por,fecha_creacion)
			VALUES(?,?,?,?)");

			$sql->bindParam(1,$datos['nombre']);
			$sql->bindParam(2,$datos['desc']);
			$sql->bindParam(3,$datos['creado']);
			$sql->bindParam(4,$datos['fecha_crea']);
			$sql->execute();
			return $sql;								
		}

		protected static function actualizar_rol_modelo($datos,$id)
		{
			$sql=mainModel::conectar()->prepare("UPDATE tbl_ms_roles SET rol=?,descripcion=?,
			modificado_por=?, fecha_modificacion=? WHERE id_rol=?");
			$sql->bindParam(1,$datos['nombre']);
			$sql->bindParam(2,$datos['desc']);
			$sql->bindParam(3,$datos['modif']);
			$sql->bindParam(4,$datos['fecha_modif']);
			$sql->bindParam(5,$id);
			$sql->execute();
			return $sql;
		}

		 protected static function eliminar_rol_modelo($id){
				$sql=mainModel::conectar()->prepare("DELETE FROM tbl_ms_roles where id_rol=?");
				$sql->bindParam(1,$id);
				$sql->execute();
				return $sql;
		}
	}