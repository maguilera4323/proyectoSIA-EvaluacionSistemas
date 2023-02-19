<?php
 if (session_status() == PHP_SESSION_NONE) {
	session_start();
} 

if($peticionAjax){
	require_once "../modelos/TipoproductoModelo.php";
	require_once "../pruebabitacora.php";
}else{
	require_once "./modelos/TipoproductoModelo.php";
	require_once "./pruebabitacora.php";//aqui se ejecuta dentro del index y no se utiliza Ajax
}


class TipoproductoControlador extends TipoproductoModelo
{

	/*--------- Controlador agregar Tipo de producto ---------*/
	public function agregar_Tipo_producto_controlador()
	{
		$Tipo_producto=mainModel::limpiar_cadena(strtoupper($_POST['tipo_producto_nuevo']));
		$Tamaño_producto=mainModel::limpiar_cadena(strtoupper($_POST['tamaño_producto_nuevo']));
		$Clasificacion=mainModel::limpiar_cadena($_POST['clasificacion_producto_nuevo']);
 
					
			/*== AGREGAR TIPO DE PRODUCTO ==*/
			$datos_Tipo_producto_reg=[
				"tipo"=>$Tipo_producto,
				"tamaño"=>$Tamaño_producto,
				"clasificacion"=>$Clasificacion,
			];

			$agregar_Tipo_producto=TipoproductoModelo::agregar_Tipo_producto_modelo($datos_Tipo_producto_reg);

			if($agregar_Tipo_producto->rowCount()==1){
				$alerta=[
					"Alerta"=>"limpiar",
					"Titulo"=>" Tipo Producto registrado",
					"Texto"=>"Los datos del Tipo producto han sido registrados con exito",
					"Tipo"=>"success"
				];

				
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido registrar el Tipo de Producto",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);

			$datos_bitacora = [
				"id_objeto" => 0,
				"fecha" => date('Y-m-d h:i:s'),
				"id_tipo_producto" => $_SESSION['id_login'],
				"accion" => "Agregar Tipo de Producto",
				"descripcion" => "Se agrego un nuevo Tipo de producto en el sistema"
			];
			Bitacora::guardar_bitacora($datos_bitacora); 
	} /* Fin controlador */


	/*--------- Controlador actualizar tipo de producto ---------*/
	public function actualizar_Tipo_producto_controlador()
	{	
		$id_actualizar=mainModel::limpiar_cadena($_POST['id_actualizacion']);
		$Tipo_producto=mainModel::limpiar_cadena($_POST['tipo_producto_actu']);
		$Tamaño_producto=mainModel::limpiar_cadena($_POST['tamaño_producto_actu']);
		$Clasificacion=mainModel::limpiar_cadena($_POST['clasificacion_producto_actu']);
		
		if($Tipo_producto=="" || $Tamaño_producto=="" || $Clasificacion==""){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"No se han llenado todos los campos que son obligatorios",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}


			/*== Verificando integridad de los datos ==*/
			if(mainModel::verificar_datos("[A-ZÁÉÍÓÚÑ ]{1,30}",$Tipo_producto)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El Tipo de Producto no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			if(mainModel::verificar_datos("[A-ZÁÉÍÓÚÑ ]{1,30}",$Tamaño_producto)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El Tamaño del tipo de producto no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			
			if(mainModel::verificar_datos("[A-ZÁÉÍÓÚÑ ]{1,30}",$Clasificacion)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"La Clasificacion no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

	
			/*== ACTUALIZAR TIPO PRODUCTO ==*/
		$datos_Tipo_producto_actu=
			[
				"tipo"=>$Tipo_producto,
				"tamaño"=>$Tamaño_producto,
				"clasificacion"=>$Clasificacion
			];

			$actualizar_Tipo_producto=TipoproductoModelo::actualizar_Tipo_producto_modelo($datos_Tipo_producto_actu,$id_actualizar);

			if($actualizar_Tipo_producto->rowCount()==1)
			{
				$alerta=[
					"Alerta"=>"limpiar",
					"Titulo"=>"Tipo de Producto Actualizado",
					"Texto"=>"Tipo de Producto actualizado exitosamente",
					"Tipo"=>"success"
				];
			}else
			{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido actualizar el tipo de producto",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);

			$datos_bitacora = [
				"id_objeto" => 0,
				"fecha" => date('Y-m-d h:i:s'),
				"id_tipo_producto" => $_SESSION['id_login'],
				"accion" => "Modificación del Tipo de Producto",
				"descripcion" => "Se actualizó un Tipo de Producto en el sistema"
			];
			Bitacora::guardar_bitacora($datos_bitacora); 
	} /* Fin controlador */
	

	public function datosTipoproductoControlador($tipo,$id){
		$tipo=mainModel::limpiar_cadena($tipo);
		$id=mainModel::limpiar_cadena($id);

		return TipoproductoModelo::datos_Tipo_producto_modelo($tipo,$id);
	}

	

		//funcion para eliminar un Tipo de producto
		public function eliminarTipoProducto()
		{
			$id=mainModel::limpiar_cadena(($_POST['id_tipo_producto_del']));
			$Tipo_producto=mainModel::limpiar_cadena(($_POST['tipo_producto_del']));
			$array=array();
			$valor='';

		

		//verifica que el usuario si exista en el sistema
		$check_proveedor=mainModel::ejecutar_consulta_simple("SELECT id_tipo_producto FROM tbl_tipo_producto
		WHERE id_tipo_Producto='$id'");
		if($check_Tipo_producto->rowCount()<=0){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ha ocurrido un error",
				"Texto"=>"El Tipo de Producto seleccionado no existe",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		
		$eliminarTipoproducto=TipoproductoModelo::eliminar_Tipo_producto_modelo("borrar",$id);
			if($eliminarTipoproducto->rowCount()==1){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"Usuario Borrado",
					"Texto"=>"El Tipo de Producto fue borrado",
					"Tipo"=>"success"
				];
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ha ocurrido un error",
					"Texto"=>"El Tipo de Producto no pudo ser borrado",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);
		$datos_bitacora = [
			"id_objeto" => 0,
			"fecha" => date('Y-m-d H:i:s'),
			"id_tipo_producto" => $_SESSION['id_login'],
			"accion" => "Usuario inactivado",
			"descripcion" => "El usuario ".$_SESSION['usuario_login']." inactivó un usuario del sistema"
		];
		Bitacora::guardar_bitacora($datos_bitacora);
			exit();

			
	}
}