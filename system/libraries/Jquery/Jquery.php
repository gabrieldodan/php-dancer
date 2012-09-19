<?php

/**
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

/**
 * Class description  
 */
class Jquery {
    
    static function urlMainScript() {
        return Locator::urlThisLib() . "/jquery.js";
    }
    
    static function urlPlugin($plugin) {
		$plugin	= preg_replace('/^(.*)\.js$/i', '${1}', $plugin) . ".js";
        return Locator::urlThisLib() . "/plugins/{$plugin}";
    }
    
    static function urlUiComponent($ui, $withDeps = TRUE, $withoutMainScript = FALSE) {
        $ui	= preg_replace('/^(.*)\.js$/i', '${1}', $ui) . ".js";
		
		if ( !$withDeps ) {
            return Locator::urlThisLib() . "/ui/jquery.{$ui}";
        }
        
    	static $deps = array(
   			'ui.core.js'			=>	array(),
   			'ui.widget.js'			=>	array(),
   			'ui.mouse.js'			=>	array('ui.core.js', 'ui.widget.js'),
   			'ui.position.js'		=>	array(),
   			'ui.draggable.js'		=>	array('ui.core.js', 'ui.widget.js', 'ui.mouse.js'),
   			'ui.droppable.js'		=>	array('ui.core.js', 'ui.widget.js', 'ui.mouse.js', 'ui.draggable.js'),
   			'ui.resizable.js'		=>	array('ui.core.js', 'ui.widget.js', 'ui.mouse.js'),
   			'ui.selectable.js'		=>	array('ui.core.js', 'ui.widget.js', 'ui.mouse.js'),
   			'ui.sortable.js'		=>	array('ui.core.js', 'ui.widget.js', 'ui.mouse.js'),
   			'ui.accordion.js'		=>	array('ui.core.js', 'ui.widget.js'),
   			'ui.autocomplete.js'	=>	array('ui.core.js', 'ui.widget.js', 'ui.position.js'),
   			'ui.button.js'			=>	array('ui.core.js', 'ui.widget.js'),
   			'ui.dialog.js'			=>	array('ui.core.js', 'ui.widget.js', 'ui.position.js'),
   			'ui.slider.js'			=> 	array('ui.core.js', 'ui.widget.js', 'ui.mouse.js'),
   			'ui.tabs.js'			=> 	array('ui.core.js', 'ui.widget.js'),
   			'ui.datepicker.js'		=>	array('ui.core.js'),
   			'ui.progressbar.js'		=>	array('ui.core.js', 'ui.widget.js'),
   			'effects.core.js'		=>	array(),
   			'effects.blind.js'		=>	array('effects.core.js'),
   			'effects.bounce.js'		=>	array('effects.core.js'),
   			'effects.clip.js'		=>	array('effects.core.js'),
   			'effects.drop.js'		=>	array('effects.core.js'),
   			'effects.explode.js'	=>	array('effects.core.js'),
   			'effects.fade.js'		=>	array('effects.core.js'),
   			'effects.fold.js'		=>	array('effects.core.js'),
   			'effects.highlight.js'	=>	array('effects.core.js'),
   			'effects.pulsate.js'	=>	array('effects.core.js'),
   			'effects.scale.js'		=>	array('effects.core.js'),
   			'effects.shake.js'		=>	array('effects.core.js'),
   			'effects.slide.js'		=>	array('effects.core.js'),
   			'effects.transfer.js'	=>	array('effects.core.js')
    	);
    	
        if ( isset($deps[$ui]) ) {
            $result	= array();
			
			if ( $withoutMainScript == FALSE ) {
				// add main jquery file
				$result[]	= Jquery::urlMainScript();
			}
    		foreach ($deps[$ui] AS $dep) {
                $result[] = Locator::urlThisLib() . "/ui/jquery.{$dep}";
    		}
            $result[] = Locator::urlThisLib() . "/ui/jquery.{$ui}";
            return $result;
    	}
        return NULL;
    }
    
    static function urlUiTheme($theme) {
        return Locator::urlThisLib() . "/ui/themes/{$theme}/theme.css";
    }
    
    static function urlDatepickerI18n($localeCode) {
        return Locator::urlThisLib() . "/ui/i18n/jquery.ui.datepicker-{$localeCode}.js";
    }
}
?>
