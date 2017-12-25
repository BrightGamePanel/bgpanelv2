/**
 * http://stackoverflow.com/a/15829686
 */
String.prototype.toCamel = function(){
    return this.replace(/^([A-Z])|\s(\w)/g, function(match, p1, p2, offset) {
        if (p2) return p2.toUpperCase();
        return p1.toLowerCase();        
    });
};