<div class="container">
	<div class="row">
		<div class="col-sm-12 text-center">
			<h1><?= $modulo ?></h1>
		</div>
	</div>
	<div class="row">
		&nbsp;
	</div>
	<div class="row">
		<div class="col-xs-12 col-md-3">
			<div class="row">
				<div class="col-sm-12 ">
					<div class="text-center">
					    <label for="almacen">Almacen: </label>
					    <select name="almacen" id="almacen" class="form-control">
					    	<option value="0">Seleccionar...</option>
					    <? foreach ($almacenes as $almacen): ?>
					    	<option value="<?= $almacen->id_almacen ?>"><?= $almacen->almacen ?></option>
						<? endforeach; ?>
					    </select>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="text-center">
						&nbsp;
					</div>
				</div>
				<div class="col-sm-12">
					<div class="text-center">
					    <button type="button" class="btn btn-info btn-lg" id="ticket">
							Venta <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
						</button>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="text-center">
						&nbsp;
					</div>
				</div>
				<div class="col-sm-12">
					<div class="text-center">
					    <button type="button" class="btn btn-success btn-lg" id="finalizar">
							Cierre Ventas (Día) <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
						</button>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="text-center">
						&nbsp;
					</div>
				</div>
				<div class="col-sm-12">
					<div class="text-center">
					    <button type="button" class="btn btn-danger btn-lg" id="cancelar">
							Cancelar <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
						</button>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-9">
			<div class="row">
				<div class="col-sm-12 col-md-6">
					<div class="text-center">
						<label for="codigo_barras">Código de barras: </label><br>
						<input class="form-control input-md" type="text" name="codigo_barras" id="codigo_barras" placeholder="Código de barras"/>
					</div>
				</div>
				<div class="col-sm-12 col-md-6">
					<div class="text-center">
						<br>
					    <button type="button" class="btn btn-info btn-sm" id="buscar_modelo">
							Producto por Modelo <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
						</button>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					&nbsp;
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 table-responsive">
					<table class="table table-bordered table-condensed" id="tabla_ventas">
						<thead>
							<tr class="th-blue">
								<th style="border: hidden; background-color: white;"></th>
								<th class="cantidad text-center">Cantidad</th>
								<th class="marca text-center">Marca</th>
								<th class="modelo text-center">Modelo</th>
								<th class="descripcion text-center">Descripcion</th>
								<th class="talla text-center">Talla</th>
								<th class="precio text-center">Precio</th>
								<th class="text-center">Borrar</th>
							</tr>
						</thead>
						<tbody>
							<tr class="text-center">
								<td class="total_ventas th-blue"><b>Total cantidad: </b></td>
								<td id="total_v">0</td>
								<td style="border: hidden;"></td>
								<td style="border: hidden;"></td>
								<td style="border: hidden;"></td>
								<td class="total_precio th-blue"><b>Total Precio: </b></td>
								<td id="total_p">0</td>
								<td style="border: hidden;"></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div id="info" class="modal fade" tabindex="-1" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title"></h4>
							</div>
							<div class="modal-body">
								<p></p>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
							</div>
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			&nbsp;
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			&nbsp;
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-4 text-left">
			<a href="<?= $pagina_retorno ?>" class="btn btn-default btn-sm" role="button">
				<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Regresar
			</a>
		</div>
		<div class="col-xs-12 col-sm-4 text-center">
			&nbsp;
		</div>
		<div class="col-xs-12 col-sm-4 text-right">
			&nbsp;
		</div>
	</div>
	<div class="row">
		<div id="modelos_p" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title"></h4>
					</div>
					<div class="row">
						<div class="col-sm-12 col-md-4">
							<div class="text-center" id="marca_modal">
							</div>
						</div>
						<div class="col-sm-12 col-md-4">
							<div class="text-center">
								<label for="modelo">Modelo: </label><br>
								<input class="form-control input-md" type="text" name="modelo" id="modelo" placeholder="Modelo"/>
							</div>
						</div>
						<div class="col-sm-12 col-md-4">
							<div class="text-center">
								<br>
							    <button type="button" class="btn btn-info btn-sm" id="find_model">
									Buscar
								</button>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 text-center cargando">
						</div>
						<div class="col-sm-12 table-responsive">
							<table class="table table-bordered table-condensed" id="tabla_modelos">
								<thead>
									<tr class="th-blue">
										<th class="marca text-center">Seleccionar</th>
										<th class="marca text-center">Marca</th>
										<th class="modelo text-center">Modelo</th>
										<th class="descripcion text-center">Descripcion</th>
										<th class="talla text-center">Talla</th>
										<th class="cantidad text-center">Cantidad</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="text-center">
								<br>
							    <button type="button" class="btn btn-success btn-sm" id="send_sel">
									Seleccionar modelos
								</button>
							</div>
						</div>
					</div>
					<br>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</div>
</div>