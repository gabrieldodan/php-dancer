<?php
/**
 * Wrapper for jQuery Datepicker control
 * @author Gabriel
 *
 */
class PFC_DatePicker extends PFC_Base {
	
	/**
	 * Date format, see jQuery datepicker date format
	 * @var string
	 */
	protected $dateFormat	= 'mm/dd/yy';
	
	
	protected $localization = '';
	
	/**
	 * @return the $dateFormat
	 */
	public function getDateFormat() {
		return $this->dateFormat;
	}

	/**
	 * @param string $dateFormat
	 */
	public function setDateFormat($dateFormat) {
		$this->dateFormat = $dateFormat;
		return $this;
	}


	public function getOption($option) {
		return $this->getAttr($option);
	}

	public function setOption($option, $value) {
		$this->setAttr($option, $value);
		return $this;
	}
	
	public function setOptionsAndEvents($optionsAndEvents) {
		$this->setAttrs($optionsAndEvents);
		return $this;
	}
	
	
	
	public function getEvent($event) {
		return $this->getAttr($event);
	}

	public function setEvent($event, $handler) {
		$this->setAttr($event, $handler);
		return $this;
	}
	
	/**
	 * 
	 * @param string $name
	 * @param array $value . Indexed array, [0] = year, [1] = month, [2] = day
	 * @param array $options
	 * @param array $events
	 */
	function __construct($name, $value, $dateFormat='mm/dd/yy', $localization='') {
		$this->name   		= $name;
		$this->value  		= $value;
		$this->dateFormat	= $dateFormat;
		$this->localization	= $localization;
		parent::__construct();
	}
	/* chaining helper */
	static function newInst($name, $value, $dateFormat='mm/dd/yy', $localization='') {
		return new PFC_DatePicker($name, $value, $dateFormat, $localization);
	}
		
	function buildContent() {
		View::jsBatchAddJqueryUi("ui.datepicker.js");
		View::cssBatchAdd( array($this->form->getCssDir() . "/jquery-ui/jquery-ui.css") );
		//View::head_addJqueryTheme("smoothness");
		
		if ( $this->localization ) {
			View::jsBatchAddJqueryDatepickerI18n($this->localization);
		}
		ob_start();
		?>
		<input value="<?php echo $this->value ?>" type="text" id="<?php echo $this->htmlId() ?>" name="<?php echo $this->htmlName() ?>" /><input type="hidden" id="<?php echo $this->htmlId() ?>-ymd" name="<?php echo $this->htmlId() ?>-ymd" />
		<script>
			var datepickerOptions = {};
			<?php
			// create options object
			$excludeOptions = array('altField', 'altFormat', 'dateFormat');
			foreach ($this->attrs AS $option => $value) {
				if ( !in_array($option, $excludeOptions) ) {
					?>
					datepickerOptions["<?php echo $option ?>"] = <?php echo $value ?>;
					<?php
				}
			}
			?>
			// create datepicker
			$( "#<?php echo $this->htmlId() ?>" ).datepicker({
				altField: "#<?php echo $this->htmlId() ?>-ymd",
				altFormat: "yy-mm-dd",
				dateFormat:"<?php echo ($this->dateFormat ? $this->dateFormat : "mm/dd/yy") ?>"
			});
			
			$("#<?php echo $this->htmlId() ?>").datepicker( "option" , datepickerOptions );
			
			<?php
			if ( $this->localization ) {
				?>
				$(function() {
					$.datepicker.setDefaults($.datepicker.regional['']);
					$("#<?php echo $this->htmlId() ?>").datepicker("option", $.datepicker.regional['<?php echo $this->localization ?>']);
				});
				<?php
			}
			?>
			// applay PersistentForm css scope
			$('#ui-datepicker-div').wrap('<div class="PersistentForm"></div>');
			
		</script>
		<?php
		return ob_get_clean();
		
	}
	
}
?>