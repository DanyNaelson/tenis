<table class="table table-bordered table-hover table-condensed">
	<tr>
		<th>#</th>
		<th>Usuario</th>
		<th>Contraseña</th>
	<? foreach ($permisos as $permiso): ?>
		<th><?= ucfirst($permiso->permiso) ?></th>
	<? endforeach; ?>
		<th>Editar</th>
		<th>Borrar</th>
	</tr>
<? for ($i = 0 ; $i < count($usuarios) ; $i++): ?>
	<tr id="usuario_<?= $i ?>">
		<td class="text-center"><?= $usuarios[$i]->id_usuario ?></td>
		<td class="text-center"><?= $usuarios[$i]->usuario ?></td>
		<td class="text-center"><?= $usuarios[$i]->password ?></td>
	<? for ($j = 0 ; $j < 8 ; $j++): ?>
		<? if(isset($u_permisos[$j]->id_permiso)): ?>
			<? if($u_permisos[$j]->id_usuario == $usuarios[$i]->id_usuario): ?>
			<td class="text-center"><?= $u_permisos[$j]->id_permiso ?></td>
			<? elseif($u_permisos[$j]->id_permiso == $j+1): ?>
			<td class="text-center">0</td>
			<? endif; ?>
		<? endif; ?>
	<? endfor; ?>
		<td class="text-center">
			<button type="button" class="btn btn-info btn-sm">
				<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
			</button>
		</td>
		<td class="text-center">
			<button type="button" class="btn btn-danger btn-sm">
				<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
			</button>
		</td>
	</tr>
<? endfor; ?>
</table>