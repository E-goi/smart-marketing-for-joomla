<?php
/**
 * @version     1.0.1
 * @package     com_egoi
 * @copyright   Copyright (C) 2020. Todos os direitos reservados.
 * @license     MIT LICENSE
 * @author      E-goi
 */

// no direct access
defined('_JEXEC') or die();

JHTML::_('behavior.modal');
JHtml::_('behavior.formvalidation');
$data = $this->viewData;
$lists = $this->egoiLists;
$tags = $this->egoiTags;

$roles = $this->roles;
$all_subscribers = $this->subs;

$mapped_fields = $this->mapped_fields;

if(isset($data['apikey']) && ($data['apikey'])){ ?>

	<script type="text/javascript">
	jQuery(document).ready(function($) {
		
		$('#toolbar').append('<a class="btn btn-success" onclick="submitForm();"><?php echo JText::_('EGOI_SYNC_SAVEBTN');?></a>');

		var listID = '<?php echo $data['list'];?>';
		var role = '<?php echo $data['role'];?>';

	   	$.ajax({
		    type: 'POST',
		    data:({
		        action: 'synchronize',
	        	list: listID,
	        	role: role
		    }),
	    	success:function(data, status) {
	    		resp = JSON.parse(data);
	    		egoi = resp[0];
	    		jm = resp[1];
	    		$('#egoi_sinc_users_jm').hide();
	    		$('#valid_sync').html('<?php echo JText::_('EGOI_SUBSCRIBED');?>:<span><b> '+egoi+'</b></span><p><?php echo JText::_('EGOI_JOOMLA_USERS');?>: <span><b>'+jm+'</b></span><p>');
	    	}
	    });

		$('#import_list').click(function() {
			
			var key = 1;
			$('#valid').hide();
			$('#load').css('display', 'inline-block');
			$.ajax({
			    type: 'POST',
			    data:({
			        key: key
			    }),
			    success:function(data, status) {
			        
			        $('#load').hide();
			        if(status=='404'){
			        	$(this).attr('disabled', 'disabled');
			        		$("#error").show();
			        		$("#valid").hide();
			        }else{
			        	$(this).removeAttr('disabled');
			        		$("#valid").show();
			        		$("#error").hide();
			        }
			    },
			    error:function(status){
			    	$('#load').hide();
			    	if(status){
				    	$(this).attr('disabled', 'disabled');
				    	$("#valid").hide();
				    	$("#error").show();
				    }
			    }
			});
		});

		$('#openFields').click(function (){
			$('#CustomFields').css('display', 'block');

			var get_map_egoi = 1;
			$.ajax({
			    type: 'POST',
			    data:({
		        	get_map_egoi: get_map_egoi
			    }),
		    	success:function(data, status) {
		    		$('#egoi').html(data);
		    	}
		    });
		});

		$('#closeFields').click(function (){
			$('#CustomFields').css('display', 'none');
		});
	});
	function submitForm(){
		document.getElementById("submitForm").click();
	}	

	</script>

	<form action="<?php echo JRoute::_('index.php?option=com_egoi&view=egoi&layout=edit_subscribers'); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="egoi-form" class="form-validate">
		<input type="hidden" name="apikey" value="<?php echo $data['apikey']; ?>" />
		<input type="hidden" name="edit_sett" value="1" />

	    <div>
	    	
	    	<div style="background:#fff;text-align: center;"><?php 
				if($data['sync']) {
					echo '<span style="background:#066;color:#fff;padding:5px;">'; echo JText::_('EGOI_SYNC_ON'); echo '</span><p style="margin-top:10px;">';
					echo JText::_('The plugin is listening to changes in your users and will automatically keep your WP users with the selected E-goi list.'); ?><?php
				} else {
					echo '<span style="background:#900;color:#fff;padding:5px;">'; echo JText::_('EGOI_SYNC_OFF'); echo '</span><p style="margin-top:10px;">';
					echo JText::_('The plugin is currently not listening to any changes in your users.'); 
				}
				
				if($data['sync']) {	?>

					<table class="table" style="background:#fff;">
						<tr valign="top">
							<td scope="row" colspan="2" style="text-align:center;" id="valid_sync">
								<span id="load_sync"></span>
								<p id="egoi_sinc_users_jm"><div class="egoi_sinc_users"><?php echo JText::_('Loading Subscribers Information...');?></div></p>
							</td>
							<td>
							</td>
						</tr>
					</table>
					<?php
				}?>
			</div>
		
			<table class="table">
				<tr>
					<th scope="row"><?php echo JText::_('EGOI_AUTO_SYNC'); ?></th>
					<td class="nowrap">
						<fieldset id="egoi_sync" class="radio btn-group egoi-mg">
	                        <input type="radio" id="egoi_sync0" value="1" name="egoi_sync" <?php if ($data['sync']) echo 'checked'; ?>>
	                        <label for="egoi_sync0" class="btn <?php if ($data['sync']) echo 'btn-success';?>"><?php echo JText::_('EGOI_YES'); ?></label>
	                        <input type="radio" id="egoi_sync1" value="0" name="egoi_sync" <?php if (!$data['sync']) echo 'checked'; ?>>
	                        <label for="egoi_sync1" class="btn <?php if (!$data['sync']) echo 'btn-danger';?>"><?php echo JText::_('EGOI_NO'); ?></label>
						</fieldset>
						<p class="help"><?php echo JText::_('EGOI_SYNC');?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo JText::_('EGOI_SYNC_USERS'); ?></th>
					<td>
						<select name="egoi_list">
							<option disabled>
								<?php echo JText::_('EGOI_SELECT_LIST'); ?>
							</option><?php

							$array_list = '';
							foreach($lists as $list) {
								if($list['title']!=''){?>
									<option value="<?php echo $list['listnum'];?>" <?php echo ($data['list'] == $list['listnum']) ? 'selected>' : '>'; echo $list['title']; ?>
									</option><?php
									$array_list .= $list['listnum'].' - ';
								}
							} ?>
						
						</select>
						<span class="help"><?php echo JText::_('EGOI_SELECT_LIST_TO_SYNC');?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo JText::_('EGOI_ROLES'); ?></th>
					<td>
						<select name="egoi_role" class="form-group">
							<option value=""><?php echo JText::_('EGOI_ALL_ROLES'); ?></option><?php
							foreach($roles as $key_role => $role) {?>
								<option value="<?php echo $role->id;?>" <?php if($data['group'] == $role->id) echo 'selected';?>> <?php echo $role->title;?> </option><?php
							}?>
						</select>
						<span class="help"><?php echo JText::_('EGOI_SYNC_ROLES'); ?></span>

					</td>
				</tr>
                <tr valign="top">
                    <th scope="row"><?php echo JText::_('EGOI_SYNC_USERS_TAGS'); ?></th>
                    <td>
                        <select name="egoi_tags">
                            <option value="">
                                <?php echo JText::_('EGOI_SELECT_TAGS'); ?>
                            </option><?php

                            foreach($tags as $tag) {
                                if($tag['name']!=''){?>
                                    <option value="<?php echo $tag['tag_id'];?>" <?php echo ($data['tag'] == $tag['tag_id']) ? 'selected>' : '>'; echo $tag['name']; ?>
                                    </option><?php
                                }
                            } ?>

                        </select>
                        <span class="help"><?php echo JText::_('EGOI_SELECT_TAG_TO_SYNC');?></span>
                    </td>
                </tr>
	        	<tr>
					<th scope="row"><?php echo JText::_('EGOI_TE');?></th>
					<td class="nowrap">
						<fieldset id="egoi_te" class="radio btn-group egoi-mg">
	                        <input type="radio" id="egoi_te0" value="1" name="egoi_te" <?php if ($data['te']) echo 'checked'; ?>>
	                        <label for="egoi_te0" class="btn <?php if ($data['te']) echo 'btn-success';?>"><?php echo JText::_('EGOI_YES'); ?></label>
	                        <input type="radio" id="egoi_te1" value="0" name="egoi_te" <?php if (!$data['te']) echo 'checked'; ?>>
	                        <label for="egoi_te1" class="btn <?php if (!$data['te']) echo 'btn-danger';?>"><?php echo JText::_('EGOI_NO'); ?></label>
						</fieldset>
						<span class="help"><a target="_blank" href="https://login.egoiapp.com/#/login/?action=login&from=<?php echo urlencode('/?action=ui#/trackengage/configurations');?>">
						<?php echo JText::_('First activate Track&Engage in E-goi');?></a><p>
						</span>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php echo JText::_('EGOI_SYNC_JOOMLA_USERS'); ?></th>
					<td class="nowrap">
						<button type="button" class="btn btn-info egoi-mg" id="import_list"><?php echo JText::_('EGOI_SYNC_BTN');?></button>
						<span id="load" style="display:none;"></span>
						<span id="valid" style="display:none;"></span>

						<span class="help"><?php echo JText::_('Syncronize All Users');?></span>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php echo JText::_('EGOI_SYNC_CUSTOM_FIELDS'); ?></th>
					<td>
						<a class="btn btn-info egoi-mg" data-toggle="modal" data-target="#CustomFields" id="openFields"><?php echo JText::_('EGOI_MAP_CUSTOM_FIELDS'); ?></a>
						<p class="help"><?php echo JText::_('EGOI_SYNC_CUSTOM_FIELDS_HELP'); ?></p>
					</td>
				</tr>
				<input type="submit" name="submit" id="submitForm" style="display: none;">
			</table>

	    </div>

	    <?php echo JHtml::_('form.token'); ?>
	    <div class="clr"></div>

	</form>

		<!-- Custom Fields -->
			<div class="modal fade" id="CustomFields" role="dialog" style="display: none;width: 50%;left: 65%;">
			    <div class="modal-dialog">
			      <div class="modal-content">
			        <div class="modal-header egoi-map-header">
			          <h1 class="modal-title" style="color:#fff;"><?php echo JText::_('Map Custom Fields');?></h1>
			        </div>
			        <div class="modal-body">
					        
				        <div class="egoi-35">
				        	<label><b>Joomla Fields</b></label>
							<select name="jm_fields" id="jm_fields" class="form-control" style="width:180px;">
								<option value=""><?php echo JText::_('Select Field');?></option>
								<option value="name">Name</option>
								<option value="username">Login Name</option>
								<option value="email">E-mail</option>
							</select>		
						</div>
						<div class="egoi-35">
							<label><b>E-goi Fields</b></label>
							<select name="egoi" id="egoi" style="width:180px;display:inline;">
								<option value="">loading ...</option>
							</select>
						</div>
						<div class="egoi-25" style="display: inline-block;">
							<button class="btn btn-primary" type="button" id="save_map_fields" disabled><?php echo JText::_('EGOI_ASSIGN'); ?>
							<div id="load_map" style="display:none;"></div>
							</button>
						</div>
						<hr>
						
						<div id="all_fields_mapped">
							<div>
								<b><?php echo JText::_('Mapped Fields');?></b> <div class="load_map" style="display:none;"></div>
							</div><?php
							if(isset($mapped_fields)){
								foreach ($mapped_fields as $key => $row) { ?>
									<div id="egoi_fields_<?php echo $row->id;?>">
										<div class='col-map-field egoi-35'><?php echo $row->jm_name;?></div> 
										<div class='col-map-field egoi-35'><?php echo $row->egoi_name;?></div>
										<div style="padding-top: 10px;">
											<button type='button' id="field_<?php echo $row->id;?>" class='egoi_fields btn btn-info' data-target="<?php echo $row->id;?>">Delete
											</button>
										</div>
									</div><?php
								}
							} ?>
						</div>
						<div id="error_map" class="alert col-md-8 col-md-offset-2" style="display:none;color:#900;font-size:16px;">
							<?php echo JText::_('The selected fields are already mapped!'); ?>
						</div>

			        </div>
			        <div class="modal-footer" style="margin-top: 50%;">
			        	<button type="button" class="btn btn-default" id="closeFields" data-dismiss="modal">Close</button>
			        </div>
			      </div>

			    </div>
			</div>
		<!-- /Custom Fields --><?php
}else{

	echo '<div class="alert alert-danger text-center"><h2>';
		echo JText::_('To proceed in this module you must insert your API Key <a href="index.php?option=com_egoi">here</a>!');
	echo '</h2></div>';

}

