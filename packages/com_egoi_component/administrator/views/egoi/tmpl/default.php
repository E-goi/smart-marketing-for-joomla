<?php
/**
 * @version     1.0.1
 * @package     com_egoi
 * @copyright   Copyright (C) 2020. Todos os direitos reservados.
 * @license     MIT LICENSE
 * @author      E-goi
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
$data = $this->viewData;
?>

	<style> 
		label {
			display: inline !important;
		}
	</style>

	<script type="text/javascript">
		jQuery(document).ready(function ($){
			$('.btn-toolbar').append('<a href="https://login.egoiapp.com/#/login/?action=login&from=<?php echo urlencode('/?action=ui#/accounts/info/account/overall');?>" target="_blank" class="btn btn-primary" id="egoi_edit_info"><?php echo JText::_('EGOI_CHANGE_DATA');?></a><span class="egoi-redirect"> <?php echo JText::_('EGOI_CHANGE_DATA_INFO');?></span>');
		}(jQuery));

		function saveApiKeyAndReload() {
			if (document.forms["adminForm"].elements['apikey_disp'].value != "<?php echo $data['apikey'];?>") {
				document.forms["adminForm"].elements['apikey'].value = document.forms["adminForm"].elements['apikey_disp'].value;

				document.getElementById('input_key').style.display = 'none';
				document.getElementById('content_key').style.display = 'inline-block';
				document.forms["adminForm"].elements['task'].value = 'save';
				document.forms["adminForm"].submit();
			}
		}

		function displayInput(){
			document.getElementById('btn_key').style.display = 'none';
			document.getElementById('content_key').style.display = 'none';
			document.getElementById('input_key').style.display = 'inline-block';

			document.getElementById('ok').style.display = 'inline-block';
		}		
	</script>
	<style type="text/css">
		.egoi-redirect{
			font-style: italic;
	   		font-size: 14px;
	    	vertical-align: -webkit-baseline-middle;
	    	vertical-align: -moz-baseline-middle;
	    }
    	.table thead>tr>th{
    		border: none;
		    border-bottom: solid 1px #a0d0eb;
			font-weight: bold;
		}
	</style>

    <div>
        <fieldset class="adminform">	
			<table class="table table-hover">
			<?php
			if($data['apikey']!=''){

				$result = $this->egoiAccount;
				?>
				<form action="<?php echo JRoute::_('index.php?option=com_egoi&view=egoi'); ?>" method="post" name="egoi_key" id="egoi-form" class="form-validate">
					<input type="hidden" name="edit_sett" value="1" />
					<input type="hidden" name="api" value="1" />
					<thead>
						<tr>
							<th>
								<label for="egoi_wp_apikey"><b><?php echo JText::_('EGOI_ACCOUNT_APIKEY');?></b></label>
							</th>
							<th>	
								<span id="content_key"><?php echo $data['apikey'];?></span>
								<input id="input_key" type="text" style="width:290px;display:none;" name="apikey" size="50" value="<?php echo $data['apikey'];?>" />&nbsp;
								
								<button type="button" onclick="displayInput();" id="btn_key" class="btn btn-info">
									<?php echo JText::_('COM_EGOI_TOOLTIP_CHANGE_API_KEY'); ?>
								</button>
								<button id="ok" class="btn btn-info" style="display:none;" onclick="document.egoi_key.submit();">
									<?php echo JText::_('EGOI_SAVE_API_KEY');?>
								</button>
							</th>
						</tr>
					</thead>
				</form>
				<tbody>
					<tr>
						<th>
							<label for="egoi_jm_clientid"><?php echo JText::_('EGOI_ACCOUNT_CLIENTE_ID');?></label>
						</th>
						<td>
							<?php echo $result['general_info']['client_id']; ?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="egoi_jm_companyname"><?php echo JText::_('EGOI_ACCOUNT_COMPANY_NAME');?></label>
						</th>
						<td>
							<?php echo $result['general_info']['name']; ?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="egoi_jm_companylegalname"><?php echo JText::_('EGOI_ACCOUNT_COMPANY_LEGAL');?></label>
						</th>
						<td>
                            <?php echo $result['billing_info']['company_legal_name']; ?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="egoi_jm_companytype"><?php echo JText::_('EGOI_ACCOUNT_COMPANY_TYPE');?></label>
						</th>
						<td>
                            <?php echo $result['billing_info']['type']; ?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="egoi_jm_country"><?php echo JText::_('EGOI_ACCOUNT_COUNTRY');?></label>
						</th>
						<td>
							<?php echo $result['billing_info']['country']['country_code']; ?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="egoi_jm_state"><?php echo JText::_('EGOI_ACCOUNT_STATE');?></label>
						</th>
						<td>
                            <?php echo $result['billing_info']['state']; ?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="egoi_jm_city"><?php echo JText::_('EGOI_ACCOUNT_CITY');?></label>
						</th>
						<td>
                            <?php echo $result['billing_info']['city']; ?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="egoi_jm_zip"><?php echo JText::_('EGOI_ACCOUNT_ZIP');?></label>
						</th>
						<td>
							<?php echo $result['billing_info']['address1']; ?> <?php echo $result['billing_info']['address2']; ?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="egoi_jm_address"><?php echo JText::_('EGOI_ACCOUNT_ADDRESS');?></label>
						</th>
						<td>
							<?php echo $result['billing_info']['zip_code']; ?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="egoi_jm_website">Website</label>
						</th>
						<td>
                            <?php echo $result['general_info']['website']; ?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="egoi_jm_credits"><?php echo JText::_('EGOI_ACCOUNT_CREDITS');?></label>
						</th>
						<td>
							<?php echo $result['balance_info']['balance']; ?> <?php echo $result['balance_info']['currency']; ?>
						</td>
					</tr>
				</tbody><?php 

			} else { ?>

				<form action="<?php echo JRoute::_('index.php?option=com_egoi&view=egoi');?>" method="post" name="adminForm" id="egoi-form" class="form-validate">
					<tr>
						<td colspan="2"><b><?php echo JText::_('COM_EGOI_SECTION_LABEL_API_KEY');?></b></td>
					</tr>
					<tr>
						<td width="40%"><?php echo JText::_('COM_EGOI_DATA_LABEL_API_KEY_TOOLTIP');?></td>
						<td>
							<input type="text" name="apikey" size="50" style="width: 40%" value="<?php echo $data['apikey'];?>" required />&nbsp;
							<input type="submit" name="submit_egoi" value="Save Changes" class="btn btn-success" style="margin-bottom: 10px;">
							<input type="hidden" name="edit_sett" value="1" />
							<input type="hidden" name="api" value="1" />
						</td>
					</tr><?php echo JHtml::_('form.token');?>

				</form><?php

			}?>
			</table>

        </fieldset>
    </div>

    
    <div class="clr"></div>
