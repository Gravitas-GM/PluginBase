<?php
	namespace Gravitas\PluginBase;

	class ResourceChain {
		private $plugin;

		private $jsDepList;
		private $cssDepList;

		public function __construct(PluginBase $plugin, array $jsDepList = array(), array $cssDepList = array()) {
			$this->plugin = $plugin;
			$this->jsDepList = $jsDepList;
			$this->cssDepList = $cssDepList;
		}

		/**
		 * @return PluginBase
		 */
		public function getPlugin() {
			return $this->plugin;
		}

		/**
		 * @return array
		 */
		public function getScriptDependencies() {
			return $this->jsDepList;
		}

		/**
		 * @param array $jsDepList
		 *
		 * @return $this
		 */
		protected function setScriptDependencies(array $jsDepList) {
			$this->jsDepList = $jsDepList;

			return $this;
		}

		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		protected function addScriptDependency($name) {
			$this->jsDepList[] = $name;

			return $this;
		}

		/**
		 * @return array
		 */
		public function getStylesheetDependencies() {
			return $this->cssDepList;
		}

		/**
		 * @param array $cssDepList
		 *
		 * @return $this
		 */
		protected function setStylesheetDependencies(array $cssDepList) {
			$this->cssDepList = $cssDepList;

			return $this;
		}

		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		protected function addStylesheetDependency($name) {
			$this->cssDepList[] = $name;

			return $this;
		}

		/**
		 * @param string $name
		 * @param string $src
		 * @param bool   $version
		 * @param bool   $footer
		 *
		 * @return ResourceChain
		 */
		public function addScript($name, $src, $version = false, $footer = false) {
			$n = $this->getPlugin()->addScript($name, $src, $this->getScriptDependencies(), $version, $footer);

			return $this->addScriptDependency($n);
		}

		/**
		 * @param string $name
		 * @param string $src
		 * @param bool   $version
		 * @param bool   $footer
		 *
		 * @return ResourceChain
		 */
		public function addLocalScript($name, $src, $version = false, $footer = false) {
			$n = $this->getPlugin()->addLocalScript($name, $src, $this->getScriptDependencies(), $version, $footer);

			return $this->addScriptDependency($n);
		}

		/**
		 * @param string $name
		 * @param string $src
		 * @param bool   $version
		 * @param bool   $footer
		 *
		 * @return ResourceChain
		 */
		public function addStylesheet($name, $src, $version = false, $footer = false) {
			$n = $this->getPlugin()->addStylesheet($name, $src, $this->getStylesheetDependencies(), $version, $footer);

			return $this->addStylesheetDependency($n);
		}

		/**
		 * @param string $name
		 * @param string $src
		 * @param bool   $version
		 * @param bool   $footer
		 *
		 * @return ResourceChain
		 */
		public function addLocalStylesheet($name, $src, $version = false, $footer = false) {
			$n = $this->getPlugin()->addLocalStylesheet($name, $src, $this->getStylesheetDependencies(), $version,
				$footer);

			return $this->addStylesheetDependency($n);
		}

		/**
		 * @return PluginBase
		 */
		public function done() {
			return $this->getPlugin();
		}
	}