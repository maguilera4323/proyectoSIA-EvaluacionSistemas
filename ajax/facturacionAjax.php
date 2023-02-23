<?php
	$peticionAjax=true;
	require_once "../config/APP.php";

	if(isset($_POST['dni_pedido_nuevo']) || isset($_POST['dni_pedido_act']) || isset($_POST['id_pedido_del']))
	{
		/*--------- Instancia al controlador ---------*/
		require_once "../controladores/facturacionControlador.php";
		$ins_factura = new facturacionControlador();


		if(isset($_POST['dni_pedido_nuevo'])){
			echo $ins_factura->agregarPedido();
			die();
		}


		if(isset($_POST['dni_pedido_act'])){
			echo $ins_factura->actualizarPedido();
			die();
		}
		
	
		
		if(isset($_POST['id_pedido_del']) ){
			echo $ins_factura->anularPedido();
			die();
		}
	}

	