<script src=""></script>
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
		<div class="col-sm-12 col-md-2">
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
		<div class="col-sm-12 col-md-2">
			<div class="text-center">
			    <label for="tipo_m">Tipo Movimiento: </label>
			    <select name="tipo_m" id="tipo_m" class="form-control">
			    	<option value="0">Todos...</option>
			    <? foreach ($tipos_m as $tipo_m): ?>
			    	<option value="<?= $tipo_m->id_tipo_movimiento ?>"><?= $tipo_m->tipo_movimiento ?></option>
				<? endforeach; ?>
			    </select>
			</div>
		</div>
		<div class="col-sm-12 col-md-2">
			<div class="text-center">
			    <label for="folio">Folio: </label><br>
				<input class="form-control input-md" type="text" id="folio"/>
			</div>
		</div>
		<div class="col-sm-12 col-md-2">
			<div class="text-center">
			    <label for="fecha_inicio">Fecha Inicio: </label><br>
				<input class="form-control input-md" type="text" id="fecha_inicio" size="30" readonly />
			</div>
		</div>
		<div class="col-sm-12 col-md-2">
			<div class="text-center">
			    <label for="fecha_fin">Fecha Fin: </label><br>
				<input class="form-control input-md" type="text" id="fecha_fin" size="30" readonly />
			</div>
		</div>
		<div class="col-sm-12 col-md-2">
			<div class="text-center">
			    <label for="registros">Mostrar </label>
			    <select name="registros" id="registros" class="form-control">
			    	<option value="2">2</option>
			    	<option value="10">10</option>
			    	<option value="20">20</option>
			    </select>
			    <label for="registros"> registros</label>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			&nbsp;
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12 text-center">
			<button type="button" class="btn btn-info btn-md" id="buscar">
				Buscar <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
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
			<table class="table table-bordered table-condensed" id="tabla_movimientos">
				<thead>
					<tr class="th-blue">
						<th class="text-center">#</th>
						<th class="text-center tipo">Tipo Movimiento</th>
						<th class="text-center folio">Folio</th>
						<th class="text-center almacen">Almacén</th>
						<th class="text-center fecha_hora">Fecha/Hora</th>
						<th class="text-center cantidad">Cantidad</th>
						<th class="text-center precio">Precio</th>
						<th class="text-center estatus">Estatus</th>
						<th class="text-center">Detalles</th>
						<th class="text-center">Cancelar</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
					<tr class="th-blue">
						<th class="text-center">#</th>
						<th class="text-center tipo_f">Tipo Movimiento</th>
						<th class="text-center folio_f">Folio</th>
						<th class="text-center almacen_f">Almacén</th>
						<th class="text-center fecha_hora">Fecha/Hora</th>
						<th class="text-center cantidad_f">Cantidad</th>
						<th class="text-center precio_f">Precio</th>
						<th class="text-center estatus_f">Estatus</th>
						<th class="text-center">Detalles</th>
						<th class="text-center">Cancelar</th>
					</tr>
				</tfoot>
			</table>
		</div>
		<div id="info" class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title text-center"></h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-sm-12 table-responsive">
								<table class="table table-bordered table-condensed" id="tabla_detalles">
									<thead>
										<tr class="th-blue">
											<th style="border: hidden; background-color: white;"></th>
											<th class="text-center cantidad">Cantidad</th>
											<th class="text-center marca">Marca</th>
											<th class="text-center modelo">Modelo</th>
											<th class="text-center descripcion">Descripción</th>
											<th class="text-center talla">Talla</th>
											<th class="text-center precio">Precio</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
									<tfoot>
										<tr class="text-center">
											<th class="text-center th-blue">Total Cantidad:</th>
											<th class="text-center" id="total_cantidad">0</th>
											<th style="border: hidden; background-color: white;"></th>
											<th style="border: hidden; background-color: white;"></th>
											<th style="border: hidden; background-color: white;"></th>
											<th class="text-center th-blue">Total Precio:</th>
											<th class="text-center" id="total_precio">0</th>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</div>
	<div class="row">
		<div class="col-sm-12 text-center">
			<nav id="pags">
				<ul class="pagination">
					<li class="first">
						<a href="#" aria-label="Previous" onclick="obtener_productos(this, 1)">
							<span aria-hidden="true">&laquo;</span>
						</a>
					</li>
				<?  for ($i = 1 ; $i <= $paginas ; $i++): ?>
					<li class="<?= $i == 1 ? 'active' : ''; ?> pag_<?= $i ?>"><a href="#" onclick="obtener_productos(this, <?= $i ?>)"><?= $i ?></a></li>
				<? endfor; ?>
					<li class="last">
						<a href="#" aria-label="Next" onclick="obtener_productos(this, <?= $paginas ?>)">
							<span aria-hidden="true">&raquo;</span>
						</a>
					</li>
				</ul>
			</nav>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-4 text-left">
			<a href="<?= $pagina_retorno ?>" class="btn btn-default btn-sm" role="button">
				<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Regresar
			</a>
		</div>
	<? if($id_usuario == '1'): ?>
		<div class="col-xs-12 col-sm-4 text-center">
			<button type="button" class="btn btn-primary btn-lg" id="excel">
				Excel <span class="glyphicon glyphicon-barcode" aria-hidden="true"></span>
			</button>
		</div>
	<? endif; ?>
		<!--div class="col-xs-12 col-sm-4 text-right">
			<button type="button" class="btn btn-info btn-lg" id="pdf">
				PDF <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
			</button>
		</div-->
	</div>
</div>
