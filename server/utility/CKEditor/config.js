/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	config.language = 'fi';
	
    config.filebrowserBrowseUrl = '/js/ckeditor/filemanager/index.html';
    config.filebrowserImageBrowseUrl = '/js/ckeditor/filemanager/index.html?type=Images';
    //config.filebrowserUploadUrl = '/js/ckeditor/filemanager2/connectors/php/filemanager.php';
    //config.filebrowserImageUploadUrl = '/js/ckeditor/filemanager2/connectors/php/filemanager.php?command=QuickUpload&type;=Images';
    
	config.toolbar = 'Lougis';
	config.toolbar_Lougis = [
            ['Cut','Copy','Paste','PasteText','PasteFromWord'],
            ['Link','Unlink','Anchor'],
            ['Image','Table','HorizontalRule'],
            ['Undo','Redo','-','Find','Replace','SelectAll','RemoveFormat'],
            '/',
            ['Format'],
            ['NumberedList','BulletedList','Outdent','Indent','Blockquote'],
            ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
            ['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','Source']
           // ,['Templates']
    ];    
    
	config.toolbar = 'LougisColumn';
	config.toolbar_LougisColumn = [
            ['PasteText','PasteFromWord'],
            ['Link','Image','HorizontalRule'],
            '/',
            ['Bold','Italic'],
            ['JustifyLeft','JustifyCenter','JustifyRight']
           // ,['Templates']
    ]; 
    
	config.toolbar = 'LougisCmsExtra';
	config.toolbar_LougisCmsExtra = [
            ['PasteText','PasteFromWord'],
            ['Bold','Italic'],
            ['NumberedList','BulletedList','Outdent','Indent'],
            ['Link']
    ];
	
    config.toolbar = 'LougisCompact';
	config.toolbar_LougisCompact = [
            ['Cut','Copy','Paste','PasteText','PasteFromWord'],
            ['Link','Unlink'],
            ['Undo','Redo','-','RemoveFormat'],
            '/',
            ['Format'],
			['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','Source']
    ];
	
	config.toolbar = 'YmparistoArviointi';
	config.toolbar_YmparistoArviointi = [
			['Undo','Redo','-','Cut','Copy','PasteText'],
            ['Link','Unlink','Anchor'],
            ['Image','Table','HorizontalRule'],
            ['Find','Replace','SelectAll','RemoveFormat'],
            '/',
            ['Format'],
            ['NumberedList','BulletedList','Outdent','Indent','Blockquote'],
            ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
            ['Bold','Italic','Underline','Strike','-','Subscript','Superscript']
    ];
};

