<?php $this->assign('title', $title); ?>
<div class="col-md-12">
	<?= $this->Flash->render() ?>
    <h1>Créer commande</h1><hr>
	<form class="form-horizontal" id="cree_commande" method="post">
		<div class="step1 col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" >Type de vente</label> 
				<div class="col-md-8">
					<select name="type_commande" id="type" class="form-control">
						<?php foreach ($type_vente as $key => $value) { ?>
							<option value="<?= $value ?>"><?= $value ?></option> 
						<?php } ?>
					</select>
				</div>	
			</div>
			<div class="form-group">
				<label class="control-label col-md-4" >SI de production </label> 
				<div class="col-md-8">
					<select name="company" id="company" class="form-control">
						<?php foreach ($si_production as $key => $value) { ?>
							<option value="<?= $key ?>"><?= $value ?></option> 
						<?php } ?>
					</select>
				</div>	
			</div>
			<div class="form-group">
				<label class="control-label col-md-4" >N° commande Odin</label>
				<div class="col-md-8">
					<input type="text" name="n_commande_odin" class="form-control" id="n_commande_odin" disabled>
				</div>	
			</div>
			<div class="form-group">
				<label class="control-label col-md-4" >Commercial</label>
				<div class="col-md-8">					
					<select name="code_commercial" id="code_commercial" class="form-control">
						<?php foreach ($commercials as $key => $value) { ?>
							<option value="<?= $key ?>"><?= $value ?></option> 
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">        
				<div class="col-md-offset-4 col-md-4">
					<button type="button" class="col-sm-12 btn btn-info" id="step1">Suivant</button>
				</div>
			</div>
		</div>	

		
		<div class="step2 col-md-12 resources_list"  style="display:none;">
			<div class="if-mol" style="display:none;">
		
				<div class="form-group">
					<label class="control-label col-md-2" >Liste des pack</label> 
					<div class="col-md-6">
						<select id="list_pack" class="form-control">
							<option value="">Selectionner </option> 
						</select>
					</div>	
				</div>
				<table id="plan_resources" class="table table-bordered" cellspacing="0" width="100%">
			        <thead>
			            <tr>
			            	<th> <input type="checkbox" id="checkAll"></th>
			                <th>Code article</th>
			                <th>Designation</th>
			                <th>Qte</th>
			                <th>Montant</th>
			            </tr>
			        </thead>

			    </table>
			</div>
			<div class="if-parallels resource_liste" style="display:none;">
				<table id="odin_order_resources" class="table table-bordered" cellspacing="0" width="100%">
			        <thead>
			            <tr>
			                <th>Code article</th>
			                <th>Designation</th>
			                <th>QTE</th>
			                <th>Montant</th>
			            </tr>
			        </thead>

			    </table>
			</div>

			<div class="form-group">    
				<div class="col-sm-offset-8 col-sm-2">
					<button type="button" class="col-sm-12 btn btn-info" id="back">Prev</button>
				</div>    
				<div class="col-sm-2 last-step">
					<button type="submit" class="col-sm-12 btn btn-info cree-cmd" id="step2">Créer commande</button>
				</div>
			</div>			
		</div>

	</form>
	<div class="loader-over">
	<div class="loader">
	    <img src="/img/loader.gif" style="width: 54%;">
	</div>
	</div>
</div>

<?php echo $this->Html->script('jquery.dataTables.min.js', ['block' => 'script']); ?>
<?php echo $this->Html->script('commande.js', ['block' => 'script']); ?>