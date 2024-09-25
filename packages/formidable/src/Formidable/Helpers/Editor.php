<?php    
namespace Concrete\Package\Formidable\Src\Formidable\Helpers;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Editor\Plugin;

class Editor {

	public static function getSimpleEditor()
	{
		$editor = Application::getFacadeApplication()->make('editor');
					
		// Set editor
		$script = "
		(function () {
			CKEDITOR.plugins.add('formidable', {
				init: function (editor) {
					editor.addCommand( 'formidableData', {
						exec: function( editor ) { 
							jQuery.fn.dialog.open({
								element: 'div[data-formidable-data]',
								modal: true,
								width: 750,
								title: '".t('Select "Formidable Data" - Tag')."',
								height: 600,
								onOpen: function() {
									$('div[data-formidable-data]').addClass('only-form');
									$('div[data-formidable-data] [data-insert]').off('click').on('click', function() {
										var code = $(this).data('insert');										
										editor.insertHtml(code);
										jQuery.fn.dialog.closeTop();
									});
								}
							});                         
						}
					});
					editor.ui.addButton('".t('Formidable Data')."', {
						label: '".t('Insert Formidable Data')."',
						command: 'formidableData',
						icon: '".REL_DIR_PACKAGES."/formidable/img/plus.png'
					});               
				}
			});
			CKEDITOR.config.extraPlugins = 'formidable';  
		})();";

		$al = AssetList::getInstance();	
		$al->register('javascript-inline', 'formidable/editor/plugin', $script);
		$al->registerGroup('formidable/editor', [['javascript-inline', 'formidable/editor/plugin']]);

		$plugin = new Plugin();
		$plugin->setKey('formidable');
		$plugin->setName('Formidable');
		$plugin->requireAsset('formidable/editor');
		
		$editor->getPluginManager()->register($plugin);		    
		$editor->getPluginManager()->select('formidable');

		return $editor;
	}

	public static function getFullEditor()
	{
		$editor = Application::getFacadeApplication()->make('editor');
					
		// Set editor
		$script = "
		(function () {
			CKEDITOR.plugins.add('formidable', {
				init: function (editor) {
					editor.addCommand( 'formidableData', {
						exec: function( editor ) { 
							jQuery.fn.dialog.open({
								element: 'div[data-formidable-data]',
								modal: true,
								width: 750,
								title: '".t('Select "Formidable Data" - Tag')."',
								height: 600,
								onOpen: function() {
									$('div[data-formidable-data] [data-insert]').off('click').on('click', function() {
										var code = $(this).data('insert');										
										editor.insertHtml(code);
										jQuery.fn.dialog.closeTop();
									});
								}
							});                         
						}
					});
					editor.ui.addButton('".t('Formidable Data')."', {
						label: '".t('Insert Formidable Data')."',
						command: 'formidableData',
						icon: '".REL_DIR_PACKAGES."/formidable/img/plus.png'
					});               
				}
			});
			CKEDITOR.config.extraPlugins = 'formidable';  
		})();";

		$al = AssetList::getInstance();	
		$al->register('javascript-inline', 'formidable/editor/plugin', $script);
		$al->registerGroup('formidable/editor', [['javascript-inline', 'formidable/editor/plugin']]);

		$plugin = new Plugin();
		$plugin->setKey('formidable');
		$plugin->setName('Formidable');
		$plugin->requireAsset('formidable/editor');
		
		$editor->getPluginManager()->register($plugin);		    
		$editor->getPluginManager()->select('formidable');

		return $editor;
	}

	public static function getTemplateEditor()
	{
		$editor = Application::getFacadeApplication()->make('editor');
					
		// Set editor
		$script = "
		(function () {
			CKEDITOR.plugins.add('formidable', {
				init: function (editor) {
					editor.addCommand( 'formidableData', {
						exec: function( editor ) { 
							editor.insertHtml('{%formidable_data%}');                        
						}
					});
					editor.ui.addButton('".t('Formidable Data')."', {
						label: '".t('Insert Formidable Form-tag')."',
						command: 'formidableData',
						icon: '".REL_DIR_PACKAGES."/formidable/img/plus.png'
					});               
				}
			});
			CKEDITOR.config.extraPlugins = 'formidable';  
		})();";

		$al = AssetList::getInstance();	
		$al->register('javascript-inline', 'formidable/editor/plugin', $script);
		$al->registerGroup('formidable/editor', [['javascript-inline', 'formidable/editor/plugin']]);

		$plugin = new Plugin();
		$plugin->setKey('formidable');
		$plugin->setName('Formidable');
		$plugin->requireAsset('formidable/editor');
		
		$editor->getPluginManager()->register($plugin);		    
		$editor->getPluginManager()->select('formidable');

		return $editor;
	}	
	
}