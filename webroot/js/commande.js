var odin_order_resources = $('#odin_order_resources').DataTable();
//var plan_resources = $('#plan_resources').DataTable();

$(document).ready(function() 
{
	$('#step1').click(function() {
		var code_client = $('#code_client').val();
		var ordernum = $('#n_commande_odin').val();
		var company = $('#company').val().toLowerCase();
		goToStep2(company);		
	});

	$('#back').click(function() {
		$('.step2').hide();
		$('.step1').show();
	});

	$('#company').change(function() {
		var company = $(this).val();
		if(company == 'PARALLELS'){
			$('#n_commande_odin').removeAttr('disabled');
		}else{
			$('#n_commande_odin').attr('disabled','disabled'); 
			$('#n_commande_odin').val('');
		}
	});
	$('#list_pack').change(function() {
		var plan_id = $(this).val();
		console.log('plan id',plan_id);
		getPlanRessources(plan_id);
		
	});

	$("#checkAll").click(function () {
	     $('.not_inclu').not(this).prop('checked', this.checked);
	 });

	$(".cmd-manage-comment").click(function (event) {
		event.preventDefault();
		var modal = $('#comment-modal');
		var commande_id = $(this).parents('.cmd-actions').data('commande-id');
		modal.find('.commande_id').val(commande_id);
		console.log( 'commande_id' , commande_id );
		modal.find('.status-msg').empty();
		modal.find('.comment-list').empty();
		modal.modal();
		$.ajax({
		    url: "/departement_administratif/get_cmd_comments/"+commande_id,
		    method : 'GET',
			beforeSend: function() {
				$('.loading-comment').show();
			},
		    success: function(result){
 		    	if(result.status == true){
 		    		modal.find('.comment-list').empty();
					showComment(result);
				}else{
					modal.find('.comment-list').empty().append('<blockquote>'+ result.message +'</blockquote>');
				}	
				$('.loading-comment').hide();    	
		    }
		});		
		
	 });

	$(".save_comment").click(function () {
	     var form_data = $('#comment_form').serialize();
	     var modal = $('#comment-modal');
	     modal.remove('.alert');
		$.ajax({
		    url: "/departement_administratif/save_cmd_comment",
		    method : 'POST',
		    data: form_data,
			beforeSend: function() {
				$('.loading-comment').show();
			},
		    success: function(result){
		    	modal.find('.comment-list').empty();
		    	$('#comment_form #comment-text').val('');
    			modal.find('.comment_action').val('add');
				modal.find('.comment_id').val('');
 		    	if(result.status == true){
 		    		modal.find('.status-msg').empty().append('<div class="alert alert-success" role="alert">'+ result.message +'</div>');
 		    		showComment(result);
				}else{
					modal.find('.status-msg').empty().append('<blockquote>'+ result.message +'</blockquote>');
				}
				$('.loading-comment').hide(); 
		    }
		});
	 });

	$(document).on('click','.edit_comment',function (argument) {
		var modal = $('#comment-modal');
		var comment_id = $(this).parents('.line_comment').data('comment-id');
		var comment = $(this).parents('.line_comment').find('.comment-text').html();
		modal.find('#comment-text').val(comment);
		modal.find('.comment_action').val('edit');
		modal.find('.comment_id').val(comment_id);
		console.log( 'comment_id'+comment_id , comment );
	});

	$(document).on('click','.delete_comment',function (argument) {
		var modal = $('#comment-modal');
		
		var ligne_comment = $(this).parents('.line_comment');
		var comment_id = $(ligne_comment).data('comment-id');
		$.ajax({
		    url: "/departement_administratif/delete_cmd_comment/"+comment_id,
		    method : 'GET',
			beforeSend: function() {
				$('.loading-comment').show();
			},
		    success: function(result){
 		    	if(result.status == true){

 		    		$(ligne_comment).remove();
				}	
				$('.loading-comment').hide();    	
		    }
		});	

	});

});

function showComment (result) {
	var modal = $('#comment-modal');
	$.each( result.data, function( k, comment ) {

		var html = '<blockquote class="line_comment" data-comment-id="'+comment.id+'">'+
		'<span class="pull-right delete_comment action_icon" ><i class="fa fa-times" aria-hidden="true"></i></span>'+
		'<span class="pull-right edit_comment action_icon" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>'+
		'<span class="comment-text">'+ comment.commentaires +'</span>'+
		'</blockquote>';
		modal.find('.comment-list').append(html);
	});
}

function goToStep2(company) {

	if(company == 'parallels'){
		var ordernum = $('#n_commande_odin').val();
		if(ordernum != ''){
			showStep2(company)
			getOdinOrder(ordernum);
		}else{
			$('#n_commande_odin').addClass('has-error');
			alert('Il faut indiquer le N° commande Odin');
		}
	}else{
		if ( $.fn.DataTable.isDataTable( '#odin_order_resources' ) ) {
		  odin_order_resources.clear().draw();
		}
		showStep2(company);
		getListOfPack();
		
	}
}

function showStep2(company) {
	var c = {'mol': 'parallels','parallels':'mol'};
	$('.step1').hide();
	$('.step2').show();

	$('.step2 .if-'+c[company]).hide();
	$('.step2 .if-'+company).show();

}

function getListOfPack() {
    $.ajax({
    	url: "/departement_administratif/get_list_pack", 
	    beforeSend: function() {
	        $('.loader-over').show();
	    },
    	success: function(result){
    		$.each( result, function( key, cat ) {
			  //console.log( key , cat.plans );
			  $('#list_pack').append('<optgroup label="'+cat.plan_cat_name+'">');
			  $.each( cat.plans, function( k, pack ) {
			  	var plan_name = pack.plan_name.split('fr');
			  	$('#list_pack').append('<option value="'+pack.plan_id+'">'+ plan_name[1] +'</option>');
			  });
			  $('#list_pack').append('</optgroup>');
			});
			$('.loader-over').hide();

	    }
	});
}

function getOdinOrder(ordernum)
{
	
    odin_order_resources =$('#odin_order_resources').DataTable( {
        "processing": true,
        "serverSide": true,
        "searching": false,
        "paging": false,
        "info": false,
        "destroy": true,
        "language": {
	    	"processing": "Recuperer les details de la commande depuis Odin......"
		},
        "ajax": "/departement_administratif/get_odin_orders/"+ordernum,
        "columns": [
            { 
            	"data": "code_article",    
				"render": function ( data, type, row, meta ) {
					//console.log('row',meta);
				  	return '<input type="text" class="form-control row_'+meta.row+'" name="cmds['+meta.row+'][code_article]" id="code_article_'+meta.row+'" value="'+data+'">';
				},
			}, 
            { 
            	"data": "designation",
               	"render": function ( data, type, row, meta ) {
            		//console.log('row',meta);
			      	return '<textarea class="form-control row_'+meta.row+'" name="cmds['+meta.row+'][designation]" id="designation_'+meta.row+'">'+data+'</textarea>';
			 	},
			},
            {
				"data": "qte",
				"render": function ( data, type, row, meta ) {
					//console.log('row',meta);
					return '<input type="text" class="row_'+meta.row+'" name="cmds['+meta.row+'][qte]" id="qte_'+meta.row+'" value="'+data+'">';
				},
			},
            {
				"data": "montant",
				"render": function ( data, type, row, meta ) {
					//console.log('row',meta);
					return '<input type="text" class="row_'+meta.row+'" name="cmds['+meta.row+'][montant]" id="montant_'+meta.row+'" value="'+data+'">';
				},
			}
        ],
        "order": [[1, 'asc']]
    } );

	odin_order_resources.ajax.reload();
}

function getPlanRessources(plan_id)
{
	
    plan_resources = $('#plan_resources').DataTable( {
    	"ordering": false,
        "processing": true,
        "serverSide": true,
        "searching": false,
        "paging": false,
        "info": false,
        "destroy": true,
        "language": {
	    	"processing": "Recuperer les resources......"
		},
        "ajax": "/departement_administratif/get_plan_resources/"+plan_id,
        "columns": [
            { 
            	"data": "is_included",    
				"render": function ( data, type, row, meta ) {
					//console.log('row',row);
					var checked , checked_class= '';
					var included_class = 'not_inclu';
					var html = '<div style="position: relative; display: block;">';
					if(data == 1){
						html += '<div class="disabled-input"></div>';
						included_class = '';
						checked_class = 'checked_input';
						checked = 'checked';
					}
					var class_ck = included_class +' '+checked_class;
					html += '<input type="checkbox" class="'+class_ck+' select row_'+meta.row+'" name="cmds['+meta.row+'][is_included]" id="is_included_'+meta.row+'" '+checked+'></div>';
				  	return html;
				},
			},
            { 
            	"data": "resource_id",    
				"render": function ( data, type, row, meta ) {
					//console.log('row',meta);
				  	return '<input type="text" class="form-control row_'+meta.row+'" name="cmds['+meta.row+'][code_article]" id="code_article_'+meta.row+'" value="'+data+'">';
				},
			}, 
            { 
            	"data": "resource_name",
               	"render": function ( data, type, row, meta ) {
            		//console.log('row',meta);
			      	return '<textarea class="form-control row_'+meta.row+'" name="cmds['+meta.row+'][designation]" id="designation_'+meta.row+'">'+data+'</textarea>';
			 	},
			},
            { 
            	"data": null,
               	"render": function ( data, type, row, meta ) {
            		//console.log('row',meta);
					return '<input type="text" class="form-control row_'+meta.row+'" name="cmds['+meta.row+'][qte]" id="qte_'+meta.row+'" value="1">';			 	},
			},			
            {
				"data": "resource_price",
				"render": function ( data, type, row, meta ) {
					//console.log('row',meta);
					return '<input type="text" class="row_'+meta.row+'" name="cmds['+meta.row+'][montant]" id="montant_'+meta.row+'" value="'+data+'">';
				},
			}
        ],
        "order": [[1, 'asc']]
    } );

	//plan_resources.ajax.reload();
}