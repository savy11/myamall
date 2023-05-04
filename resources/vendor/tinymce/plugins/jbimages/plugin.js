tinymce.PluginManager.add('jbimages', function(editor, url) {
	
	function jbBox() {
		editor.windowManager.open({
			title: 'Upload Image',
			file : url + '/dialog-v4.htm',
			width : 350,
			height: 135,
			buttons: [{
				text: 'Upload',
				classes:'widget btn primary first abs-layout-item',
				disabled : true,
				onclick: 'close'
			},
			{
				text: 'Close',
				onclick: 'close'
			}]
		});
	}
	
	// Add a button that opens a window
	editor.addButton('jbimages', {
		tooltip: 'Upload Image',
		icon : 'image',
		text: 'Upload',
		onclick: jbBox
	});

	// Adds a menu item to the tools menu
	editor.addMenuItem('jbimages', {
		text: 'Upload Image',
		icon : 'image',
		context: 'insert',
		onclick: jbBox
	});
});