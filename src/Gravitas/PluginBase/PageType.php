<?php
	namespace Gravitas\PluginBase;

	use DaybreakStudios\Common\Enum\Enum;

	/**
	 * Class PageType
	 *
	 * @package gravitas\PluginBase
	 *
	 * @method static PageType OPTIONS()
	 */
	class PageType extends Enum {
		private $capability;

		/**
		 * PageType constructor.
		 *
		 * @param string $capability
		 */
		public function __construct($capability) {
			$this->capability = $capability;
		}

		/**
		 * @return string
		 */
		public function getCapability() {
			return $this->capability;
		}

		public static final function init() {
			parent::register('OPTIONS', 'manage_options');
		}
	}