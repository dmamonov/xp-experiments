// {{{ Throwable
lang.Throwable = define('lang.Throwable', null, function (message) { 
  this.message = message;
  this.fillInStacktrace();
});

lang.Throwable.prototype= Error.prototype;

// root-trait
lang.Throwable.prototype.getClass = function() {
  return new lang.XPClass(this.__class);
}
lang.Throwable.prototype.getClassName = function() {
  return this.__class;
}
lang.Throwable.prototype.equals = function(cmp) {
  return this == cmp;
}
// root-trait

lang.Throwable.prototype.message = '';
lang.Throwable.prototype.stacktrace = new Array();

lang.Throwable.prototype.getMessage = function() {
  return this.message;
}

lang.Throwable.prototype.stringOf = function(arg) {
  switch (typeof(arg)) {
    case 'number': return arg;
    case 'boolean': return arg ? 'true' : 'false';
    case 'string': {
      var cut = arg.indexOf("\n");
      cut = cut < 0 ? 0x40 : Math.min(cut, 0x40);
      return '"' + (arg.length > cut ? arg.substring(0, cut) + '...' : arg) + '"';
    }
    case 'object': {
      if (null === arg) {
        return 'null';
      } else if (arg instanceof Array) {
        return 'array[' + arg.length + ']';
      } else if (arg.__class === undefined) {
        return Object.prototype.toString.call(arg);
      } else {
        return arg.__class + '{}';
      }
    }
  }
  return typeof(arg);
}

lang.Throwable.prototype.fillInStacktrace = function () {
  var current= arguments.callee.caller;
  while (current && current !== global.__main) {
    var f= current.toString();
    var a= '';
    for (var i= 0; i < current.arguments.length; i++) {
      a += ', ' + this.stringOf(current.arguments[i]);
    }
    this.stacktrace.push((f.substring(0, f.indexOf('{')) || '<anonymous>') + '(' + a.substring(2) + ')');
    current = current.caller;
  }
}

lang.Throwable.prototype.toString = function() {
  var r = this.__class + '(' + this.message + ")\n";
  for (var i= 0; i < this.stacktrace.length; i++) {
    r += '  at ' + this.stacktrace[i] + "\n";
  }
  r += '  at <main>';
  return r;
}
// }}}
