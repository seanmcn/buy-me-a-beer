<form method="POST">
	<div class="bmabWrapper">
		<?php if ( $image ) { ?>
            <div class="bmabWidgetImageWrapper">
                <img id="bmabWidgetImage" src="<?php echo $image; ?>"/>
		</div>
		<?php } ?>
        <div class="bmabContentWrapper">
            <h3 id="bmabWidgetTitle"><?php echo $title; ?></h3>
            <div id="bmabWidgetDescription">
				<?php echo $description; ?>
			</div>
            <div id="bmabForm">
                <!-- todo add hidden label -->
                <select name="bmabItemSelect" id="bmabItemSelect">
		            <?php foreach ( $items as $item ) { ?>
                        <option class="bmabOption" value="<?php echo $item->id; ?>"><?php echo $item->name; ?>
                            - <?php echo $item->price; ?></option>
					<?php } ?>
				</select>
                <input type="hidden" name="bmabWidgetId" id="bmabWidgetId"
                       value="<?php echo $widgetId; ?>">
                <button id="bmabBuyButton">Buy!</button>
			</div>
		</div>
	</div>
</form>