function textAreaAdjust(o) {
	o.style.overflow = 'hidden';
    o.style.height = '1px';
	o.style.height = (o.scrollHeight)+'px';
}

function replaceAll(text, busca, reemplaza) {
	while (text.toString().indexOf(busca) != -1)
		text = text.toString().replace(busca, reemplaza);
	return text;
}

function pixel_lenght(cadena) {
	var array_js = new Array();
	array_js['Á'] = 9;
	array_js['É'] = 8;
	array_js['Í'] = 5;
	array_js['Ó'] = 8;
	array_js['Ú'] = 9;
	array_js['Ü'] = 9;
	array_js[' '] = 6;
	array_js['!'] = 7;
	array_js['"'] = 7;
	array_js['#'] = 8;
	array_js['ü'] = 7;
	array_js['%'] = 9;
	array_js['&'] = 8;
	array_js['\''] = 4;
	array_js['('] = 7;
	array_js[')'] = 6;
	array_js['¡'] = 7;
	array_js['+'] = 7;
	array_js[','] = 4;
	array_js['-'] = 7;
	array_js['.'] = 3;
	array_js['/'] = 9;
	array_js['0'] = 8;
	array_js['1'] = 6;
	array_js['2'] = 8;
	array_js['3'] = 8;
	array_js['4'] = 8;
	array_js['5'] = 8;
	array_js['6'] = 8;
	array_js['7'] = 8;
	array_js['8'] = 8;
	array_js['9'] = 8;
	array_js[':'] = 4;
	array_js[';'] = 4;
	array_js['<'] = 9;
	array_js['='] = 7;
	array_js['>'] = 9;
	array_js['?'] = 7;
	array_js['@'] = 8;
	array_js['A'] = 9;
	array_js['B'] = 8;
	array_js['C'] = 8;
	array_js['D'] = 8;
	array_js['E'] = 8;
	array_js['F'] = 8;
	array_js['G'] = 8;
	array_js['H'] = 9;
	array_js['I'] = 5;
	array_js['J'] = 7;
	array_js['K'] = 9;
	array_js['L'] = 8;
	array_js['M'] = 9;
	array_js['N'] = 8;
	array_js['Ñ'] = 8;
	array_js['O'] = 8;
	array_js['P'] = 8;
	array_js['Q'] = 8;
	array_js['R'] = 9;
	array_js['S'] = 8;
	array_js['T'] = 8;
	array_js['U'] = 9;
	array_js['V'] = 8;
	array_js['W'] = 9;
	array_js['X'] = 8;
	array_js['Y'] = 8;
	array_js['Z'] = 8;
	array_js['['] = 6;
	array_js['¿'] = 7;
	array_js[']'] = 7;
	array_js['ñ'] = 7;
	array_js['_'] = 8;
	array_js['á'] = 7;
	array_js['a'] = 7;
	array_js['b'] = 6;
	array_js['c'] = 6;
	array_js['d'] = 7;
	array_js['e'] = 6;
	array_js['f'] = 7;
	array_js['g'] = 7;
	array_js['h'] = 6;
	array_js['i'] = 4;
	array_js['j'] = 6;
	array_js['k'] = 6;
	array_js['l'] = 4;
	array_js['m'] = 8;
	array_js['n'] = 7;
	array_js['ñ'] = 7;
	array_js['o'] = 6;
	array_js['p'] = 6;
	array_js['q'] = 7;
	array_js['r'] = 7;
	array_js['s'] = 6;
	array_js['t'] = 5;
	array_js['u'] = 7;
	array_js['v'] = 6;
	array_js['w'] = 8;
	array_js['x'] = 7;
	array_js['y'] = 6;
	array_js['z'] = 6;
	array_js['é'] = 7;
	array_js['í'] = 4;
	array_js['ó'] = 7;
	array_js['ú'] = 7;
	array_js['©'] = 9;
	array_js['<1>'] = 8;
	array_js['<3>'] = 8;
	array_js['<4>'] = 8;

	var long = 0;
	var caracter = '';
	for ( var i = 0; i < cadena.length; i++) {
		caracter = cadena[i];
		if(caracter in array_js)
		{
			long += array_js[caracter];	
		}
	}

	return long;
}