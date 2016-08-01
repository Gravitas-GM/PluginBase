<?php
	namespace Gravitas\PluginBase;

	use \Serializable;

	abstract class PluginBase implements Serializable {
		const PLUGIN_ROOT_SEARCH_LEN = 18;

		private static $instance = null;

		private $pageCache = array();

		abstract function run();

		/**
		 * @param string               $title    the page title (displayed in the menu bar)
		 * @param string               $page     the filename to load from the "pages" directory
		 * @param PageType|string|null $type     the capability level required by the page
		 * @param string               $hook     the name of the hook to add the page action to
		 * @param string|null          $plug     the page plug (if not provided, it will be the page title with all
		 *                                       spaces converted to dashes)
		 * @param \Closure|null        $callback an optional callback that can be used to enqueue stylesheets, scripts,
		 *                                       or perform any other operation, when the hook is called (executes
		 *                                       BEFORE `add_menu_page` is called)
		 */
		public function addMenuPage($title, $page, $type = null, $hook = 'admin_menu', $plug = null, \Closure $callback = null) {
			if ($type === null)
				$type = PageType::OPTIONS();

			if ($type instanceof PageType)
				$type = $type->getCapability();

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
		 * @param NoticeLevel $level
		 * @param string      $message
		 *
		 * @return $this
		 */
		public function addNotice(NoticeLevel $level, $message) {
			add_action('admin_notices', function() use ($level, $message) {
				printf('<div class="%1$s"><p>%2$s</p></div>', $level, $message);
			});

			return $this;
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
			return serialize(array());
		}

		public function unserialize($data) {
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