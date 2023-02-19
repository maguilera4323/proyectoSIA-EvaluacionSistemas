<?php
 if (session_status() == PHP_SESSION_NONE) {
	session_start();
} 

if($peticionAjax){
	require_once "../modelos/descuentosModelo.php";
	require_once "../pruebabitacora.php";
}else{
	require_once "./modelos/descuentosModelo.php";
	require_once "./pruebabitacora.php";//aqui se ejecuta dentro del index y no se utiliza Ajax
}


class descuentosControlador extends descuentosModelo
{

	/*--------- Controlador agregar Descuentos ---------*/
	public function agregar_descuentos_controlador()
	{
		$nombre=mainModel::limpiar_cadena(strtoupper($_POST['nombre_descuento_nuevo']));
		$porcentaje=mainModel::limpiar_cadena($_POST['porcentaje_descuento_nuevo'])/100;
				
			
			$datos_descuento_reg=[
				"nombre"=>$nombre,
				"porc"=>$porcentaje,
			];

			$agregar_descuento=descuentosModelo::agregar_descuento_modelo($datos_descuento_reg);

			if($agregar_descuento->rowCount()==1){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>" Descuento Registrado",
					"Texto"=>"El descuento ha sido registrado con exito",
					"Tipo"=>"success"
				];

				
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido registrar el descuento",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);

			$datos_bitacora = [
				"id_objeto" => 0,
				"fecha" => date('Y-m-d h:i:s'),
				"id_tipo_producto" => $_SESSION['id_login'],
				"accion" => "Agregar Descuento",
				"descripcion" => "Se agrego un nuevo descuento en el sistema"
			];
			Bitacora::guardar_bitacora($datos_bitacora); 
	} /* Fin controlador */


	/*--------- Controlador actualizar descuento ---------*/
	public function actualizar_descuentos_controlador()
	{	
		$id_actualizar=mainModel::limpiar_cadena($_POST['id_actualizacion']);
		$nombre=mainModel::limpiar_cadena($_POST['nombre_descuento_actu']);
		$porcentaje=mainModel::limpiar_cadena($_POST['porcentaje_descuento_nuevo'])/100;
		
		if($nombre=="" || $porcentaje=="" ){
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
			if(mainModel::verificar_datos("[A-ZÁÉÍÓÚÑ ]{1,30}",$nombre)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El Descuento no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			
			/*== ACTUALIZAR Descuento ==*/
		$datos_descuentos_actu=
			[
				"nombre"=>$nombre,
				"porc"=>$porcentaje,
			];

			$actualizar_descuentos=descuentosModelo::actualizar_descuento_modelo($datos_descuentos_actu,$id_actualizar);

			if($actualizar_descuentos->rowCount()==1)
			{
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"Descuento Actualizado",
					"Texto"=>"descuento actualizado exitosamente",
					"Tipo"=>"success"
				];
			}else
			{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido actualizar el descuento",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);

			$datos_bitacora = [
				"id_objeto" => 0,
				"fecha" => date('Y-m-d h:i:s'),
				"id_usuario" => $_SESSION['id_login'],
				"accion" => "Modificación de un descuento",
				"descripcion" => "Se actualizó un descuento en el sistema"
			];
			Bitacora::guardar_bitacora($datos_bitacora); 
	} /* Fin controlador */
	

	public function datosdescuentosControlador($nombre,$id){
		$nombre=mainModel::limpiar_cadena($nombre);
		$id=mainModel::limpiar_cadena($id);

		return descuentosModelo::datos_descuentos_modelo($nombre,$id);
	}

	

		//funcion para eliminar un descuento
		public function eliminar_descuentos()
		{
			$id=mainModel::limpiar_cadena(($_POST['id_descuentos_del']));
			$nombre=mainModel::limpiar_cadena(($_POST['id_descuentos_del']));
			$array=array();
			$valor='';

		

		//verifica que el usuario si exista en el sistema
		$check_descuento=mainModel::ejecutar_consulta_simple("SELECT id_descuentos FROM tbl_descuentos
		WHERE id_descuentos='$id'");
		if($check_descuento->rowCount()<=0){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ha ocurrido un error",
				"Texto"=>"El descuento seleccionado no existe",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		
		$eliminardescuentos=descuentosModelo::eliminar_descuentos_modelo("borrar",$id);
			if($eliminardescuentos->rowCount()==1){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"Descuento Borrado",
					"Texto"=>"El descuentoo fue borrado",
					"Tipo"=>"success"
				];
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ha ocurrido un error",
					"Texto"=>"El descuento no pudo ser borrado",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);
		$datos_bitacora = [
			"id_objeto" => 0,
			"fecha" => date('Y-m-d H:i:s'),
			"id_usuario" => $_SESSION['id_login'],
			"accion" => "Usuario inactivado",
			"descripcion" => "El usuario ".$_SESSION['usuario_login']." elimino un descuento del sistema"
		];
		Bitacora::guardar_bitacora($datos_bitacora);
			exit();

			
	}
}