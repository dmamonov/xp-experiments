uses('unittest.TestCase');






;





tests.XPClassTests= define('tests.XPClassTests','unittest.TestCase',function XPClassTests(){unittest.TestCase.apply(this, arguments);});






tests.XPClassTests.prototype.className= function XPClassTests$className(){
this.assertEquals('tests.XPClassTests',this.getClass().getName());};tests.XPClassTests.prototype.className['@']= {test:null};







tests.XPClassTests.prototype.classNameShortCut= function XPClassTests$classNameShortCut(){
this.assertEquals('tests.XPClassTests',this.getClassName());};tests.XPClassTests.prototype.classNameShortCut['@']= {test:null};







tests.XPClassTests.prototype.forName= function XPClassTests$forName(){
this.assertEquals(this.getClass(),lang.XPClass.forName('tests.XPClassTests'));};tests.XPClassTests.prototype.forName['@']= {test:null};







tests.XPClassTests.prototype.forNameNonExistant= function XPClassTests$forNameNonExistant(){
lang.XPClass.forName('non-existant-class');};tests.XPClassTests.prototype.forNameNonExistant['@']= {test:null,expect:'lang.ClassNotFoundException'};







tests.XPClassTests.prototype.hasNameField= function XPClassTests$hasNameField(){
this.assertTrue(this.getClass().hasField('name'));};tests.XPClassTests.prototype.hasNameField['@']= {test:null};







tests.XPClassTests.prototype.nameField= function XPClassTests$nameField(){
field=this.getClass().getField('name');
this.assertInstanceOf('lang.reflect.Field',field);
this.assertEquals('name',field.getName());};tests.XPClassTests.prototype.nameField['@']= {test:null};







tests.XPClassTests.prototype.doesNotHaveNonExistantField= function XPClassTests$doesNotHaveNonExistantField(){
this.assertFalse(this.getClass().hasField('non-existant'));};tests.XPClassTests.prototype.doesNotHaveNonExistantField['@']= {test:null};







tests.XPClassTests.prototype.getNonExistantField= function XPClassTests$getNonExistantField(){
this.getClass().getField('non-existant');};tests.XPClassTests.prototype.getNonExistantField['@']= {test:null,expect:'lang.ElementNotFoundException'};







tests.XPClassTests.prototype.hasNameMethod= function XPClassTests$hasNameMethod(){
this.assertTrue(this.getClass().hasMethod('name'));};tests.XPClassTests.prototype.hasNameMethod['@']= {test:null};







tests.XPClassTests.prototype.nameMethod= function XPClassTests$nameMethod(){
Method=this.getClass().getMethod('name');
this.assertInstanceOf('lang.reflect.Method',Method);
this.assertEquals('name',Method.getName());};tests.XPClassTests.prototype.nameMethod['@']= {test:null};







tests.XPClassTests.prototype.doesNotHaveNonExistantMethod= function XPClassTests$doesNotHaveNonExistantMethod(){
this.assertFalse(this.getClass().hasMethod('non-existant'));};tests.XPClassTests.prototype.doesNotHaveNonExistantMethod['@']= {test:null};







tests.XPClassTests.prototype.getNonExistantMethod= function XPClassTests$getNonExistantMethod(){
this.getClass().getMethod('non-existant');};tests.XPClassTests.prototype.getNonExistantMethod['@']= {test:null,expect:'lang.ElementNotFoundException'};







tests.XPClassTests.prototype.thisIsInstanceofSelf= function XPClassTests$thisIsInstanceofSelf(){
this.assertTrue(this.getClass().isInstance(this));};tests.XPClassTests.prototype.thisIsInstanceofSelf['@']= {test:null};







tests.XPClassTests.prototype.thisIsInstanceofParentClass= function XPClassTests$thisIsInstanceofParentClass(){
this.assertTrue(lang.XPClass.forName('unittest.TestCase').isInstance(this));};tests.XPClassTests.prototype.thisIsInstanceofParentClass['@']= {test:null};







tests.XPClassTests.prototype.thisIsInstanceofObjectClass= function XPClassTests$thisIsInstanceofObjectClass(){
this.assertTrue(lang.XPClass.forName('lang.Object').isInstance(this));};tests.XPClassTests.prototype.thisIsInstanceofObjectClass['@']= {test:null};







tests.XPClassTests.prototype.thisIsNotAnInstanceOfThrowable= function XPClassTests$thisIsNotAnInstanceOfThrowable(){
this.assertFalse(lang.XPClass.forName('lang.Throwable').isInstance(this));};tests.XPClassTests.prototype.thisIsNotAnInstanceOfThrowable['@']= {test:null};







tests.XPClassTests.prototype.nullIsNotAnInstanceOfObject= function XPClassTests$nullIsNotAnInstanceOfObject(){
this.assertFalse(lang.XPClass.forName('lang.Object').isInstance(null));};tests.XPClassTests.prototype.nullIsNotAnInstanceOfObject['@']= {test:null};







tests.XPClassTests.prototype.annotatedClassHasAnnotations= function XPClassTests$annotatedClassHasAnnotations(){
this.assertTrue(lang.XPClass.forName('tests.AnnotatedClass').hasAnnotations());};tests.XPClassTests.prototype.annotatedClassHasAnnotations['@']= {test:null};







tests.XPClassTests.prototype.annotatedClassHasWebserviceAnnotation= function XPClassTests$annotatedClassHasWebserviceAnnotation(){
this.assertTrue(lang.XPClass.forName('tests.AnnotatedClass').hasAnnotation('webservice'));};tests.XPClassTests.prototype.annotatedClassHasWebserviceAnnotation['@']= {test:null};







tests.XPClassTests.prototype.annotatedClassDoesNotHaveTestAnnotation= function XPClassTests$annotatedClassDoesNotHaveTestAnnotation(){
this.assertFalse(lang.XPClass.forName('tests.AnnotatedClass').hasAnnotation('test'));};tests.XPClassTests.prototype.annotatedClassDoesNotHaveTestAnnotation['@']= {test:null};







tests.XPClassTests.prototype.annotatedClassAnnotations= function XPClassTests$annotatedClassAnnotations(){
this.assertEquals({'webservice' : null},lang.XPClass.forName('tests.AnnotatedClass').getAnnotations());};tests.XPClassTests.prototype.annotatedClassAnnotations['@']= {test:null};







tests.XPClassTests.prototype.annotatedClassChildAnnotations= function XPClassTests$annotatedClassChildAnnotations(){
this.assertEquals({'webservice' : null},lang.XPClass.forName('tests.AnnotatedClassChild').getAnnotations());};tests.XPClassTests.prototype.annotatedClassChildAnnotations['@']= {test:null};







tests.XPClassTests.prototype.annotatedClassWebserviceAnnotations= function XPClassTests$annotatedClassWebserviceAnnotations(){
this.assertNull(lang.XPClass.forName('tests.AnnotatedClass').getAnnotation('webservice'));};tests.XPClassTests.prototype.annotatedClassWebserviceAnnotations['@']= {test:null};







tests.XPClassTests.prototype.thisClassHasNoAnnotations= function XPClassTests$thisClassHasNoAnnotations(){
this.assertFalse(this.getClass().hasAnnotations());};tests.XPClassTests.prototype.thisClassHasNoAnnotations['@']= {test:null};







tests.XPClassTests.prototype.thisClassAnnotations= function XPClassTests$thisClassAnnotations(){
this.assertEquals([],this.getClass().getAnnotations());};tests.XPClassTests.prototype.thisClassAnnotations['@']= {test:null};







tests.XPClassTests.prototype.thisClassWebserviceAnnotation= function XPClassTests$thisClassWebserviceAnnotation(){
this.getClass().getAnnotation('webservice');};tests.XPClassTests.prototype.thisClassWebserviceAnnotation['@']= {test:null,expect:'lang.ElementNotFoundException'};







tests.XPClassTests.prototype.thisClassParent= function XPClassTests$thisClassParent(){
this.assertEquals(lang.XPClass.forName('unittest.TestCase'),this.getClass().getParentclass());};tests.XPClassTests.prototype.thisClassParent['@']= {test:null};