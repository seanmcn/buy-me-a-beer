<select id="bmabTitleDescripID" name="bmabTitleDescripID">
<?php foreach($titlesAndDescriptions as $td) {
	?>
	<option id="<?php echo $td->id;?>"><?php echo $td->title;?></option>
<?php } ?>
</select>

<?php
$c = 1;
foreach($titlesAndDescriptions as $td) { ?>
	<div class="tdPreview" id="<?php echo $td->id?>" <?php echo $c == 1 ? '' : 'style="display:none;"'; ?>>
			<h3><?php echo $td->title;?></h3>
			<img src="<?php echo $td->image;?>" style="max-width:200px;"/>
			<p><?php echo $td->description;?></p>
	</div>
<?php $c++; }?>