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
			    &nbsp;
			</div>
		</div>
		<div class="col-sm-12 col-md-4">
			<div class="text-center">
			    <label for="tipo_movimiento">Tipo Movimiento: </label>
			    <select name="tipo_movimiento" id="tipo_movimiento" class="form-control">
			    	<option value="0">Seleccionar...</option>
			    <? foreach ($tipo_mov as $tipo): ?>
			    	<option value="<?= $tipo->id_tipo_movimiento ?>"><?= $tipo->tipo_movimiento ?></option>
				<? endforeach; ?>
			    </select>
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
						<th class="text-center">Seleccionar</th>
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
						<td id="total_diferencia">0</td>
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
</div>