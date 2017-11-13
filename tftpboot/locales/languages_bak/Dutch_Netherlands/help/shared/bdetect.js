var d, dom, ie, ie4, ie5x, moz, mac, win, lin, old, ie5mac, ie5xwin, op;

d = document;
n = navigator;
na = n.appVersion;
nua = n.userAgent;
win = ( na.indexOf( 'Win' ) != -1 );
mac = ( na.indexOf( 'Mac' ) != -1 );
lin = ( nua.indexOf( 'Linux' ) != -1 );

if ( !d.layers ){
	dom = ( d.getElementById );
	op = ( nua.indexOf( 'Opera' ) != -1 );
	konq = ( nua.indexOf( 'Konqueror' ) != -1 );
	saf = ( nua.indexOf( 'Safari' ) != -1 );
	moz = ( nua.indexOf( 'Gecko' ) != -1 && !saf && !konq);
	ie = ( d.all && !op );
	ie4 = ( ie && !dom );

	/*
	ie5x tests only for functionality. ( dom||ie5x ) would be default settings. 
	Opera will register true in this test if set to identify as IE 5
	*/

	ie5x = ( d.all && dom );
	ie5mac = ( mac && ie5x );
	ie5xwin = ( win && ie5x );
}
