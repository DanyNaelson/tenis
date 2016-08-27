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
		<div class="col-sm-12 col-md-4">
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
		<div class="col-sm-12 col-md-4">
			<div class="text-center">
				<label for="codigo_barras">Código de barras: </label><br>
				<input class="form-control input-md" type="text" name="codigo_barras" id="codigo_barras" placeholder="Código de barras"/>
			</div>
		</div>
		<div class="col-sm-12 col-md-4">
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
			<table class="table table-bordered table-condensed" id="tabla_salidas">
				<thead>
					<tr class="th-blue">
						<th class="marca text-center">Marca</th>
						<th class="modelo text-center">Modelo</th>
						<th class="descripcion text-center">Descripcion</th>
						<th class="talla text-center">Talla</th>
						<th class="cantidad_sist text-center">Cantidad Sistema</th>
						<th class="cantidad_f text-center">Cantidad Física</th>
						<th class="diff_sist text-center">Diferencia</th>
						<th class="text-center">Borrar</th>
					</tr>
				</thead>
				<tbody>
					<tr class="text-center" id="tr_total">
						<td style="border: hidden;"></td>
						<td style="border: hidden;"></td>
						<td style="border: hidden;"></td>
						<td class="total_salidas th-blue"><b>Total: </b></td>
						<td id="total_sistema">0</td>
						<td id="total_s">0</td>
						<td style="border: hidden;"></td>
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
	<div class="row">
		<div class="col-xs-4 text-center">
			<button type="button" class="btn btn-success btn-sm" id="finalizar">
				Finalizar <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
			</button>
		</div>
		<div class="col-xs-4 text-center">
			<!--button type="button" class="btn btn-warning btn-sm" id="pausar">
				Pausar <span class="glyphicon glyphicon-pause" aria-hidden="true"></span>
			</button-->
		</div>
		<div class="col-xs-4 text-center">
			<button type="button" class="btn btn-danger btn-sm" id="cancelar">
				Cancelar <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
			</button>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			&nbsp;
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12 table-responsive">
			<table class="table table-bordered table-condensed" id="tabla_productos">
				<thead>
					<tr class="th-blue">
						<th class="text-center" class="marca">Marca</th>
						<th class="text-center" class="modelo">Modelo</th>
						<th class="text-center" class="descripcion">Descripcion</th>
					<? foreach ($tallas as $talla): ?>
						<th class="text-center"><?= ucfirst($talla->talla) ?></th>
					<? endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<tr class="text-center" id="tr_tallas">
						<td style="border: hidden;"></td>
						<td style="border: hidden;"></td>
						<td class="total_tallas th-blue"><b>Total tallas: </b></td>
					<? foreach ($tallas as $talla): ?>
						<td class="text-center tallas_c idtalla_<?= $talla->id_talla ?>">0</td>
					<? endforeach; ?>
					</tr>
				</tbody>
			</table>
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