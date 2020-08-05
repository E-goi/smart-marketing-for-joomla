<?php
/**
 * @version     1.0.1
 * @package     com_egoi
 * @author      E-goi
 * @link        https://www.e-goi.com
 * @copyright   Copyright (c) 2020 E-GOI. All rights reserved.
 * @license		MIT License
 */

// no direct access
defined('_JEXEC') or die();

$doc = JFactory::getDocument();
JHtml::_('jquery.framework');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
$custom_data = $this->viewData;
$data = $this->forms;
$lists = $this->egoiLists;

$forms = $this->egoi_forms;
$count_area = $this->area;

if(isset($custom_data['apikey']) && ($custom_data['apikey'])){

	if(isset($_GET['form']) && ($_GET['type']) && ($_GET['form'] <= 5)){
		$decl = '';
		if($_GET['type'] == 'iframe'){
			$decl = '$("#toolbar").append("<a class=\'preview btn btn-default\'>'.JText::_('Preview').' <span class=\'dashicons dashicons-welcome-view-site\'></span></a>");';
		}

		$doc->addScriptDeclaration('
		    jQuery(document).ready(function ($){
		    	$("#toolbar").append("<a class=\'btn btn-success\' onclick=\'submitForm();\'>'.JText::_('EGOI_FORMS_SAVEBTN').'</a>");
		    	$("#toolbar").append("<a class=\'btn btn-default\' href=\''.JRoute::_('index.php?option=com_egoi&view=egoi&layout=edit_forms').'\'>'.JText::_('Go to Forms List').'</a>");
		    	'.$decl.'
				
				jQuery(\'.preview\').click(function() {
					$("#formid_egoi").change();
				});

		    	jQuery(\'#preview\').css(\'margin-top\', \'100%\');
		        jQuery(\'#formid_egoi\').change(function() {
					jQuery("#preview").css(\'margin-top\', \'0%\');
					var e = document.getElementById(\'formid_egoi\');
					var result = e.options[e.selectedIndex].value;
					jQuery("#load").show();

					if(result != \'\'){
						jQuery.ajax({
						    type: \'POST\',
						    data:({
						        url_egoi: result
						    }),
						    success:function(data, status) {
						        jQuery(\'#egoi_form_inter\').html(data);
						        jQuery(\'#preview\').modal(\'show\');
						        jQuery("#load").hide();
						        jQuery("#preview").css(\'margin-top\', \'0%\');
						    },
						    error:function(status){
						    	jQuery("#load").hide();
						    	if(status){
							    	jQuery("#valid").hide();
							    	jQuery("#error").show();
							    	jQuery(\'#preview\').css(\'margin-top\', \'100%\');
							    }
						    }
						});
					}else{
						jQuery("#load").hide();
						jQuery(\'#preview\').css(\'margin-top\', \'100%\');
					}
				});
		    });    
		'); ?>
	
		<script type="text/javascript">
			function submitForm(){
				document.getElementById("submit_btn").click();
			}

			function previewForm(){
				document.getElementById("preview_btn").click();
			}
		</script>

		<!-- Main Content -->
		<div class="main-content col col-4">
			<form method="get" action="">
				<input type="hidden" name="option" value="com_egoi">
				<input type="hidden" name="view" value="egoi">
				<input type="hidden" name="layout" value="edit_forms">
				<input type="hidden" name="form" value="<?php echo $_GET['form'];?>">
				<table class="table" style="background:#ddd;">
					<tr valign="top">
						<td colspan="2" style="text-align: left;"><?php echo JText::_('Select the Form Type you want');?> &nbsp; 
						<select name="type" style="width: 250px;" id="form_choice" onchange="this.form.submit();">
							<option value="" disabled><?php echo JText::_('Type');?></option>
							<option value="popup" <?php if($_GET['type'] == 'popup') echo 'selected';?>><?php echo JText::_('E-goi Popup');?></option>
							<option value="html" <?php if($_GET['type'] == 'html') echo 'selected';?>><?php echo('E-goi Advanced HTML');?></option>
							<option value="iframe" <?php if($_GET['type'] == 'iframe') echo 'selected';?>><?php echo JText::_('E-goi Iframe');?></option>
						</select></td>
					</tr>
				</table>
			</form>

			<form action="<?php echo JRoute::_('index.php?option=com_egoi&view=egoi&layout=edit_forms');?>" method="post" enctype="multipart/form-data" name="adminForm" id="egoi-form" class="form-validate">
				<input type="hidden" name="form_type" value="<?php echo $_GET['type'];?>">
				<input type="hidden" name="form_id" value="<?php echo $_GET['form'];?>">
				<input type="hidden" name="token_form" value="1">
				<table class="table">
					<tr valign="top">
						<th><?php echo JText::_('Form Title');?></th>
						<td>
							<input type="hidden" name="form_id" value="<?php echo $_GET['form'];?>">
							<input type="text" name="form_title" size="30" style="width: 30%;margin-left: 90px;" value="<?php echo $data->form_title;?>" id="title" spellcheck="true" autocomplete="off" placeholder="Title" style="line-height: initial;" required pattern="\S.*\S">
						</td>
					</tr>
				</table>
				
				<?php include('includes/egoi_form_content.php'); ?>

				<div style="display: none;">
					<button type="submit" id="submit_btn" class="btn btn-primary">&nbsp;</button><?php
					if($data->enable){?>
						<p style="padding-left: 20px;">
							<a target="_blank" id="preview_btn" class="btn btn-secondary" href="<?php echo str_replace('/administrator', '', JRoute::_('index.php?option=com_egoi&view=egoi&layout=form&form='.$_GET['form']));?>">
								<span class="dashicons dashicons-welcome-view-site" style=""></span> &nbsp;
							</a>
						</p><?php
					} ?>
				</div>
			</form>
		</div>
		
		<?php
		if ($_GET['type'] == 'iframe'){ ?>
			
			<div class="modal fade" id="preview" role="dialog" style="width:70%;height:500px;">
			    <div class="modal-dialog">
			     	<div class="modal-content">
				        <div class="modal-body"><?php 
				        	if($data->form_content){?>
				        		<div id="egoi_form_inter">
				        			<iframe src="//<?php echo $data->url;?>" style="border: 0 none; max-height:450px;" onload="window.parent.parent.scrollTo(0,0);"></iframe>
				        		</div><?php
							}else{ ?>
				        		<div id="egoi_form_inter"></div><?php
							} ?>
				        </div>
			      	</div>

			    </div>
			</div><?php
		} ?>

		<?php
	}else{ ?>
			
		<script type="text/javascript">
			jQuery(document).ready(function ($){
		    	$("#toolbar").append("<a class='btn btn-success' onclick='submitForm();'><?php echo JText::_('Create New form');?></a>");
		    });

			function submitForm(){
				document.getElementById("btn_create_form").click();
			}
		</script>

		<style type="text/css">
			#active{
				background: url('components/com_egoi/assets/images/check.png') no-repeat;
		   		display: inline-block;
		   		width: 20px;
		   		height: 20px;
		   		background-size: 20px;
			}
			#inactive{
				background: url('components/com_egoi/assets/images/error.png') no-repeat;
		   		display: inline-block;
		   		width: 20px;
		   		height: 20px;
		   		background-size: 20px;
			}
		</style>
		<div class="main-content col col-4">
			<h2><?php echo JText::_('Max number of forms:');?> 5</h2>
			<table border='0' class="table table-hover">
			<thead>
				<tr>
					<th><?php echo JText::_('Form ID');?></th>
					<th><?php echo JText::_('Title');?></th>
					<th><?php echo JText::_('Active');?></th>
					<th><?php echo JText::_('Option');?></th>
				</tr>
			</thead><?php

			$i = 0;
			foreach ($data as $key => $forms){?>
				<tr>
					<td><?php echo $forms->id;?></td>
					<td><?php echo $forms->form_title;?></td>
					<td><?php echo $forms->enable ? '<div id="active">&nbsp;</div>' : '<div id="inactive">&nbsp;</div>';?></td>
					<td><a href="<?php echo JRoute::_('index.php?option=com_egoi&view=egoi&layout=edit_forms&form='.$forms->id.'&type='.$forms->form_type);?>">
					<?php echo JText::_('Edit');?></a></td>
				</tr><?php
				$i++;
			}	

			if($i == 0){
				echo "<td colspan='4'>";
					echo JText::_('Subscriber Forms are empty');
				echo "</td>";
			} ?>
			</table>

			<p><?php
				if($i >= 5){ ?>
					<a id="disabled" class='btn btn-primary'><?php echo JText::_('Create a new form');?></a><?php
				}else{ ?>
					<a style="display: none;" href="<?php echo JRoute::_('index.php?option=com_egoi&view=egoi&layout=edit_forms&form='.($i+1).'&type=html');?>" id='btn_create_form'></a><?php
				} ?>
			</p>
		</div>

		<?php
			
	}
}else{

	echo '<div class="alert alert-danger text-center"><h2>';
		echo JText::_('To proceed in this module you must insert your API Key <a href="index.php?option=com_egoi">here</a>!');
	echo '</h2></div>';

} ?>