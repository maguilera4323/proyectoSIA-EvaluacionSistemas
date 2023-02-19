<?php
	
	require_once "mainModel.php";

	class objetoModelo extends mainModel{

		protected static function agregar_objeto_modelo($datos){
			$sql=mainModel::conectar()->prepare("INSERT INTO tbl_objetos(objeto,descripcion,
			tipo_objeto,creado_por,fecha_creacion)
			VALUES(?,?,?,?,?)");
			$sql->bindParam(1,$datos['nombre']);
			$sql->bindParam(2,$datos['desc']);
			$sql->bindParam(3,$datos['tipo']);
			$sql->bindParam(4,$datos['creado']);
			$sql->bindParam(5,$datos['fecha_crea']);
			$sql->execute();
			return $sql;

											
		}


		protected static function actualizar_objeto_modelo($datos,$id){
			$sql=mainModel::conectar()->prepare("UPDATE tbl_objetos SET objeto=?,descripcion=?,tipo_objeto=?,
			modificado_por=?, fecha_modificacion=? WHERE id_objeto=?");
			$sql->bindParam(1,$datos['nombre']);
			$sql->bindParam(2,$datos['desc']);
			$sql->bindParam(3,$datos['tipo']);
			$sql->bindParam(4,$datos['modif']);
			$sql->bindParam(5,$datos['fecha_modif']);
			$sql->bindParam(6,$id);
			$sql->execute();
			return $sql;
		}


		 protected static function eliminar_objeto_modelo($id){
				$sql=mainModel::conectar()->prepare("DELETE FROM tbl_objetos where id_objeto=?");
				$sql->bindParam(1,$id);
				$sql->execute();
				return $sql;
		}
	}