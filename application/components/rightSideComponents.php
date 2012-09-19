<?php
class rightSideComponents {
	static function top($p1, $p2) {
		echo "top<br>";
	}
	
	static function middle() {
		echo "middle<br>";
	}
	
	static function bottom() {
		echo "bottom<br>";
	}
	
	static function main() {
		?>
		<div style="background-color: blue;">
			<?= View::renderComp("rightSide->top", array('a', 'b')) ?>
			<?= View::renderComp("rightSide->middle") ?>
			<?= View::renderComp("rightSide->bottom") ?>
		</div>
		<?php
		
		
	}
}

?>