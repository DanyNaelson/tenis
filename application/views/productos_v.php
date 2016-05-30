<div class="container">
	<div class="row">
		<div class="col-sm-12 table-responsive">
			<table class="table table-bordered table-condensed" id="tabla_productos">
				<tr class="th-blue">
					<th class="text-center">#</th>
					<th class="text-center" class="marca">Marca</th>
					<th class="text-center" class="modelo">Modelo</th>
					<th class="text-center" class="descripcion">Descripcion</th>
				<? foreach ($tallas as $talla): ?>
					<th class="text-center"><?= ucfirst($talla->talla) ?></th>
				<? endforeach; ?>
					<th class="text-center" class="precio">Precio</th>
					<th class="text-center">Editar</th>
					<th class="text-center">Borrar</th>
				</tr>
			<?  for ($i = 0 ; $i < count($productos) ; $i++): ?>
				<tr id="producto_<?= $productos[$i]->id_producto ?>">
					<td class="text-center no-item"><?= $i+1 ?></td>
					<td class="text-center"><?= $productos[$i]->marca ?></td>
					<td class="text-center"><?= $productos[$i]->modelo ?></td>
					<td class="text-center"><?= $productos[$i]->descripcion ?></td>
				<? for ($j = 0 ; $j < count($tallas) ; $j++): ?>
					<? if($productos_tallas[$i][$j] != ''): ?>
						<td class="text-center check"><?= $productos_tallas[$i][$j] ?></td>
					<? else: ?>
						<td class="text-center no-check"></td>
					<? endif; ?>
				<? endfor; ?>
					<td class="text-center"><?= $productos[$i]->precio ?></td>
					<td class="text-center">
						<button type="button" class="btn btn-info btn-sm editar_p">
							<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
						</button>
					</td>
					<td class="text-center">
						<button type="button" class="btn btn-danger btn-sm borrar_p">
							<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
						</button>
					</td>
				</tr>
			<? endfor; ?>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-4">
			<a href="<?= $pagina_retorno ?>" class="btn btn-default btn-sm" role="button">
				<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Regresar
			</a>
		</div>
		<div class="col-sm-4 col-sm-offset-4 text-right">
			<button type="button" class="btn btn-info btn-sm" id="agregar_p">
				Agregar <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
			</button>
		</div>
	</div>
</div>
