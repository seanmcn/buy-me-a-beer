<form method="POST">
	<div class="bmabWrapper">
		<?php if ( $image ) { ?>
            <div class="bmabImageWrapper">
                <img id="bmabImage" src="<?php echo $image; ?>"/>
		</div>
		<?php } ?>
        <div class="bmabContentWrapper">
            <h3 id="bmabTitle"><?php echo $title; ?></h3>
			<div id="bmabDescription">
				<?php echo $description; ?>
			</div>
            <div id="bmabForm">
                <select name="bmabSelect" id="bmabSelect">
					<?php foreach ( $pqs as $pq ) { ?>
                        <option class="bmabOption" value="<?php echo $pq->id; ?>"><?php echo $pq->name; ?>
							- <?php echo $pq->price; ?></option>
					<?php } ?>
				</select>
				<input type="hidden" name="bmabDescriptionId" id="bmabDescriptionId"
				       value="<?php echo $descriptionId; ?>">
                <button id="bmabBuyButton">Buy!</button>
			</div>
		</div>
	</div>
</form>