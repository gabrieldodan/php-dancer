<?php
$form = $form;/* @var $form PersistentForm */
?>
		<table>
		<tr>
			<td>Email</td>
			<td>
				<?php
				$form->getControl("email")->render();
				?>
			</td>
		</tr>
		<tr>
			<td>Detalii</td>
			<td>
				<?php
				$form->getControl("detalii")->render();
				?>
			</td>
		</tr>
		</table>
