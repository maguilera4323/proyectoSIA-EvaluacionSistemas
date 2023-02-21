<?php
	$peticionAjax=true;
	require_once "../config/APP.php";

	if(isset($_POST['proveedor_compra']) || isset($_POST['id_compra_del']))
	{
		/*--------- Instancia al controlador ---------*/
		require_once "../controladores/compraControlador.php";
		$ins_compra = new compraControlador();


		/*--------- Agregar una compra ---------*/
		if(isset($_POST['proveedor_compra'])){
			echo $ins_compra->agregarCompra();
			echo $ins_compra->agregarDetalleCompra();
			die();
		}
		
		
		/*--------- Eliminar un insumo ---------*/
		if(isset($_POST['id_compra_del']) ){
			echo $ins_compra->anularCompra();
			die();
		}
	}

	