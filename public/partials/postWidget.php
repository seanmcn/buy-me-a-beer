<style>
	.bmabWrapper {
		position: relative;
		width: 100%;
	}
	.bmabImage {
		position: relative;
		width: 30%;
		float: left;
	}
	.bmabContent {
		position: relative;
		width: 70%;
		float: left;
	}
	#bmabDescription {
		padding: 1em 0em;
	}
</style>
<div class="bmabWrapper">
	<div class="bmabImage">
		<img src="<?php echo $image;?>"/>
	</div>
	<div class="bmabContent">
		<h3><?php echo $title; ?></h3>
		<div id="bmabDescription">
			<?php echo $description; ?>
		</div>
		<div>
			<select name="bmabOption" id="bmabOption">
				<?php foreach($pqs as $pq) { ?>
				<option value="<?php echo $pq->id;?>"><?php echo $pq->name;?> - <?php echo $pq->price;?></option>
				<?php } ?>
			</select>
			<button>Buy!</button>
		</div>
	</div>
</div>

<input type="hidden" name="id" id="name" value="<?php echo $descriptionId; ?>">