<table class="table table-bordered table-condensed">
	<tr class="th-blue">
		<th>#</th>
		<th>Usuario</th>
		<th>Contraseña</th>
	<? foreach ($permisos as $permiso): ?>
		<th><?= ucfirst($permiso->permiso) ?></th>
	<? endforeach; ?>
		<th>Editar</th>
		<th>Borrar</th>
	</tr>
<?  for ($i = 0 ; $i < count($usuarios) ; $i++): ?>
	<tr id="usuario_<?= $usuarios[$i]->id_usuario ?>">
		<td class="text-center no-item"><?= $i+1 ?></td>
		<td class="text-center"><?= $usuarios[$i]->usuario ?></td>
		<td class="text-center"><?= $usuarios[$i]->password ?></td>
	<? for ($j = 0 ; $j < $cont_permisos ; $j++): ?>
		<? if($u_permisos[$i][$j] == 1): ?>
			<td class="text-center check"><input type="checkbox" class="input_req" disabled checked></td>
		<? else: ?>
			<td class="text-center no-check"><input type="checkbox" class="input_req" disabled></td>
		<? endif; ?>
	<? endfor; ?>
		<td class="text-center">
			<button type="button" class="btn btn-info btn-sm editar_u">
				<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
			</button>
		</td>
		<td class="text-center">
			<button type="button" class="btn btn-danger btn-sm borrar_u" <? if($usuarios[$i]->id_usuario ==  '1'){ echo "disabled"; } ?>>
				<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
			</button>
		</td>
	</tr>
<? endfor; ?>
</table>