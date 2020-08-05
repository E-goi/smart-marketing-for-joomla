<?php
/**
 * @version     1.0.1
 * @package     com_egoi
 * @copyright   Copyright (C) 2020. Todos os direitos reservados.
 * @license     MIT LICENSE
 * @author      E-goi
 */
 
// No direct access
defined('_JEXEC' ) or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$result = ModEgoiHelper::processForm($_POST);

echo "<style>
    #load{
        background: url('//hulk-games.com/themes/happywheels_v2//resources/img/loading.gif') no-repeat;
        width: 100%;
        height: 40px;
        background-size: 50px;
    }</style>";

// Instantiate global document object
$doc = JFactory::getDocument();
$loadJquery = $params->get('loadJquery', 1);
// Load jQuery
if ($loadJquery == '1') {
	$doc->addScript('//code.jquery.com/jquery-latest.min.js');
}
$js = <<<JS
    (function ($) {
        $(document).on('click', 'input#egoi_submit', function () {
            $("#load").show();
            var form = $('#egoi_subscribe');
            var formData = $(form).serialize();

            $.ajax({
                type: 'POST',
                data: formData,
                success: function (response) {
                    $("#load").hide();
                    $('#egoi_result').html(response);
                    $(form).find("input[type=text], input[type=email]").val("");
                },
                error: function(response) {
                    $("#load").hide();
                    $('#egoi_result').html('');
                }
            });
            return false;
        });
    })(jQuery);
JS;

$doc->addScriptDeclaration($js);
$fields = ModEgoiHelper::getFields($params);
$form_type = $fields[0]->form_type;

$enc_content = $fields[0]->content;
$show = $fields[0]->show_title;
if($show){
	echo $title = $fields[0]->form_title;
}
$content = ModEgoiHelper::decode($enc_content);

//require(JModuleHelper::getLayoutPath('mod_egoi'));
