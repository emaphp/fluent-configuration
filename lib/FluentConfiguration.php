<?php
/**
 * The FluentConfiguration trait stores a list of configuration values as an associative array
 * @author emaphp
 */
trait FluentConfiguration {
	/**
	 * Configuration values
	 * @var array
	 */
	protected $config = [];
	
	/**
	 * Indicates if an instance gets cloned whenever a new configuration is applied
	 * @var boolean
	 */
	public $preserveInstance = false;
	
	/**
	 * Sets an instance configuration array
	 * @param array $config
	 */
	public function setConfig(array $config) {
		$this->config = $config;
	}
	
	/**
	 * Obtains instance configuration array
	 * @return array
	 */
	public function getConfig() {
		return $this->config;
	}
	
	/**
	 * Validates an option name
	 * @param string $name
	 * @throws \InvalidArgumentException
	 */
	protected function validateName($name) {
		if (!is_string($name) || empty($name))
			throw new \InvalidArgumentException("Option name must be a valid string");
	}
	
	/**
	 * Declares a non-transient configuration value
	 * @param string $name
	 * @param mixed $value
	 * @throws \InvalidArgumentException
	 */
	public function setOption($name, $value) {
		$this->validateName($name);
		$this->config[$name] = $value;
	}
	
	/**
	 * Obtains a configuration value
	 * @param string $name
	 * @throws \InvalidArgumentException
	 * @return mixed
	 */
	public function getOption($name) {
		$this->validateName($name);
		if (array_key_exists($name, $this->config))
			return $this->config[$name];
	
		return null;
	}
	
	/**
	 * Determines if current instance has the given option
	 * @param string $name
	 * @return boolean
	 */
	public function hasOption($name) {
		return array_key_exists($name, $this->config);
	}
	
	/**
	 * Clones the current object and merges configuration values with the new ones
	 * Ex: $newconf = $conf->merge(['key1' => 'test', 'key2' => 'test']);
	 * @param array $values
	 * @param boolean $invert
	 * @throws \InvalidArgumentException
	 * @return \FluentConfiguration
	 */
	public function merge(array $values, $invert = false) {
		$obj = $this->preserveInstance ? $this : clone $this;
		$obj->config = ($invert) ? array_merge($values, $this->config) : array_merge($this->config, $values);
		return $obj;
	}
	
	/**
	 * Creates a copy of this object removing the given configuration options
	 * Ex: $config->discard('map.type, 'map.params');
	 * @return \FluentConfiguration
	 */
	public function discard() {
		$filter = array_flip(func_get_args());
		$obj = $this->preserveInstance ? $this : clone $this;
		$obj->config = array_diff_key($this->config, $filter);
		return $obj;
	}
	
	/**
	 * Adds a transient configuration value
	 * Ex: $newconf = $config->option('new_key', 'new_value);
	 * @param string $name
	 * @param mixed $value
	 * @throws \InvalidArgumentException
	 * @return \FluentConfiguration
	 */
	public function option($name, $value) {
		$this->validateName($name);
		return $this->merge([$name => $value]);
	}
	
	/**
	 * Pushes a list of values to a configuration key
	 * Ex: $config->push('list', 'a', 'b', c')
	 * @param string $name
	 * @return \FluentConfiguration
	 */
	public function push($name) {
		$this->validateName($name);
		$obj = $this->preserveInstance ? $this : clone $this;
		$args = func_get_args();
		array_shift($args);
		if (empty($args))
			return 0;
		
		if (!array_key_exists($name, $obj->config))
			$obj->config[$name] = $args;
		else {
			if (!is_array($obj->config[$name]))
				$obj->config[$name] = [$obj->config[$name]];
			
			foreach($args as $arg)
				$obj->config[$name][] = $arg; //faster than array_push
		}
		
		return $obj;
	}
	
	/**
	 * Pops one element off the end of a configuration value
	 * Ex: $value = $conf->pop('key');
	 * @param string $name
	 * @return mixed
	 */
	public function pop($name) {
		$this->validateName($name);
		
		if (!array_key_exists($name, $this->config))
			return null;
		
		if (!is_array($this->config[$name])) {
			$value = $this->config[$name];
			unset($this->config[$name]);
			return $value;
		}
		
		return array_pop($this->config[$name]);
	}
}