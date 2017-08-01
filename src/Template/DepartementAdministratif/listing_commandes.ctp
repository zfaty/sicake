<h1>Listes des Commandes</h1>



<div class="col-md-12">
	<table id="cmd_liste" class="table table-bordered" cellspacing="0" width="100%">
        <thead>

        </thead>
        <tbody>
        	<?php foreach ($cmds as $key => $cmd) { 
        		$bc_odin ='';
        		$company_icon = '<img src="http://www.magic.fr/favicon.ico"></a>';
        		if($cmd['cmd']['company'] == 'PARALLELS'){
        			$company_icon = '<img src="http://www.parallels.com//typo3conf/ext/parallels/Resources/Public/img/favicon.ico" ></a>';
        		}
        		if(!empty($cmd['cmd']['n_commande_parallels'])){
        			$bc_odin = '/ BC Odin : '.$cmd['cmd']['n_commande_parallels'];
        		}
        		?>
	            <tr class="success">
	            	<th></th>
	                <th>N°</th>
	                <th>Type de Commande</th>
	                <th>Créateur</th>
	                <th>Date</th>
	                <th>Actions</th>
	            </tr>
        		<tr>
        			<td><?=$company_icon?></td>
        			<td><?=$cmd['cmd']['commande_id']?></td>
        			<td><?=$cmd['cmd']['type_commande']?></td>
        			<td><?=$cmd['cmd']['commercial']?></td>
        			<td><?=$cmd['cmd']['date_creation']?></td>
        			<td>
        				<?=$bc_odin?>
        				<div class="cmd-actions" data-commande-id="<?=$cmd['cmd']['commande_id']?>" title="Ajouter un commontaire">
        					<a class="cmd-manage-comment" >
        						<i class="fa fa-commenting" aria-hidden="true"></i>
        					</a>
        				</div>	
        			</td>
        		</tr>
        		<tr>
        			<td colspan="6" class="cmd_detail">
        				<table class="table table-bordered table_cmd_article" cellspacing="0" width="100%">
        					<thead>
        						<tr class="info">
	        						<th>Code article</th>
	        						<th>Description</th>
	        						<th>Quantite</th>
	        						<th>Prix</th>
        						</tr>
        					</thead>
        					<tbody>
				        	<?php foreach ($cmd['cmd_articles'] as $k => $article) { ?>
			        		<tr>
			        			<td><?=$article['code_article']?></td>
			        			<td><?=$article['description']?></td>
			        			<td><?=$article['qte']?></td>
			        			<td><?=$article['prix_ht']?></td>
			        		</tr>
			        		<?php } ?>
        					</tbody>
        				</table>
        			</td>
        		</tr>

        	<?php } ?>
        </tbody>
            
    </table>
</div>


<div id="comment-modal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Laisser un commentaire</h4>
      </div>
      <div class="modal-body">
      	<div class="row">
	        <form id="comment_form">
	          <div class="form-group">
	            <label for="message-text" class="control-label">Commentaire:</label>
	            <input class="comment_action" name="comment_action" type="hidden" value="add">
	            <input class="comment_id" name="comment_id"  type="hidden" value="">
	            <input class="commande_id" name="commande_id" type="hidden" value="">
	            <textarea class="form-control" id="comment-text" name="comment"></textarea>
	          </div>
	        </form>
      	</div>
      	<div class="row">
      		<div class="col-md-12">
      			<h4>Liste des commentaires : </h4>
				<div class="loading-comment">
					<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
					<span class="sr-only">Loading...</span>
				</div>	
        <div class="status-msg">
        </div>
				<div class="comment-list">

				</div>		
		
      		</div>
      	</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-primary save_comment">Valider</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php echo $this->Html->script('jquery.dataTables.min.js', ['block' => 'script']); ?>
<?php echo $this->Html->script('commande.js', ['block' => 'script']); ?>