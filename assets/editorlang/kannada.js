/*  Gopi's Unicode Converters Version 2.0
    Copyright (C) 2007  Gopalakrishnan (Gopi) http://www.higopi.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    Further to the terms mentioned you should leave this copyright notice
    intact, stating me as the original author.
*/
var lang = "kannada";
var chnbin = "\u0CCD";
var ugar = "\u0CC1";
var uugar = "\u0CC2";
myimg.src = webhome+"/images/ucedit/"+lang+".png";

var ka = new Array();
var katw = new Array();
//Typewritter
katw['\\~'] = "\u0C92";
katw['\\`'] = "\u0CCA";
katw['\\#'] = "\u0CCD\u0CB0";
katw['\\$'] = "\u0CB0\u0CCD";
katw['\\%'] = "\u0C9C\u0CCD\u0C9E";
katw['\\^'] = "\u0CA4\u0CCD\u0CB0";
katw['\\&'] = "\u0C95\u0CCD\u0CB7";
katw['\\*'] = "\u0CB6\u0CCD\u0CB0";
katw['_'] = "\u0C83";
katw['\\+'] = "\u0C8B";
katw['\\='] = "\u0CD3";
katw['q'] = "\u0CCC";
katw['w'] = "\u0CC8";
katw['e'] = "\u0CBE";
katw['r'] = "\u0CC0";
katw['t'] = "\u0CC2";
katw['y'] = "\u0CAC";
katw['u'] = "\u0CB9";
katw['i'] = "\u0C97";
katw['o'] = "\u0CA6";
katw['p'] = "\u0C9C";
katw['\\['] = "\u0CA1";

katw['Q'] = "\u0C94";
katw['W'] = "\u0C90";
katw['E'] = "\u0C86";
katw['R'] = "\u0C88";
katw['T'] = "\u0C8A";
katw['Y'] = "\u0CAD";
katw['U'] = "\u0C99";
katw['I'] = "\u0C98";
katw['O'] = "\u0CA7";
katw['P'] = "\u0C9D";
katw['\\{'] = "\u0CA2";
katw['\\}'] = "\u0C9E";

katw['a'] = "\u0CCB";
katw['s'] = "\u0CC7";
katw['d'] = "\u0CCD";
katw['f'] = "\u0CBF";
katw['g'] = "\u0CC1";
katw['h'] = "\u0CAA";
katw['j'] = "\u0CB0";
katw['k'] = "\u0C95";
katw['l'] = "\u0CA4";
katw[';'] = "\u0C9A";
katw['\\\''] = "\u0C9F";

katw['A'] = "\u0C93";
katw['S'] = "\u0C8F";
katw['D'] = "\u0C85";
katw['F'] = "\u0C87";
katw['G'] = "\u0C89";
katw['H'] = "\u0CAB";
katw['J'] = "\u0CB1";
katw['K'] = "\u0C96";
katw['L'] = "\u0CA5";
katw[':'] = "\u0C9B";
katw['"'] = "\u0CA0";

katw['z'] = "\u0CC6";
katw['x'] = "\u0C82";
katw['c'] = "\u0CAE";
katw['v'] = "\u0CA8";
katw['b'] = "\u0CB5";
katw['n'] = "\u0CB2";
katw['m'] = "\u0CB8";
katw['/'] = "\u0CAF";
katw['Z'] = "\u0C8E";
katw['X'] = "";
katw['C'] = "\u0CA3";
katw['V'] = "";
katw['B'] = "";
katw['N'] = "\u0CB3";
katw['M'] = "\u0CB6";
katw['<'] = "\u0CB7";
katw['>'] = "\u007C";
//Phonetic
ka['B'] = "b";
ka['C'] = "c";
ka['F'] = "ph";
ka['f'] = "ph";
ka['G'] = "g";
ka['J'] = "j";
ka['K'] = "k";
ka['M'] = "m";
ka['P'] = "p";
ka['Q'] = "q";
ka['V'] = "v";
ka['W'] = "v";
ka['w'] = "v";
ka['X'] = "x";
ka['Y'] = "y";
ka['Z'] = "j";
ka['z'] = "j";
//Cons
ka['k'] = "\u0C95\u0CCD";
ka['c'] = "\u0C9A\u0CCD";
ka['T'] = "\u0C9F\u0CCD";
ka['D'] = "\u0CA1\u0CCD";
ka['N'] = "\u0CA3\u0CCD";
ka['t'] = "\u0CA4\u0CCD";
ka['d'] = "\u0CA6\u0CCD";
ka['n'] = "\u0CA8\u0CCD";
ka['p'] = "\u0CAA\u0CCD";
ka['b'] = "\u0CAC\u0CCD";


ka['y'] = "\u0CAF\u0CCD";
ka['R'] = "\u0CB1\u0CCD";
ka['L'] = "\u0CB3\u0CCD";
ka['v'] = "\u0CB5\u0CCD";
ka['s'] = "\u0CB8\u0CCD";
ka['S'] = "\u0CB6\u0CCD";
ka['H'] = "\u0CB9\u0CCD";
ka['x'] = "\u0C95\u0CCD\u0CB6\u0CCD";

ka['\u200Dm'] = "\u0C82";
ka[':h'] = "\u0C83";
ka['m'] = "\u0CAE\u0CCD";

ka['\u0C95\u0CCDh'] = "\u0C96\u0CCD";
ka['\u0C97\u0CCDh'] = "\u0C98\u0CCD";
ka['\u0CA8\u0CCDg'] = "\u0C99\u0CCD";
ka['\u0C9A\u0CCDh'] = "\u0C9B\u0CCD";
ka['\u0C9C\u0CCDh'] = "\u0C9D\u0CCD";
ka['\u0CA8\u0CCDj'] = "\u0C9E\u0CCD";
ka['\u0C9F\u0CCDh'] = "\u0CA0\u0CCD";
ka['\u0CA1\u0CCDh'] = "\u0CA2\u0CCD";
ka['\u0CA4\u0CCDh'] = "\u0CA5\u0CCD";
ka['\u0CA6\u0CCDh'] = "\u0CA7\u0CCD";
ka['\u0CAA\u0CCDh'] = "\u0CAB\u0CCD";
ka['\u0CAC\u0CCDh'] = "\u0CAD\u0CCD";
ka['\u0CB8\u0CCDh'] = "\u0CB7\u0CCD";
ka['\u0CB1\u0CCDr'] = "\u0C8B";
ka['\u0CB3\u0CCDl'] = "\u0C8C";

ka['\u0CCD\u0C8B'] = "\u0CC3";
ka['h'] = "\u0CB9\u0CCD";
ka['j'] = "\u0C9C\u0CCD";
ka['g'] = "\u0C97\u0CCD";
ka['r'] = "\u0CB0\u0CCD";
ka['l'] = "\u0CB2\u0CCD";
//VowSml
ka['\u0CCDa'] = "\u200C";
ka['\u0CCDi'] = "\u0CBF";
ka['\u0CCDu'] = "\u0CC1";
ka['\u0C8Bu'] = "\u0CC3";
ka['\u0CCDe'] = "\u0CC6";
ka['\u0CCDo'] = "\u0CCA";
ka['\u200Ci'] = "\u0CC8";
ka['\u200Cu'] = "\u0CCC";
ka['\u200C-'] = "\u200D";
ka['\u200C:'] = ":";
ka['-'] = "\u200D";
//VowBig
ka['\u200Ca'] = "\u0CBE";
ka['\u0CBFi'] = "\u0CC0";
ka['\u0CC1u'] = "\u0CC2";
ka['\u0CC3u'] = "\u0CC4";
ka['\u0CC6e'] = "\u0CC7";
ka['\u0CCAo'] = "\u0CCB";
ka['\u0CCDA'] = "\u0CBE";
ka['\u0CCDI'] = "\u0CC0";
ka['\u0CCDU'] = "\u0CC2";
ka['\u0C8BU'] = "\u0CC4";
ka['\u0CCDE'] = "\u0CC7";
ka['\u0CCDO'] = "\u0CCB";
//Vows
ka['\u0C85i'] = "\u0C90";
ka['\u0C85u'] = "\u0C94";
ka['\u0C85a'] = "\u0C86";
ka['\u0C87i'] = "\u0C88";
ka['\u0C89u'] = "\u0C8A";
ka['\u0C8Ee'] = "\u0C8F";
ka['\u0C92o'] = "\u0C93";

ka['a'] = "\u0C85";
ka['A'] = "\u0C86";
ka['i'] = "\u0C87";
ka['I'] = "\u0C88";
ka['u'] = "\u0C89";
ka['U'] = "\u0C8A";
ka['e'] = "\u0C8E";
ka['E'] = "\u0C8F";
ka['o'] = "\u0C92";
ka['O'] = "\u0C93";
ka['q'] = "\u0C95\u0CCD";
//Nums(txt);
ka['\u200D1'] = "\u0CE7";
ka['\u200D2'] = "\u0CE8";
ka['\u200D3'] = "\u0CE9";
ka['\u200D4'] = "\u0CEA";
ka['\u200D5'] = "\u0CEB";
ka['\u200D6'] = "\u0CEC";
ka['\u200D7'] = "\u0CED";
ka['\u200D8'] = "\u0CEE";
ka['\u200D9'] = "\u0CEF";
ka['\u200D0'] = "\u0CE6";
ka['(.+)\u200C(.+)'] = "$1$2";