<?php
/**
 *
 * E-goi configuration view
 *
 * @package	E-goi
 * @subpackage Views
 * @author E-goi
 * @link https://www.e-goi.com
 * @copyright Copyright (c) 2020 E-GOI. All rights reserved.
 * @license		MIT License
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$data = $this->viewData;
$lists = $this->egoiLists;

if(isset($data['apikey']) && ($data['apikey'])){
?>
	
	<script type="text/javascript">	
		function addList() {
			if (document.forms["adminForm"].elements['newList'].value.length == 0) {
				alert("<?php echo JText::_('EGOI_DATA_LABEL_SELECT_LIST') ?>");
				document.forms["adminForm"].elements['newList'].focus();
				return false;
			}

			document.forms["adminForm"].elements['task'].value = 'addList';
			document.forms["adminForm"].submit();
		}
	</script>

	<form action="<?php echo JRoute::_('index.php?option=com_egoi&view=egoi&layout=edit_lists'); ?>" method="post" enctype="multipart/form-data" name="adminForm">
		<table id="listsTable" class="table table-hover">
			<thead>
				<tr>
					<th><?php echo JText::_('COM_EGOI_LIST_TABLE_HEADER_LIST_ID'); ?></th>
					<th><?php echo JText::_('COM_EGOI_LIST_TABLE_HEADER_TITLE'); ?></th>
					<th><?php echo JText::_('COM_EGOI_LIST_TABLE_HEADER_INTERNAL_TITLE'); ?></th>
					<th><?php echo JText::_('COM_EGOI_LIST_TABLE_HEADER_ACTIVE_SUBSCRIBERS'); ?></th>
					<th><?php echo JText::_('COM_EGOI_LIST_TABLE_HEADER_TOTAL_SUBSCRIBERS'); ?></th>
					<th><?php echo JText::_('COM_EGOI_LIST_TABLE_HEADER_LANG'); ?></th>
					<th><?php echo JText::_('COM_EGOI_LIST_TABLE_HEADER_CHANGE'); ?></th>
				</tr>
			</thead>
			<tbody><?php
				for ($counter = 0; $counter < count($lists); $counter++) {
					$list = $lists[$counter];?>
					<tr>
						<td class="number"><?php echo $list['listnum']; ?></td>
						<td><?php echo $list['title'];?></td>
						<td><?php echo $list['title_ref'];?></td>
						<td><?php echo $list['subs_activos'];?></td>
						<td><?php echo $list['subs_total'];?></td>
						<td><?php echo $list['idioma'];?></td>
						<td><a href="https://login.egoiapp.com/#/login/?action=login&from=<?php echo urlencode('/?action=lista_definicoes_principal&list='.$list['listnum']);?>" class='btn btn-primary' target="_blank" /><?php echo JText::_('COM_EGOI_LIST_TABLE_HEADER_CHANGE'); ?></a></td>
					</tr><?php
				} ?>
			</tbody>
		</table>

		<p>&nbsp;</p>
		<p><b><?php echo JText::_('COM_EGOI_DATA_LABEL_CREATE_LIST'); ?></b></p>
		<table class="table">
			<tr class="newList">
				<td class="newList"><?php echo JText::_('COM_EGOI_DATA_LABEL_NEW_LIST_NAME'); ?></td>
				<td class="newList"><input name="newList" type="text" size="100" value="" title="<?php echo JText::_('COM_EGOI_LIST_TABLE_TOOLTIP_NEW_LIST_NAME');?>" required /></td>
			</tr>
			<tr class="newList">
				<td class="newList"><?php echo JText::_('COM_EGOI_DATA_LABEL_NEW_LIST_LANGUAGE'); ?></td>
				<td class="newList">
					<select name="newLanguage" title="">
						<option value="br"><?php echo JText::_('COM_EGOI_DATA_LABEL_PT_BR'); ?></option>
						<option value="en"><?php echo JText::_('COM_EGOI_DATA_LABEL_EN_US'); ?></option>
						<option value="pt"><?php echo JText::_('COM_EGOI_DATA_LABEL_PT_PT'); ?></option>
						<option value="es"><?php echo JText::_('COM_EGOI_DATA_LABEL_ES_ES'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="newList">
				<td class="newList" colspan="2"><input name="add" type="button" class="btn btn-info" value="<?php echo JText::_('COM_EGOI_BUTTON_ADD'); ?>" onclick="addList()" /></td>
			</tr>
		</table>

		<input type="hidden" name="list_token" value="1" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
		<div class="clr"></div>

	</form><?php

}else{

	echo '<div class="alert alert-danger text-center"><h2>';
		echo JText::_('To proceed in this module you must insert your API Key <a href="index.php?option=com_egoi">here</a>!');
	echo '</h2></div>';

}

