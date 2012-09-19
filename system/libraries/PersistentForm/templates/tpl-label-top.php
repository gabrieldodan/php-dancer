<?php
$form = $form;/* @var $form PersistentForm */
?>
<table>
	<?php
	$controls	= $form->getControls();
	$control	= NULL;/* @var $control PFC_Base */
	foreach ($controls AS $control) {
		if ( is_a($control, "PFC_AnyHtml") ) {
			echo $control->getValue();
			continue;
		}
		?>
		<tr>
			<td>
				<label><?php echo $control->getLabel(); ?></label><br />
				<?php echo $control->render(); ?>
			</td>
		</tr>
		<?php
	}
	?>
</table>