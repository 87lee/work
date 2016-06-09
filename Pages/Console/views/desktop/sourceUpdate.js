//@ sourceURL=desktop.sourceUpdate.js
var myData = {};
$(function () {
	reloadUpdate('/desktop/publishDesktopSourceLists');
});

commonUpdate(
	'/desktop/publishDesktopSource',
	'/desktop/publishDesktopSourceLists',
	'/desktop/deletepublishDesktopSource',
	'/desktop/desktopSourceLists'
);