var I18N = new MeicanI18N;

function MeicanI18N() {
    this._dic = [];
    this._activeLang;
}

MeicanI18N.prototype.begin = function(lang) {
    if(!this._dic[lang]) this._dic[lang] = [];
    this._activeLang = lang;
}

MeicanI18N.prototype.add = function(key, value) {
    this._dic[this._activeLang][key] = value;
}

MeicanI18N.prototype.t = function(key) {
    if(language && this._dic[language]) return this._dic[language][key];
    else return key;
} 