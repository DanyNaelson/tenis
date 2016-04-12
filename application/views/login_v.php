<section>
	<div class="container">
		<div class="row">
			<div class="col-sm-4">
			</div>
			<div class="col-sm-4 text-center">
				<form id="login">
					<div class="form-group has-feedback">
						<input type="text" class="form-control" id="usuario" name="usuario" placeholder="Usuario">
						<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
						<span class="label label-danger"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" class="form-control" id="password" name="password" disabled="disabled" placeholder="Contraseña">
						<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
						<span class="label label-danger"></span>
					</div>
					<button type="button" class="btn btn-primary btn-lg" id="entrar">Iniciar Sesión</button><br>
					<span id="loading" class="alert alert-info" role="alert"></span>
				</form>
			</div>
		</div>
	</div>
</section>