<?php
/**
 * @version     1.0.1
 * @package     com_egoi
 * @copyright   Copyright (C) 2020. Todos os direitos reservados.
 * @license     MIT LICENSE
 * @author      E-goi
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldSubmit extends JFormField {

    protected $type = 'submit';
    protected $value;
    protected $for;

    public function getInput() {
        
        $this->value = $this->getAttribute('value');
        
        return '<button id="' . $this->id . '"' 
                . ' name="submit_' . $this->for . '"'
                . ' value="'. $this->value . '"' 
                . ' title="' . JText::_('JSEARCH_FILTER_SUBMIT') . '"'
                . ' class="btn" style="margin-top: -10px;">' 
                . JText::_('JSEARCH_FILTER_SUBMIT') 
                . ' </button>';
    }

}
