<?php
	require_once "./pruebabitacora.php";
		include ("./cone.php");     

		//verificación de permisos
		//se revisa si el usuario tiene acceso a una vista específica por medio del rol que tiene y el objeto al que quiere acceder
		$id_rol=$_SESSION['id_rol'];
			$SQL="SELECT * FROM tbl_permisos where id_rol='$id_rol' and id_objeto=13";
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
					"id_objeto" => 13,
					"fecha" => date('Y-m-d H:i:s'),
					"id_usuario" => $_SESSION['id_login'],//cambiar aqui para que me pueda traer el USU conectado
					"accion" => "Cambio de vista",
					"descripcion" => "El usuario ".$_SESSION['usuario_login']." entró a la Vista del Recetario"
				];
				Bitacora::guardar_bitacora($datos_bitacora);
			}
?>

<div class="full-box page-header">
	<h3 class="text-left">
		<i class="fas fa-tools"></i> &nbsp; RECETARIO
	</h3>
</div>

<div class="container-fluid">
	<ul class="full-box list-unstyled page-nav-tabs">
		<li>
			<div class="btn btn-dark btn-lg" data-toggle="modal" data-target="#ModalCrear"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR RECETA</div>
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
			$where="WHERE tbl_recetario.receta LIKE'%".$busqueda."%'";
		}
	}
?>

<div class="container-fluid">
  <form class="d-flex" action="../pdf/recetariopdf.php" method="post" accept-charset="utf-8">
  	<input class="form-control me-2 light-table-filter" data-table="table_id" type="text" name="filtrorecetario" placeholder="Buscar Receta">
	<button type="submit" class="btn btn-danger mx-auto btn-lg"><i class="fas fa-file-pdf"></i> &nbsp;Descargar Reporte</button>
      </form>
  </div>
  </div>

      <table class="table table-striped table-dark table_id text-center" id="tblDatos">
        <thead>    
        <tr>
            <th>PRODUCTO</th>
            <th>INSUMO</th>
			<th>CANTIDAD INSUMO</th>
			<th>UNIDAD MEDIDA</th>
            <th>ACTUALIZAR</th>
			<th>ELIMINAR</th>
            </tr>
        </thead>
        <tbody>

		<?php
			include ("./cone.php");              
			$SQL="SELECT p.id_producto, p.nom_producto, i.id_insumos, i.nom_insumo, i.unidad_medida, r.id_recetario, r.cant_insumo FROM tbl_recetario r
			inner JOIN tbl_producto p ON p.id_producto = r.id_producto
			inner JOIN tbl_insumos i ON i.id_insumos = r.id_insumo
			$where";
			$dato = mysqli_query($conexion, $SQL);
		

			if($dato -> num_rows >0){
				while($fila=mysqli_fetch_array($dato)){
				
			?>
		<tr>
			<td><?php echo $fila['nom_producto']; ?></td>
			<td><?php echo $fila['nom_insumo']; ?></td>
			<td><?php echo $fila['cant_insumo']; ?></td>
			<td><?php echo $fila['unidad_medida']; ?></td>
			<td>
				<div class="btn btn-success" data-toggle="modal" data-target="#ModalActualizar<?php echo $fila['id_recetario'];?>">
					<i class="fas fa-sync-alt"> </i>
				</div>
						<!-- Modal actualizar-->
						<div class="modal fade" id="ModalActualizar<?php echo $fila['id_recetario'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						<?php
							if(!isset($permiso_act)){
								echo '<div class="alert alert-warning text-center" style="font-size: 28px;">Usted no tiene autorización para actualizar un rol</div>';
								echo "<script> window.location.href='".SERVERURL."home/'; </script>";	
							//valida si el permiso tiene valor de cero, lo que significa que no puede acceder a la vista	
							}else if($permiso_act==0){
								echo '<div class="alert alert-warning text-center" style="font-size: 28px;">Usted no tiene autorización para actualizar un rol
								<button type="button" class="close" data-dismiss="alert" onclick="window.location.reload()"X</button>
								</div>';
							}else{
						?>
							<div class="modal-dialog" role="document">
								<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="exampleModalLabel">Actualizar Receta</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body text-center">
								<form action="<?php echo SERVERURL; ?>ajax/recetarioAjax.php" class="FormularioAjax" method="POST" data-form="save" autocomplete="off">
								<div class="form-group">
								<label class="color-label">Producto</label>
									<select class="form-control" name="recetario_act" id="Id_producto_nuevo">
										<?php
											include ("./cone.php");   
											$tipo="SELECT * FROM tbl_producto";
											$resultado=mysqli_query($conexion, $tipo);
											while ($valores = mysqli_fetch_array($resultado)){
											//validación para obtener el valor guardado en la base de datos
											//y que este se muestre seleccionado en la base de datos
												if($fila['id_producto']==$valores['id_producto']){
													echo '<option value="'.$valores['id_producto'].'" selected>'.$valores['nom_producto'].'</option>';
											//validación para obtener los demás valores de la base de datos para el select
												}elseif($fila['id_producto']!=$valores['id_producto']){
													echo '<option value="'.$valores['id_producto'].'">'.$valores['nom_producto'].'</option>';
												}
											}
										?>
									</select>
								</div>
								<div class="form-group">
								<label class="color-label">Insumo</label>
								<select class="form-control" name="Id_insumo_act" id="Id_insumo_nuevo">
									<?php
									include ("./cone.php");   
									$tipo="SELECT * FROM tbl_insumos";
									$resultado=mysqli_query($conexion, $tipo);
									while ($valores = mysqli_fetch_array($resultado)){
									//validación para obtener el valor guardado en la base de datos
									//y que este se muestre seleccionado en la base de datos
										if($fila['id_insumos']==$valores['id_insumos']){
											echo '<option value="'.$valores['id_insumos'].'" selected>'.$valores['nom_insumo'].'</option>';
									//validación para obtener los demás valores de la base de datos para el select
										}elseif($fila['id_insumos']!=$valores['id_insumos']){
											echo '<option value="'.$valores['id_insumos'].'">'.$valores['nom_insumo'].'</option>';
										}
									}
									?>
								</select>
								</select>
								</div>
								
								
								<div class="form-group">
								<label class="color-label">Cantidad Insumo</label>
								<input type="text" class="form-control" name="cant_insumo_act" value="<?php echo $fila['cant_insumo']; ?>" id="id_insumo" step="any" required>
								<input type="hidden" class="form-control" name="id_actualizacion" value="<?php echo $fila['id_recetario']?>">
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
				<form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/recetarioAjax.php" method="POST" data-form="delete" autocomplete="off">
				<input type="hidden" pattern="" class="form-control" name="recetario_del" value="<?php echo $fila['id_recetario'] ?>">
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
		echo '<div class="alert alert-warning text-center" style="font-size: 28px;">Usted no tiene autorización para crear un rol</div>';
		echo "<script> window.location.href='".SERVERURL."home/'; </script>";	
	//valida si el permiso tiene valor de cero, lo que significa que no puede acceder a la vista	
	}else if($permiso_in==0){
		echo '<div class="alert alert-warning text-center" style="font-size: 28px;">Usted no tiene autorización para crear un rol
		<button type="button" class="close" data-dismiss="alert" onclick="window.location.reload()">X</button>
		</div>';
	}else{
?>  
<div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nueva Receta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>

      </div>
      <div class="modal-body text-center">
			<form action="<?php echo SERVERURL; ?>ajax/recetarioAjax.php" class="FormularioAjax" method="POST" data-form="save" autocomplete="off">
			<div class="form-group">
			<label class="color-label">Producto</label>
							<select class="form-control" name="recetario_nuevo" id="Id_producto_nuevo">
								<option value="0">Seleccione una opción</option>
								<?php
								include ("./cone.php");   
								$tipo="SELECT * FROM tbl_producto";
								$resultado=mysqli_query($conexion, $tipo);
								while ($valores = mysqli_fetch_array($resultado)){
									echo '<option value="'.$valores['id_producto'].'">'.$valores['nom_producto'].'</option>';
								}
								?>
							</select>
			</div>
			<div class="form-group">
			<label class="color-label">Insumo</label>
							<select class="form-control" name="Id_insumo_nuevo" id="Id_insumo_nuevo">
								<option value="0">Seleccione una opción</option>
								<?php
								include ("./cone.php");   
								$tipo="SELECT * FROM tbl_insumos";
								$resultado=mysqli_query($conexion, $tipo);
								while ($valores = mysqli_fetch_array($resultado)){
									echo '<option value="'.$valores['id_insumos'].'">'.$valores['nom_insumo'].'</option>';
								}
								?>
							</select>
							</div>
				<div class="form-group">
				<label class="color-label">Cantidad Insumo</label>
				<input type="text" class="form-control" name="cant_insumo_nuevo" id="id_insumo" step="any" required>
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





