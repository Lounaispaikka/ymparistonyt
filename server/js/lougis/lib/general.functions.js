//Make console log work safer.
if ( typeof window.console == 'undefined' ) {
	console = {};
	old_console_log = function() {};
	console.log = function() {
		if ( typeof console != 'undefined' && typeof console.log != 'undefined' ) old_console_log.apply(this, arguments);
	}
}