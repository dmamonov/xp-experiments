// {{{ Field
function Field(clazz, name, modifiers) {
  {
    this.__class = 'lang.reflect.Field';
    this.clazz = clazz;
    this.name = name;
    this.modifiers = modifiers;
  }
}

Field.prototype= new Object();

Field.prototype.getName = function() {
  return this.name;
}

Field.prototype.toString = function() {
  return this.getClassName() + '<' + (this.modifiers & 1 ? 'static ' : '') + this.clazz.name + '::' + this.name + '>';
}
// }}}
