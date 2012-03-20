uses('unittest.AssertionFailedError', 'unittest.TestFailure', 'unittest.TestSuccess');

// {{{ TestSuite
unittest.TestSuite = function() {
  {
    if (typeof(this.__class) === 'undefined') this.__class = 'unittest.TestSuite';
  }
}

extend(unittest.TestSuite, lang.Object);

unittest.TestSuite.prototype.tests = new Array();
unittest.TestSuite.prototype.outcome = new Array();

unittest.TestSuite.prototype.addTestClass = function(clazz) {
  this.tests.push(clazz);
}

unittest.TestSuite.prototype.run = function() {

  // Run tests
  util.cmd.Console.write('[');
  for (var i= 0; i < this.tests.length; i++) {
    var methods = this.tests[i].getMethods();

    for (var m= 0; m < methods.length; m++) {
      if (!methods[m].hasAnnotation('test')) continue;
      var instance = this.tests[i].newInstance(methods[m].getName());
      var annotation = methods[m].getAnnotation('test');
      
      instance.setUp();
      try {
        methods[m].invoke(instance, []);
        util.cmd.Console.write('.');
        this.outcome.push(new unittest.TestSuccess(instance));
      } catch (e) {
        if (annotation && typeof(annotation.expect) !== 'undefined' && lang.XPClass.forName(annotation.expect).isInstance(e)) {
          util.cmd.Console.write('.');
          this.outcome.push(new unittest.TestSuccess(instance));
        } else {
          util.cmd.Console.write('E');
          this.outcome.push(new unittest.TestFailure(instance, e));
        }
      }
      instance.tearDown();
    }
  }
  util.cmd.Console.writeLine(']');
  util.cmd.Console.writeLine();
  
  // Display all failures
  var failed= 0;
  var total= 0;
  for (var i= 0; i < this.outcome.length; i++) {
    total++;
    if (!(this.outcome[i] instanceof unittest.TestFailure)) continue;
    failed++;
    util.cmd.Console.writeLine('F ', this.outcome[i].toString());
  }
  util.cmd.Console.writeLine(
    failed ? 'FAIL: ' : 'OK: ', 
    total.toString(), ' run, ', 
    (total - failed).toString(), ' succeeded, ', 
    failed.toString(), ' failed'
  );
}
// }}}
