<?php
 if (session_status() == PHP_SESSION_NONE) {
	session_start();
} 

if($peticionAjax){
	require_once "../modelos/parametroModelo.php";
	require_once "../pruebabitacora.php";
}else{
	require_once "./modelos/parametroModelo.php";
	require_once "./pruebabitacora.php";//aqui se ejecuta dentro del index y no se utiliza Ajax
}


class parametroControlador extends parametroModelo
{

	/*--------- Controlador agregar parametro ---------*/
	public function agregarParametro()
	{
		$nom_parametro=mainModel::limpiar_cadena(strtoupper($_POST['nombre_parametro_nuevo']));
		$valor=mainModel::limpiar_cadena($_POST['valor_parametro_nuevo']);
		$creado=$_SESSION['usuario_login'];
		$fec_creacion=date('y-m-d H:i:s');

		//verificar datos ingresados
		if(mainModel::verificar_datos("[A-ZÁÉÍÓÚÑ_]{1,40}",$nom_parametro)){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"El nombre del parámetro no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(mainModel::verificar_datos("[A-Za-zÑñ0-9]{1,40}",$valor)){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"El valor del parámetro no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		$check_parametro=mainModel::ejecutar_consulta_simple("SELECT parametro FROM tbl_ms_parametros WHERE parametro='$nom_parametro'");
			if($check_parametro->rowCount()>0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El parámetro ingresado ya se encuentra registrado en el sistema",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			$datos_parametro_reg=[
				"nombre"=>$nom_parametro,
				"valor"=>$valor,
				"id_usuario" => $_SESSION['id_login'],
				"creado"=>$creado,
				"fecha_crea"=>$fec_creacion
			];

			$agregar_parametro=parametroModelo::agregar_parametro_modelo($datos_parametro_reg);

			if($agregar_parametro->rowCount()==1){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"Parámetro Registrado",
					"Texto"=>"Los datos del parámetro han sido registrados con exito",
					"Tipo"=>"success"
				];

				
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido registrar el parámetro",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);

			$datos_bitacora = [
				"id_objeto" => 0,
				"fecha" => date('Y-m-d H:i:s'),
				"id_usuario" => $_SESSION['id_login'],
				"accion" => "Creación de Parámetro",
				"descripcion" => "El usuario ".$_SESSION['usuario_login']." creó un nuevo parámetro en el sistema"
			];
			Bitacora::guardar_bitacora($datos_bitacora); 
	} /* Fin controlador */


	/*--------- Controlador actualizar parametro ---------*/
	public function actualizarParametro()
	{	
		$nom_parametro=mainModel::limpiar_cadena(strtoupper($_POST['nombre_parametro_act']));
		$valor=mainModel::limpiar_cadena($_POST['valor_parametro_act']);
		$modificado=$_SESSION['usuario_login'];
		$fec_modificacion=date('y-m-d H:i:s');
		$id_actualizar=mainModel::limpiar_cadena($_POST['id_actualizacion']);
		
		//verificar datos ingresados
		if(mainModel::verificar_datos("[A-ZÁÉÍÓÚÑ_]{1,40}",$nom_parametro)){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"El nombre del parámetro no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(mainModel::verificar_datos("[A-Za-zÑñ0-9]{1,40}",$valor)){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"El valor del parámetro no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

			$datos_parametro_act=[
				"nombre"=>$nom_parametro,
				"valor"=>$valor,
				"id_usuario" => $_SESSION['id_login'],
				"modif"=>$modificado,
				"fecha_modif"=>$fec_modificacion
			];

			$actualizar_parametro=parametroModelo::actualizar_parametro_modelo($datos_parametro_act,$id_actualizar);

			if($actualizar_parametro->rowCount()==1)
			{
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"Parámetro Actualizado",
					"Texto"=>"Parámetro actualizado exitosamente",
					"Tipo"=>"success"
				];
			}else
			{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido actualizar el parámetro",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);

			$datos_bitacora = [
				"id_objeto" => 0,
				"fecha" => date('Y-m-d H:i:s'),
				"id_usuario" => $_SESSION['id_login'],
				"accion" => "Modificación de Parámetro",
				"descripcion" => "El usuario ".$_SESSION['usuario_login']." actualizó un parámetro del sistema"
			];
			Bitacora::guardar_bitacora($datos_bitacora); 
	} /* Fin controlador */
	

	
		//funcion para eliminar un parametro
		public function eliminarParametro()
		{
			$id=mainModel::limpiar_cadena(($_POST['id_parametro_del']));

		//verifica que el parametro si exista en el sistema
		$check_parametro=mainModel::ejecutar_consulta_simple("SELECT id_parametro FROM tbl_ms_parametros
		WHERE id_parametro='$id'");
		if($check_parametro->rowCount()<=0){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ha ocurrido un error",
				"Texto"=>"El parametro seleccionado no existe",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		
		$eliminarparametro=parametroModelo::eliminar_parametro_modelo($id);
			if($eliminarparametro->rowCount()==1){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"Parametro Eliminado",
					"Texto"=>"El parametro fue eliminado del sistema",
					"Tipo"=>"success"
				];
                echo json_encode($alerta);

                $datos_bitacora = [
                    "id_objeto" => 0,
                    "fecha" => date('Y-m-d H:i:s'),
                    "id_usuario" => $_SESSION['id_login'],
                    "accion" => "Parámetro eliminado",
                    "descripcion" => "El usuario ".$_SESSION['usuario_login']." eliminó un parámetro del sistema"
                ];
                Bitacora::guardar_bitacora($datos_bitacora);
                exit();
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ha ocurrido un error",
					"Texto"=>"El Parametro no pudo ser borrado",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);
			exit();	
	}
}