<?php
	namespace Gravitas\PluginBase;

	use DaybreakStudios\Common\Enum\Enum;

	/**
	 * Class NoticeLevel
	 *
	 * @package gravitas\PluginBase
	 *
	 * @method static NoticeLevel ERROR()
	 * @method static NoticeLevel NAG()
	 * @method static NoticeLevel UPDATE()
	 */
	class NoticeLevel extends Enum {
		private $cssClass;

		/**
		 * NoticeLevel constructor.
		 *
		 * @param string $cssClass
		 */
		public function __construct($cssClass) {
			$this->cssClass = $cssClass;
		}

		/**
		 * @return string
		 */
		public function getClassName() {
			return $this->cssClass;
		}

		public static function init() {
			parent::register('ERROR', 'error');
			parent::register('NAG', 'update-nag');
			parent::register('UPDATE', 'updated');
		}
	}