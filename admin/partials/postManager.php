<style>
	table.bmabTable {
		width: 500px;
	}

	table.bmabTable tr {
		width: 500px;
	}

	td.label {
		width: 100px;
	}

	td.input {
		width: 400px;
	}
</style>

<table class="bmabTable">
	<?php if ( $bmabMode == 'manual' ) { ?>
		<tr class="bmabActiveArea">
			<td class="label"><label>Active:</label></td>
			<td class="input">
				<select name="bmabActive" id="bmabActive">
					<option value="0" id="off" <?php if ( $bmabActive == 0 ) {
						echo 'selected';
					} ?>>Off
					</option>
					<option value="1" id="on" <?php if ( $bmabActive == 1 ) {
						echo 'selected';
					} ?>>On
					</option>
				</select>
			</td>
		</tr>
	<?php } ?>
	<tr class="bmabWrapper" <?php if ( $bmabMode == 'manual' && $bmabActive == 0 ) {
		echo "style='display:none;'";
	} ?>>
		<td class="label"><label>Description: </label></td>
		<td class="input">
			<select id="bmabTitleDescripID" name="bmabTitleDescripID">
				<?php foreach ( $titlesAndDescriptions as $td ) {
					?>
					<option value="<?php echo $td->id; ?>"
					        id="<?php echo $td->id; ?>"><?php echo $td->title; ?></option>
				<?php } ?>
			</select>
		</td>
	</tr>
	<tr class="bmabWrapper" <?php if ( $bmabMode == 'manual' && $bmabActive == 0 ) {
		echo "style='display:none;'";
	} ?>>
		<td colspan="2"><h2>Preview</h2>
		<td>
	</tr>
	<tr class="bmabWrapper" <?php if ( $bmabMode == 'manual' && $bmabActive == 0 ) {
		echo "style='display:none;'";
	} ?>>
		<td colspan="2">
			<?php
			$c = 1;
			foreach ( $titlesAndDescriptions as $td ) { ?>
				<div class="tdPreview"
				     value="<?php echo $td->id ?>" <?php echo $c == 1 ? '' : 'style="display:none;"'; ?>>
					<h3><?php echo $td->title; ?></h3>
					<img src="<?php echo $td->image; ?>" style="max-width:200px;"/>

					<p><?php echo $td->description; ?></p>
				</div>
				<?php $c ++;
			} ?>
		</td>
		</td>
	</tr>
</table>