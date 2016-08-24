<div class="container">
  <div class="row">
    <div class="col-sm-12 text-center">
      <h1><?= $modulo ?></h1>
    </div>
  </div>
  <div class="row">
    &nbsp;
  </div>
<? if(isset($permisos[0]->permiso)): ?>
  	<div class="row">
  		<div class="col-sm-6 btn_menu text-center">
  			<a class="btn btn-primary btn-lg" href="/inventarios/<?= $permisos[0]->permiso ?>/" role="button"><?= ucfirst($permisos[0]->permiso) ?></a>
  		</div>
  	<? if(isset($permisos[1]->permiso)): ?>
  		<div class="col-sm-6 btn_menu text-center">
  			<a class="btn btn-primary btn-lg" href="/inventarios/<?= $permisos[1]->permiso ?>/" role="button"><?= ucfirst($permisos[1]->permiso) ?></a>
  		</div>
  	<? endif ?>
	</div>
<? endif ?>
<? if(isset($permisos[2]->permiso)): ?>
	<div class="row">
  		<div class="col-sm-6 btn_menu text-center">
  			<a class="btn btn-primary btn-lg" href="/inventarios/<?= $permisos[2]->permiso ?>/" role="button"><?= ucfirst($permisos[2]->permiso) ?></a>
  		</div>
  	<? if(isset($permisos[3]->permiso)): ?>
  		<div class="col-sm-6 btn_menu text-center">
  			<a class="btn btn-primary btn-lg" href="/inventarios/<?= $permisos[3]->permiso ?>/" role="button"><?= ucfirst($permisos[3]->permiso) ?></a>
  		</div>
  	<? endif ?>
	</div>
<? endif ?>
<? if(isset($permisos[4]->permiso)): ?>
	<div class="row">
  		<div class="col-sm-6 btn_menu text-center">
  			<a class="btn btn-primary btn-lg" href="/inventarios/<?= $permisos[4]->permiso ?>/" role="button"><?= ucfirst($permisos[4]->permiso) ?></a>
  		</div>
  	<? if(isset($permisos[5]->permiso)): ?>
  		<div class="col-sm-6 btn_menu text-center">
  			<a class="btn btn-primary btn-lg" href="/inventarios/<?= $permisos[5]->permiso ?>/" role="button"><?= ucfirst($permisos[5]->permiso) ?></a>
  		</div>
  	<? endif ?>
	</div>
<? endif ?>
<? if(isset($permisos[6]->permiso)): ?>
	<div class="row">
  		<div class="col-sm-6 btn_menu text-center">
  			<a class="btn btn-primary btn-lg" href="/inventarios/<?= $permisos[6]->permiso ?>/" role="button"><?= ucfirst($permisos[6]->permiso) ?></a>
  		</div>
  	<? if(isset($permisos[7]->permiso)): ?>
  		<div class="col-sm-6 btn_menu text-center">
  			<a class="btn btn-primary btn-lg" href="/inventarios/<?= $permisos[7]->permiso ?>/" role="button"><?= ucfirst($permisos[7]->permiso) ?></a>
  		</div>
  	<? endif ?>
	</div>
<? endif ?>
<? if(isset($permisos[8]->permiso)): ?>
  <div class="row">
      <div class="col-sm-6 btn_menu text-center">
        <a class="btn btn-primary btn-lg" href="/inventarios/<?= $permisos[8]->permiso ?>/" role="button"><?= ucfirst($permisos[8]->permiso) ?></a>
      </div>
    <? if(isset($permisos[9]->permiso)): ?>
      <div class="col-sm-6 btn_menu text-center">
        <a class="btn btn-primary btn-lg" href="/inventarios/<?= $permisos[9]->permiso ?>/" role="button"><?= ucfirst($permisos[9]->permiso) ?></a>
      </div>
    <? endif ?>
  </div>
<? endif ?>
<? if(isset($permisos[10]->permiso)): ?>
  <div class="row">
      <div class="col-sm-12 btn_menu text-center">
        <a class="btn btn-primary btn-lg" href="/inventarios/<?= $permisos[10]->permiso ?>/" role="button"><?= ucfirst($permisos[10]->permiso) ?></a>
      </div>
  </div>
<? endif ?>
</div>