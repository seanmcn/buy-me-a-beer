<!-- Todo Sean: Fix path -->
<form method="POST" action="<?php echo plugins_url('public/ajax/formHandler.php', __DIR__);?>">
	<div class="bmabWrapper">
		<div class="bmabImage">
			<img src="<?php echo $image; ?>"/>
		</div>
		<div class="bmabContent">
			<h3><?php echo $title; ?></h3>

			<div id="bmabDescription">
				<?php echo $description; ?>
			</div>
			<div>
				<select name="bmabOption" id="bmabOption">
					<?php foreach ( $pqs as $pq ) { ?>
						<option value="<?php echo $pq->id; ?>"><?php echo $pq->name; ?>
							- <?php echo $pq->price; ?></option>
					<?php } ?>
				</select>
				<input type="hidden" name="bmabDescriptionId" id="bmabDescriptionId"
				       value="<?php echo $descriptionId; ?>">
				<button id="bmabBuy">Buy!</button>
			</div>
		</div>
	</div>
</form>