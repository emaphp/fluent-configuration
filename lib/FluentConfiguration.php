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
	 * Declares a non-transient configuration value
	 * @param string $name
	 * @param mixed $value
	 * @throws \InvalidArgumentException
	 */
	public function setOption($name, $value) {
		if (!is_string($name) || empty($name))
			throw new \InvalidArgumentException("Option name must be a valid string");
	
		$this->config[$name] = $value;
	}
	
	/**
	 * Obtains a configuration value
	 * @param string $name
	 * @throws \InvalidArgumentException
	 * @return mixed
	 */
	public function getOption($name) {
		if (!is_string($name) || empty($name))
			throw new \InvalidArgumentException("Option name must be a valid string");
	
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
	 * @return FluentConfiguration
	 */
	public function merge(array $values, $invert = false) {
		$obj = $this->preserveInstance ? $this : clone $this;
		$obj->config = ($invert) ? array_merge($values, $this->config) : array_merge($this->config, $values);
		return $obj;
	}
	
	/**
	 * Creates a copy of this object removing the given configuration options
	 * Ex: $config->discard('map.type, 'map.params');
	 * @return FluentConfiguration
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
	 * @return FluentConfiguration
	 */
	public function option($name, $value) {
		if (!is_string($name) || empty($name))
			throw new \InvalidArgumentException("Option name must be a valid string");
	
		return $this->merge([$name => $value]);
	}
	
	/**
	 * Appends a configuration value to an existing key
	 * Ex: $conf->setOption('test', 1); $newconf = $conf->append('test', 2);
	 * @param string $key
	 * @param mixed $value
	 * @return FluentConfiguration
	 */
	public function append($key, $value) {
		if (!is_string($key) || empty($key)) {
			throw new \InvalidArgumentException("Option name must be a valid string");
		}

		$args = func_get_args();
		array_shift($args);		
		$obj = $this->preserveInstance ? $this : clone $this;
		
		if (array_key_exists($key, $obj->config)) {
			if (!is_array($obj->config[$key]))
				$obj->config[$key] = [$obj->config[$key]];
			
			foreach ($args as $arg)
				array_push($obj->config[$key], $arg);
		}
		else
			$obj->config[$key] = $args;
	
		return $obj;
	}
}
?>
