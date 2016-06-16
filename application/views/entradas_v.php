<div class="container">
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
		<div class="hidden-sm col-md-4">
			&nbsp;
		</div>
		<div class="col-sm-12 col-md-4">
			<div class="text-center">
			    <label for="codigo_barras">Código de barras: </label><br>
			    <input class="form-control input-md" type="text" name="codigo_barras" id="codigo_barras" placeholder="Código de barras"/>
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
			<table class="table table-bordered table-condensed" id="tabla_productos">
				<thead>
					<tr class="th-blue">
						<th class="text-center">#</th>
						<th class="text-center" class="marca">Marca</th>
						<th class="text-center" class="modelo">Modelo</th>
						<th class="text-center" class="descripcion">Descripcion</th>
						<th class="text-center" class="talla">Talla</th>
						<th class="text-center" class="cantidad">Cantidad</th>
						<th class="text-center">Borrar</th>
					</tr>
				</thead>
				<tbody>
				<? for ($i = 0 ; $i < count($productos) ; $i++): ?>
					<tr id="producto_<?= $productos[$i]->id_producto ?>">
						<td class="text-center no-item"><?= $i+1 ?></td>
						<td class="text-center marca" id="marca_<?= $productos[$i]->id_marca ?>"><?= $productos[$i]->marca ?></td>
						<td class="text-center modelo"><?= $productos[$i]->modelo ?></td>
						<td class="text-center descripcion"><?= $productos[$i]->descripcion ?></td>
						<td class="text-center precio"><?= $productos[$i]->talla ?></td>
						<td class="text-center cantidad">
							<input name="cantidad" id="cantidad_<?= $productos[$i]->id_producto ?>" />
						</td>
						<td class="text-center">
							<button type="button" class="btn btn-danger btn-sm borrar_p">
								<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
							</button>
						</td>
					</tr>
				<? endfor; ?>
				</tbody>
				<tfoot>
					<tr class="th-blue">
						<th class="text-center">#</th>
						<th class="text-center" class="marca">Marca</th>
						<th class="text-center" class="modelo">Modelo</th>
						<th class="text-center" class="descripcion">Descripcion</th>
						<th class="text-center" class="talla">Talla</th>
						<th class="text-center" class="cantidad">Cantidad</th>
						<th class="text-center">Borrar</th>
					</tr>
				</tfoot>
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
			<button type="button" class="btn btn-warning btn-sm" id="pausar">
				Pausar <span class="glyphicon glyphicon-pause" aria-hidden="true"></span>
			</button>
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
						<th class="text-center">#</th>
						<th class="text-center" class="marca">Marca</th>
						<th class="text-center" class="modelo">Modelo</th>
						<th class="text-center" class="descripcion">Descripcion</th>
					<? foreach ($tallas as $talla): ?>
						<th class="text-center"><?= ucfirst($talla->talla) ?></th>
					<? endforeach; ?>
						<!--th class="text-center" class="precio">Precio</th>
						<th class="text-center">Editar</th>
						<th class="text-center">Borrar</th-->
					</tr>
				</thead>
				<tbody>
				<? for ($i = 0 ; $i < count($productos) ; $i++): ?>
					<tr id="producto_<?= $productos[$i]->id_producto ?>">
						<td class="text-center no-item"><?= $i+1 ?></td>
						<td class="text-center marca" id="marca_<?= $productos[$i]->id_marca ?>"><?= $productos[$i]->marca ?></td>
						<td class="text-center modelo" onchange="validar_modelo(this)"><?= $productos[$i]->modelo ?></td>
						<td class="text-center descripcion"><?= $productos[$i]->descripcion ?></td>
					<? for ($j = 0 ; $j < count($tallas) ; $j++): ?>
						<? if($productos_tallas[$i][$j] != ''): ?>
							<td class="text-center i-codigo check"><?= $productos_tallas[$i][$j] ?></td>
						<? else: ?>
							<td class="text-center i-codigo no-check"></td>
						<? endif; ?>
					<? endfor; ?>
						<td class="text-center precio"><?= $productos[$i]->precio ?></td>
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
				</tbody>
				<tfoot>
					<tr class="th-blue">
						<th class="text-center">#</th>
						<th class="text-center" class="marca">Marca</th>
						<th class="text-center" class="modelo">Modelo</th>
						<th class="text-center" class="descripcion">Descripcion</th>
					<? foreach ($tallas as $talla): ?>
						<th class="text-center"><?= ucfirst($talla->talla) ?></th>
					<? endforeach; ?>
						<!--th class="text-center" class="precio">Precio</th>
						<th class="text-center">Editar</th>
						<th class="text-center">Borrar</th-->
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<!--div class="row">
		<div class="col-sm-12 text-center">
			<nav id="pags">
				<ul class="pagination">
					<li class="first">
						<a href="#" aria-label="Previous" onclick="obtener_productos(this, 1)">
							<span aria-hidden="true">&laquo;</span>
						</a>
					</li>
				<?  /*for ($i = 1 ; $i <= $paginas ; $i++): ?>
					<li class="<?= $i == 1 ? 'active' : ''; ?> pag_<?= $i ?>"><a href="#" onclick="obtener_productos(this, <?= $i ?>)"><?= $i ?></a></li>
				<? endfor;*/ ?>
					<li class="last">
						<a href="#" aria-label="Next" onclick="obtener_productos(this, <?= $paginas ?>)">
							<span aria-hidden="true">&raquo;</span>
						</a>
					</li>
				</ul>
			</nav>
		</div>
	</div-->
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