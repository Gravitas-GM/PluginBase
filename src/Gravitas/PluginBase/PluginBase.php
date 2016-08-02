<?php
	namespace Gravitas\PluginBase;

	use \Serializable;

	abstract class PluginBase implements Serializable {
		const PLUGIN_ROOT_SEARCH_LEN = 18;

		private static $instance = null;

		/**
		 * @var string
		 */
		protected $optPrefix = '';

		private $pageCache = array();

		abstract function run();

		/**
		 * @return string
		 */
		public function getOptionPrefix() {
			return $this->optPrefix;
		}

		/**
		 * @param string $prefix
		 *
		 * @return $this
		 */
		protected function setOptionPrefix($prefix) {
			$this->optPrefix = $prefix;

			return $this;
		}

		/**
		 * @param string     $name
		 * @param mixed|null $def
		 *
		 * @return mixed
		 */
		public function getOption($name, $def = null) {
			return get_option($this->getOptionPrefix() . $name, $def);
		}

		/**
		 * @param string $name
		 * @param mixed  $value
		 * @param bool   $autoload
		 *
		 * @return $this
		 */
		public function setOption($name, $value, $autoload = true) {
			update_option($this->getOptionPrefix() . $name, $value, $autoload);

			return $this;
		}

		/**
		 * @param string        $title           the page title (displayed in the menu bar)
		 * @param string        $page            the filename to load from the "pages" directory
		 * @param string|null   $type            the capability level required by the page
		 * @param string        $hook            the name of the hook to add the page action to
		 * @param string|null   $plug            the page plug (if not provided, it will be the page title with all
		 *                                       spaces converted to dashes)
		 * @param \Closure|null $callback        an optional callback that can be used to enqueue stylesheets, scripts,
		 *                                       or perform any other operation, when the hook is called (executes
		 *                                       BEFORE `add_menu_page` is called)
		 */
		public function addMenuPage($title, $page, $type = null, $hook = 'admin_menu', $plug = null, \Closure $callback = null) {
			if ($type === null)
				$type = PageType::OPTIONS;

			if ($plug === null)
				$plug = strtolower(str_replace(' ', '-', $title));

			$self = $this;

			add_action($hook, function() use ($title, $type, $plug, $self, $page, $callback) {
				if ($callback !== null)
					call_user_func($callback, $self);

				add_menu_page($title, $title, $type, $plug, function() use ($self, $page) {
					echo $self->getPage($page);
				});
			});
		}

		/**
		 * @param string $page the filename to load
		 *
		 * @return string
		 */
		public function getPage($page) {
			if (array_key_exists($page, $this->pageCache))
				return $this->pageCache[$page];

			if (!file_exists(sprintf('%s/pages/%s.php', $this->getPluginRoot(), $page)))
				return '<div><strong>Page not found.</strong></div>';
			else {
				ob_start();

				include sprintf('%s/pages/%s.php', $this->getPluginRoot(), $page);

				$data = ob_get_clean();
			}

			$this->pageCache[$page] = $data;

			return $data;
		}

		/**
		 * @param string $level
		 * @param string $message
		 *
		 * @return $this
		 */
		public function addNotice($level, $message) {
			add_action('admin_notices', function() use ($level, $message) {
				printf('<div class="%1$s"><p>%2$s</p></div>', $level, $message);
			});

			return $this;
		}

		/**
		 * @param string $name
		 * @param string $src
		 * @param array  $depends
		 * @param bool   $version
		 * @param bool   $footer
		 *
		 * @return string
		 */
		public function addScript($name, $src, array $depends = array(), $version = false, $footer = false) {
			wp_enqueue_script($name, $src, $depends, $version, $footer);

			return $name;
		}

		/**
		 * @param string $name
		 * @param string $src
		 * @param array  $depends
		 * @param bool   $version
		 * @param bool   $footer
		 *
		 * @return string
		 */
		public function addLocalScript($name, $src, array $depends = array(), $version = false, $footer = false) {
			$n = $this->getOptionPrefix() . $name;
			$s = $this->getPluginUrlRoot() . $src;

			return $this->addScript($n, $s, $depends, $version, $footer);
		}

		/**
		 * @return string
		 */
		public function getPluginRoot() {
			$pos = strpos(__DIR__, 'wp-content/plugins') + self::PLUGIN_ROOT_SEARCH_LEN;

			return substr(__DIR__, 0, strpos(__DIR__, '/', $pos + 1));
		}

		/**
		 * @return string
		 */
		public function getPluginUrlRoot() {
			$url = plugins_url('', __DIR__);
			$pos = strpos($url, 'wp-content/plugins') + self::PLUGIN_ROOT_SEARCH_LEN;

			return substr($url, 0, strpos($url, '/', $pos + 1));
		}

		public function serialize() {
			return serialize(array(
				$this->getOptionPrefix(),
			));
		}

		public function unserialize($data) {
			list($this->optPrefix) = unserialize($data);

			self::$instance = $this;
		}

		/**
		 * @return static
		 */
		public static function instance() {
			if (!self::$instance)
				self::$instance = new static();

			return self::$instance;
		}
	}