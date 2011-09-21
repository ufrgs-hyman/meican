<?php

class Router {

	public function __construct($defaults = array()){
		$this->defaults = array_merge(array('app' => '', 'controller' => '', 'action' => '', 'params' => array()));
	}

/**
 * Checks to see if the given URL can be parsed by this route.
 * If the route can be parsed an array of parameters will be returned; if not,
 * false will be returned. String urls are parsed if they match a routes regular expression.
 *
 * @param string $url The url to attempt to parse.
 * @return mixed Boolean false on failure, otherwise an array or parameters
 * @access public
 */
	public function parse($url) {
		$val = explode('/', $url);
		if (count($val) >= 3)
			$route = array('app' => $val[0], 'controller' => $val[1], 'action' => $val[2], 'params' => array_slice($val, 3));
		else if (count($val) == 2)
			$route = array('app' => $val[0], 'controller' => $val[1]);
		else if (count($val) == 1)
			$route = array('app' => $val[0]);
		$route = array_merge($this->defaults, $route);
		return $route;
		}
	}

}
