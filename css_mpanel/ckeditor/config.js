/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	config.language = 'ru';

	config.toolbar = 'Bagira';

	config.filebrowserBrowseUrl = '/css_mpanel/elfinder/elfinder.html';
	config.filebrowserWindowHeight = 520;

	config.extraPlugins = 'mediaembed';

	config.coreStyles_bold = { element : 'b', overrides : 'strong' };
	config.coreStyles_italic = { element : 'i', overrides : 'em' };

	config.stylesSet = [
		{ name: 'Красный текст', element: 'span', attributes: { 'class': 'red' } }
	];
	
	config.pasteFromWordRemoveFontStyles = true;
	config.forceEnterMode = true;

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbar = [
		{ name: 'document', items : [ 'Source','ShowBlocks','Print'] },
		{ name: 'clipboard', items : [ 'Cut','Copy','PasteText','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace','-','SelectAll' ] },
		{ name: 'insert', items : [ '-','Image','MediaEmbed','Iframe','Flash','Table'/*,'Smiley'*/,'SpecialChar'] },
		{ name: 'insert', items : ['-','About'] },
		'/',
		{ name: 'styles', items : [ 'FontSize','Format','Styles' ] },    /* ,'Font' */
		{ name: 'paragraph', items : [ '-','Bold','Italic','Underline','Strike'] },
		{ name: 'links', items : [ 'Link','Unlink' ] },
		{ name: 'paragraph', items : [ '-','JustifyLeft','JustifyCenter','JustifyRight' /*,'JustifyBlock'*/ ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] },
		{ name: 'basicstyles', items : [ '-','NumberedList','BulletedList', '-','Subscript','Superscript', '-','Blockquote'] },
		{ name: 'basicstyles', items : [ '-','RemoveFormat'] }
	];

	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	//config.removeButtons = 'Underline,Subscript,Superscript';
};
