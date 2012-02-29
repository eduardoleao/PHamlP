<?php
/* SVN FILE: $Id$ */
/**
 * Sass_tree_SassContext class file.
 * @author			Chris Yates <chris.l.yates@gmail.com>
 * @copyright 	Copyright (c) 2010 PBM Web Development
 * @license			http://phamlp.googlecode.com/files/license.txt
 * @package			PHamlP
 * @subpackage	Sass.tree
 */

/**
 * Sass_tree_SassContext class.
 * Defines the context that the parser is operating in and so allows variables
 * to be scoped.
 * A new context is created for Mixins and imported files.
 * @package			PHamlP
 * @subpackage	Sass.tree
 */
class Sass_tree_SassContext {
	/**
	 * @var Sass_tree_SassContext enclosing context
	 */
	protected $parent;
	/**
	 * @var array mixins defined in this context
	 */
	protected $mixins = array();
	/**
	 * @var array variables defined in this context
	 */
	protected $variables = array();
	/**
	 * @var Sass_tree_SassNode the node being processed
	 */
	public $node;

	/**
	 * Sass_tree_SassContext constructor.
	 * @param Sass_tree_SassContext - the enclosing context
	 * @return Sass_tree_SassContext
	 */
	public function __construct($parent = null) {
		$this->parent = $parent;
	}

	/**
	 * Adds a mixin
	 * @param string name of mixin
	 * @return Sass_tree_SassMixinDefinitionNode the mixin
	 */
	public function addMixin($name, $mixin) {
		$this->mixins[$name] = $mixin;
		return $this;
	}

	/**
	 * Returns a mixin
	 * @param string name of mixin to return
	 * @return Sass_tree_SassMixinDefinitionNode the mixin
	 * @throws Sass_tree_SassContextException if mixin not defined in this context
	 */
	public function getMixin($name) {
		if (isset($this->mixins[$name])) {
			return $this->mixins[$name];
		}
		elseif (!empty($this->parent)) {
			return $this->parent->getMixin($name);
		}
		throw new Sass_tree_SassContextException('Undefined {what}: {name}', array('{what}'=>'Mixin', '{name}'=>$name), $this->node);
	}

	/**
	 * Returns a variable defined in this context
	 * @param string name of variable to return
	 * @return string the variable
	 * @throws Sass_tree_SassContextException if variable not defined in this context
	 */
	public function getVariable($name) {
		if (isset($this->variables[$name])) {
			return $this->variables[$name];
		}
		elseif (!empty($this->parent)) {
			return $this->parent->getVariable($name);
		}
		else {
			throw new Sass_tree_SassContextException('Undefined {what}: {name}', array('{what}'=>'Variable', '{name}'=>$name), $this->node);
		}
	}

	/**
	 * Returns a value indicating if the variable exists in this context
	 * @param string name of variable to test
	 * @return boolean true if the variable exists in this context, false if not
	 */
	public function hasVariable($name) {
		return isset($this->variables[$name]);
	}

	/**
	 * Sets a variable to the given value
	 * @param string name of variable
	 * @param sassLiteral value of variable
	 */
	public function setVariable($name, $value) {
		$this->variables[$name] = $value;
		return $this;
	}

	/**
	 * Makes variables and mixins from this context available in the parent context.
	 * Note that if there are variables or mixins with the same name in the two
	 * contexts they will be set to that defined in this context.
	 */
	public function merge() {
	  $this->parent->variables =
	  	array_merge($this->parent->variables, $this->variables);
	  $this->parent->mixins = array_merge($this->parent->mixins, $this->mixins);
	}
}