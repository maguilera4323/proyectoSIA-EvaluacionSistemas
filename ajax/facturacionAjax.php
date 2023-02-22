<?php
	$peticionAjax=true;
	require_once "../config/APP.php";

	if(isset($_POST['dni_pedido_nuevo']) || isset($_POST['id_act_compra']) || isset($_POST['id_compra_del']))
	{
		/*--------- Instancia al controlador ---------*/
		require_once "../controladores/facturacionControlador.php";
		$ins_factura = new facturacionControlador();


		/*--------- Agregar una compra ---------*/
		if(isset($_POST['dni_pedido_nuevo'])){
			echo $ins_factura->agregarPedido();
			die();
		}

		/*--------- Actualizar una compra ---------*/
		if(isset($_POST['id_act_compra'])){
			echo $ins_compra->actualizarCompra();
			echo $ins_compra->actualizarDetalleCompra();
			die();
		}
		
		
		/*--------- Eliminar una compra ---------*/
		if(isset($_POST['id_compra_del']) ){
			echo $ins_compra->anularCompra();
			die();
		}
	}

	