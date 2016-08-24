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
			<!--div class="text-center">
				<label for="codigo_barras">Código de barras: </label><br>
				<input class="form-control input-md" type="text" name="codigo_barras" id="codigo_barras" placeholder="Código de barras"/>
			</div-->
		</div>
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
			<!--div class="text-center">
				<br>
			    <button type="button" class="btn btn-info btn-sm" id="buscar_modelo">
					Producto por Modelo <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
				</button>
			</div-->
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
						<th class="text-center" class="cantidad_total">Cantidad Total</th>
					</tr>
				</thead>
				<tbody>
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
	<? if($id_usuario == '1'): ?>
		<div class="col-xs-12 col-sm-4 text-center">
			<button type="button" class="btn btn-success btn-lg" id="excel">
				Excel <span class="glyphicon glyphicon-object-align-bottom" aria-hidden="true"></span>
			</button>
		</div>
	<? endif; ?>
		<div class="col-xs-12 col-sm-4 text-right">
			&nbsp;
		</div>
	</div>
</div>