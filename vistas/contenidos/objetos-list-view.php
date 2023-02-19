<?php
	require_once "./pruebabitacora.php";
		include ("./cone.php");     

		//verificación de permisos
		//se revisa si el usuario tiene acceso a una vista específica por medio del rol que tiene y el objeto al que quiere acceder
		$id_rol=$_SESSION['id_rol'];
			$SQL="SELECT * FROM tbl_permisos where id_rol='$id_rol' and id_objeto=7";
			$dato = mysqli_query($conexion, $SQL);

			if($dato -> num_rows >0){
				while($fila=mysqli_fetch_array($dato)){
					$permiso_in=$fila['permiso_insercion'];
					$permiso_act=$fila['permiso_actualizacion'];
					$permiso_eli=$fila['permiso_eliminacion'];
					$permiso_con=$fila['permiso_consulta'];
				}
			}

			//valida si el query anterior no retornó ningún valor
			//en este caso no había un permiso registrado del objeto para el rol del usuario conectado
			if(!isset($permiso_con)){
				echo '<div class="alert alert-warning text-center" style="font-size: 28px;">Usted no tiene acceso autorizado a esta vista</div>';
				echo "<script> window.location.href='".SERVERURL."home/'; </script>";	
			//valida si el permiso tiene valor de cero, lo que significa que no puede acceder a la vista	
			}else if($permiso_con==0){
				echo '<div class="alert alert-warning text-center" style="font-size: 28px;">Usted no tiene acceso autorizado a esta vista</div>';
				echo "<script> window.location.href='".SERVERURL."home/'; </script>";
			}else{
				$datos_bitacora = 
				[
					"id_objeto" => 7,
					"fecha" => date('Y-m-d H:i:s'),
					"id_usuario" => $_SESSION['id_login'],//cambiar aqui para que me pueda traer el USU conectado
					"accion" => "Cambio de vista",
					"descripcion" => "El usuario ".$_SESSION['usuario_login']." entró a la Vista de Mantenimiento de Objetos"
				];
				Bitacora::guardar_bitacora($datos_bitacora);
			}
?>

<div class="full-box page-header">
	<h3 class="text-left">
		<i class="fas fa-list-alt"></i> &nbsp; OBJETOS
	</h3>

</div>

<div class="container-fluid">
	<ul class="full-box list-unstyled page-nav-tabs">
		<li>
			<div class="btn btn-dark btn-lg" data-toggle="modal" data-target="#ModalCrear"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR OBJETO</div>
		</li>
		</ul>	
</div>

<?php
	include ("./cone.php");
	$where="";

	if(isset($_GET['enviar'])){
	$busqueda = $_GET['busqueda'];


		if (isset($_GET['busqueda']))
		{
			$where="WHERE tbl_objetos.objeto LIKE'%".$busqueda."%' or TBL_objetos.tipo_objeto LIKE'%".$busqueda."%'";
		}
	}
?>

</form>
<div class="container-fluid">
<form class="d-flex" action="../pdf/objetospdf.php" method="post" accept-charset="utf-8">		
	<input class="form-control me-2 light-table-filter" data-table="table_id" type="text" name="filtroobjetos"
	placeholder="Buscar Objetos"> 
	<button type="submit" class="btn btn-danger mx-auto btn-lg"><i class="fas fa-file-pdf"></i> &nbsp;Descargar Reporte</button>
</div>
      <hr>
      </form>
</div>
      <table class="table table-striped table-dark table_id text-center" id="tblDatos">
        <thead>    
        <tr>
            <th>OBJETO</th>
            <th>DESCRIPCION</th>
            <th>TIPO DE OBJETO</th>
            <th>ACTUALIZAR</th>
			<th>ELIMINAR</th>
            </tr>
        </thead>
        <tbody>

		<?php
			include ("./cone.php");              
			$SQL="SELECT * FROM tbl_objetos 
			$where";
			$dato = mysqli_query($conexion, $SQL);

			if($dato -> num_rows >0){
				while($fila=mysqli_fetch_array($dato)){
				
			?>
		<tr>
			<td><?php echo $fila['objeto']; ?></td>
			<td><?php echo $fila['descripcion']; ?></td>
			<td><?php echo $fila['tipo_objeto']; ?></td>
			<td>
				<div class="btn btn-success" data-toggle="modal" data-target="#ModalActualizar<?php echo $fila['id_objeto'];?>">
					<i class="fas fa-sync-alt"> </i>
				</div>
						<!-- Modal actualizar-->
						<div class="modal fade" id="ModalActualizar<?php echo $fila['id_objeto'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						<?php
							if(!isset($permiso_act)){
								echo '<div class="alert alert-warning text-center" style="font-size: 28px;">Usted no tiene autorización para actualizar un objeto</div>';
								echo "<script> window.location.href='".SERVERURL."home/'; </script>";	
							//valida si el permiso tiene valor de cero, lo que significa que no puede acceder a la vista	
							}else if($permiso_act==0){
								echo '<div class="alert alert-warning text-center" style="font-size: 28px;">Usted no tiene autorización para actualizar un objeto
								<button type="button" class="close" data-dismiss="alert" onclick="window.location.reload()">X</button>
								</div>';
							}else{
						?>
							<div class="modal-dialog" role="document">
								<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="exampleModalLabel">Actualizar Objeto</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<form action="<?php echo SERVERURL; ?>ajax/objetoAjax.php" class="FormularioAjax" method="POST" data-form="save" autocomplete="off">
										<div class="form-group">
										<label class="color-label">Nombre del Objeto</label>
											<input type="text" class="form-control" name="objeto_act" id="cliente_dni" 
											value="<?php echo $fila['objeto']?>" style="text-transform:uppercase;" required>
										</div>
										<div class="form-group">
										<label class="color-label">Descripción</label>
											<input type="text" class="form-control" name="desc_objeto_act" id="cliente_dni"  
											value="<?php echo $fila['descripcion']?>" required>
											<input type="hidden" class="form-control" name="id_actualizacion" value="<?php echo $fila['id_objeto']?>">
										</div>
										<div class="form-group">
											<div class="form-group">
											<label class="color-label">Tipo de Objeto</label>
											<select class="form-control" name="tipo_objeto_act">
												<option value="1" <?php if ($fila['tipo_objeto'] == 'Home'): ?>selected<?php endif; ?>>Home</option>
												<option value="2" <?php if ($fila['tipo_objeto'] == 'Proveedores'): ?>selected<?php endif; ?>>Proveedores</option>
												<option value="3" <?php if ($fila['tipo_objeto'] == 'Insumos'): ?>selected<?php endif; ?>>Insumos</option>
												<option value="4" <?php if ($fila['tipo_objeto'] == 'Productos'): ?>selected<?php endif; ?>>Productos</option>
												<option value="5" <?php if ($fila['tipo_objeto'] == 'Compras'): ?>selected<?php endif; ?>>Compras</option>
												<option value="6" <?php if ($fila['tipo_objeto'] == 'Facturacion'): ?>selected<?php endif; ?>>Facturación</option>
												<option value="7" <?php if ($fila['tipo_objeto'] == 'Mantenimiento'): ?>selected<?php endif; ?>>Mantenimiento</option> 
												<option value="8" <?php if ($fila['tipo_objeto'] == 'Administracion'): ?>selected<?php endif; ?>>Administración</option>
											</select>
											</div>
										</div>
										<button type="submit" class="btn btn-primary">Guardar</button>
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
										</form>
									</div>
								</div>
							</div>
							<?php
								}
							?>
					</div>
			</td>
			<td>
				<form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/objetoAjax.php" method="POST" data-form="delete" autocomplete="off">
				<input type="hidden" pattern="" class="form-control" name="id_objeto_del" value="<?php echo $fila['id_objeto'] ?>">
				<button type="submit" class="btn btn-warning">
					<i class="far fa-trash-alt"></i>
				</button>
				</form>
			</td>
		</tr>

		<?php
			}
				}else{
		?>

		<tr class="text-center">
		<td colspan="16">No existen registros</td>
		</tr>

    <?php
		}
	?>
		</tbody>
  </table>

<div id="paginador" class=""></div>	
<div class="container-fluid"></div>


<!-- Modal crear-->
<div class="modal fade" id="ModalCrear" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<?php
	if(!isset($permiso_in)){
		echo '<div class="alert alert-warning text-center" style="font-size: 28px;">Usted no tiene autorización para crear un objeto</div>';
		echo "<script> window.location.href='".SERVERURL."home/'; </script>";	
	//valida si el permiso tiene valor de cero, lo que significa que no puede acceder a la vista	
	}else if($permiso_in==0){
		echo '<div class="alert alert-warning text-center" style="font-size: 28px;">Usted no tiene autorización para crear un objeto
		<button type="button" class="close" data-dismiss="alert" onclick="window.location.reload()">X</button>
		</div>';
	}else{
?>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nuevo Objeto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
			<form action="<?php echo SERVERURL; ?>ajax/objetoAjax.php" class="FormularioAjax" method="POST" data-form="save" autocomplete="off">
			<div class="form-group">
				<label class="color-label">Nombre del Objeto</label>
				<input type="text" class="form-control" name="objeto_nuevo" id="cliente_dni" style="text-transform:uppercase;" required>
			</div>
			<div class="form-group">
				<label class="color-label">Descripción</label>
				<textarea class="form-control" rows="3" name="desc_objeto_nuevo" id="cliente_dni" required></textarea>
			</div>
			<div class="form-group">
				<div class="form-group">
				<label class="color-label">Tipo de Objeto</label>
					<select class="form-control" name="tipo_objeto_nuevo" required>
						<option value="0" >Seleccione una opción</option>
						<option value="1">Home</option>
						<option value="2">Proveedores</option>
						<option value="3">Insumos</option>
						<option value="4">Productos</option>
						<option value="5">Compras</option>
						<option value="6">Facturación</option>
						<option value="7">Mantenimiento</option>
						<option value="8">Administración</option>
						</select>
				</div>
			</div>
			<button type="submit" class="btn btn-primary">Guardar</button>
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</form>
      </div>
    </div>
  </div>
  <?php
		}
	?>
</div>