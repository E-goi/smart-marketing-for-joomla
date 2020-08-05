<?php 
$options = array(
	'active' => 'tab1_j36_id'
); 
echo JHtml::_('bootstrap.startTabSet', 'ID-Tabs-J36-Group', $options);?> 


	<?php echo JHtml::_('bootstrap.addTab', 'ID-Tabs-J36-Group', 'tab1_j36_id', JText::_('Form Content Settings')); ?> 
	
	<style type="text/css">
		#load{
			background: url('components/com_egoi/assets/images/load.gif') no-repeat;
	   		display: inline-block;
	   		width: 20px;
	   		height: 20px;
	   		background-size: 20px;
	   		margin-left: 20px;
		}
	</style>
	<table class="table">
		<tr valign="top">
			<th style="border-top: none;"><?php echo JText::_('Enable Form'); ?></th>
			<td style="border-top: none;">
				<fieldset id="egoi_enable" class="radio btn-group">
	                <input type="radio" id="egoi_enable0" value="1" name="enable" <?php if ($data->enable) echo 'checked'; ?>>
	                <label for="egoi_enable0" class="btn <?php if ($data->enable) echo 'btn-success';?>"><?php echo JText::_('EGOI_YES'); ?></label>
	                <input type="radio" id="egoi_enable1" value="0" name="enable" <?php if (!$data->enable) echo 'checked'; ?>>
	                <label for="egoi_enable1" class="btn <?php if (!$data->enable) echo 'btn-danger';?>"><?php echo JText::_('EGOI_NO'); ?></label>
				</fieldset>
				<p class="help" style="padding: 10px;">
					<?php echo JText::_('Select "yes" to enable this form.'); ?>
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th><?php echo JText::_('Show Title'); ?></th>
			<td>
				<fieldset id="egoi_enable" class="radio btn-group">
	                <input type="radio" id="egoi_show_title0" value="1" name="show_title" <?php if ($data->show_title) echo 'checked'; ?>>
	                <label for="egoi_show_title0" class="btn <?php if ($data->show_title) echo 'btn-success';?>"><?php echo JText::_('EGOI_YES'); ?></label>
	                <input type="radio" id="egoi_show_title1" value="0" name="show_title" <?php if (!$data->show_title) echo 'checked'; ?>>
	                <label for="egoi_show_title1" class="btn <?php if (!$data->show_title) echo 'btn-danger';?>"><?php echo JText::_('EGOI_NO'); ?></label>
				</fieldset>
				<p class="help" style="padding: 10px;">
					<?php echo JText::_('Select "yes" to show the title of this form in Front Page.'); ?>
				</p>
			</td>
		</tr><?php
		
		if (($_GET['type'] == 'popup') || ($_GET['type'] == 'html')){

			$content = html_entity_decode(base64_decode($data->content));
			if($data->form_type == $_GET['type']) {?>

				<tr valign="top">
					<th><?php echo JText::_('EGOI_FORM_TEXT');?></th>
					<td>
						<textarea name="content" rows="20" style="width: 80%;"><?php echo $content;?></textarea>
					</td>
				</tr><?php
			}else{ ?>

				<tr valign="top">
					<th><?php echo JText::_('EGOI_FORM_TEXT');?></th>
					<td>
						<textarea name="content" rows="20" style="width: 80%;"></textarea>
					</td>
				</tr><?php
			}

		}else{ ?>

			<tr valign="top">
				<th><?php echo JText::_('List to Subscribe');?></th>
				<?php
				if(empty($lists)) { ?>
					<td colspan="2" style="padding-left: 10px;"><?php echo JText::_('Lists not found, <a href="%s">are you connected to egoi</a>?');?></td><?php
				}else{ ?>
					<td style="padding-left: 15px;">
						<select name="list"><?php
							$index = 1;
							foreach($lists as $list) { ?>
								<option value="<?php echo ($list['listnum']);?>" <?php if($list['listnum'] == $data->list) echo 'selected';?>><?php echo ($list['title']);?></option><?php
								$index++;
							} ?>
						</select>
						<p class="help"><?php echo JText::_('Select the list to which people who submit this form should be subscribed.');?></p>
					</td><?php 
				} ?>

			</tr>

			<tr valign="top">
				<th><?php echo JText::_('E-goi Form to Subscribe'); ?></th>
				<?php
				if($data->list) { ?>
					<td style="padding-left: 15px;">
						<select name="content" id="formid_egoi">
							<option value=""><?php echo JText::_('Select your form');?></option><?php
							foreach ($forms as $value) {?>
								<option value="<?php echo $value['id'].' - '.$value['url'];?>" <?php if(($value['id'].' - '.$value['url']) == base64_decode($data->content)) echo 'selected';?>><?php echo $value['title'];?></option><?php
							} ?>
						</select><div id="load" style="display:none;">&nbsp;</div>
					</td><?php
				}else{ ?>
					<td colspan="2"><?php echo JText::_('First you need to select and save your list then you can get your form from E-goi');?></td><?php
				} ?>
			</tr><?php
		} ?>

	</table>
	<?php echo JHtml::_('bootstrap.endTab');?>
	

	<?php echo JHtml::_('bootstrap.addTab', 'ID-Tabs-J36-Group', 'tab2_j36_id', JText::_('Form Area Settings'));?>
		
		<table class="table"><?php
			if($_GET['type'] == 'iframe'){ ?> 
				<tr valign="top">
					<th><?php echo JText::_('Box Witdh'); ?></th>
					<td>
						<input type="text" name="style_w" value="<?php echo $data->style_w;?>" class="form-control" placeholder="Ex: 600">
						<p class="help">
							<?php echo JText::_('Witdh of the Iframe Box'); ?>
						</p>
					</td>
				</tr>
				<tr valign="top">
					<th><?php echo JText::_('Box Height'); ?></th>
					<td>
						<input type="text" name="style_h" value="<?php echo $data->style_h;?>" class="form-control" placeholder="Ex: 400">
						<p class="help">
							<?php echo JText::_('Height of the Iframe Box'); ?>
						</p>
					</td>
				</tr><?php
			} ?>
			
			<tr valign="top">
				<th><?php echo JText::_('Form Display Area');?></th>
				<td>
					<select name="area"><?php
						if($data->area == 'widget'){ ?>
							<option value="widget" selected>Widget/Module</option><?php
						}else{
							if(!$count_area){ ?>
								<option value="widget">Widget/Module</option><?php
							}
						} ?>
						<option value="header" <?php if($data->area == 'header') echo 'selected';?>>On Site Body (after title)</option>
						<option value="body" <?php if($data->area == 'body') echo 'selected';?>>On Site Body (after content page)</option>
					</select>
					<p class="help"><?php echo JText::_('Select the area when this form should be displayed.');?></p>
				</td>

			</tr>
		</table>
		

	<?php echo JHtml::_('bootstrap.endTab');?> 

<?php echo JHtml::_('bootstrap.endTabSet');?>