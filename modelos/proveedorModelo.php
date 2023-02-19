<?php
	
	require_once "mainModel.php";

	class proveedorModelo extends mainModel{

		/*--------- Modelo agregar proveedor ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function agregar_proveedor_modelo($datos)
		{
			$sql=mainModel::conectar()->prepare("INSERT INTO tbl_Proveedores(nom_proveedor,rtn_proveedor,
			tel_proveedor,correo_proveedor,dir_proveedor)
			VALUES(?,?,?,?,?)");

			$sql->bindParam(1,$datos['nombre']);
			$sql->bindParam(2,$datos['rtn']);
			$sql->bindParam(3,$datos['telefono']);
			$sql->bindParam(4,$datos['correo']);
			$sql->bindParam(5,$datos['direccion']);
			$sql->execute();
			return $sql;

											
		}

		/*--------- Modelo actualizar proveedor ------ESTE ES EL QUE INTERACTUA DIRECTO CON LA BD---*/
		protected static function actualizar_proveedor_modelo($dato,$id)
		{
			$sql=mainModel::conectar()->prepare("UPDATE tbl_Proveedores SET nom_proveedor=?,rtn_proveedor=?,tel_proveedor=?,
			correo_proveedor=?, dir_proveedor=? WHERE id_Proveedores=?");

			$sql->bindParam(1,$dato['nombre']);
			$sql->bindParam(2,$dato['rtn']);	
			$sql->bindParam(3,$dato['telefono']);			
			$sql->bindParam(4,$dato['correo']);			
			$sql->bindParam(5,$dato['direccion']);
			$sql->bindParam(6,$id);
			$sql->execute();
			return $sql;
		}

		 protected static function eliminar_proveedor_modelo($accion,$id){
			if ($accion=='borrar'){
				$sql=mainModel::conectar()->prepare("DELETE FROM tbl_Proveedores where id_Proveedores=?");
				$sql->bindParam(1,$id);
				$sql->execute();
				return $sql;
			}
		}

		
	}