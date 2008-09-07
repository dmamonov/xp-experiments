<?php

  $package= 'oel';

  uses(
    'oel.iVisitor',
    'oel.iAcceptor'
  );

  class oel�ExecuteVisitor extends Object implements oel�iVisitor {
    /**
     * execute an instruction
     *
     * @param   oel.iAcceptor acceptor
     * @return  mixed
     */
    public function visit(oel�iAcceptor $acceptor) {
      // visitor for oel�InstructionTreeRoot
      if ($acceptor instanceof oel�InstructionTreeRoot) {
        oel_finalize($acceptor->oparray);
        return oel_execute($acceptor->oparray);
      // visitor for oel�InstructionTree
      } else if ($acceptor instanceof oel�InstructionTree) {
        call_user_func_array($acceptor->name, $acceptor->config);
      }
    }
  }

?>
