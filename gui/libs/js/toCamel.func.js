/**
 * http://jamesroberts.name/blog/2010/02/22/string-functions-for-javascript-trim-to-camel-case-to-dashed-and-to-underscore/
 * Written by james on February 22, 2010
 */
String.prototype.toCamel = function(){
	return this.replace(/(\-[a-z])/g, function($1){return $1.toUpperCase().replace('-','');});
};