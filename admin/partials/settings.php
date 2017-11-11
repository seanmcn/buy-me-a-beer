<script type="text/javascript">
  jQuery.noConflict();
  jQuery(document).ready(function($) {
    bmabInit();
  });
</script>

<div class="bmabWrap">
    <h2>Buy Me A Beer Settings</h2>

    <ul class="subsubsub">
        <li class="all"><a href="" id="bmabViewMain" class="current bmabPage">Main</span></a> |</li>
        <li class="active"><a href="" id="bmabViewItems" class="bmabPage">Manage Items</a> |</li>
        <li class="inactive"><a href="" id="bmabViewGroups" class="bmabPage">Manage Groups</a> |</li>
        <li class="inactive"><a href="" id="bmabViewWidgets" class="bmabPage">Manage Widgets</a> |</li>
        <li class="inactive"><a href="" id="bmabViewPayments" class="bmabPage">Payments</a> |</li>
        <li class="inactive"><a href="" id="bmabViewHelp" class="bmabPage">Help</a></li>
    </ul>

    <div id="alertArea"></div>

    <!-- View Main -->
    <div class="bmabContent" id="bmabViewMain">
        <p>
            <strong>Where do I get my Paypal ID?</strong> <br/>
            You can get a Paypal REST ID by <a href="https://developer.paypal.com/developer/applications"
                                               target="_blank"> clicking here </a>
            and pressing the blue "Create App" button
        </p>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="paypalClientId">Display Mode:</label>
                </th>
                <td>
					<?php
					$displayMode = get_option( 'bmabDisplayMode', 'automatic' );
					?>
                    <select name="bmabDisplayMode" id="bmabDisplayMode">
                        <option value="automatic" value="automatic" <?php if ( $displayMode == 'automatic' ) {
							echo "selected";
						}
						?>>Automatic (Display on all posts)
                        </option>
                        <option value="automatic-all"
                                value="automatic-all" <?php if ( $displayMode == 'automatic-all' ) {
							echo "selected";
						}
						?>>Automatic (Display on all posts &amp; pages)
                        </option>
                        <option value="manual" value="manual" <?php if ( $displayMode == 'manual' ) {
							echo "selected";
						}
						?>>Manual ( Manually choose which posts to display on)
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="paypalClientId">Paypal Email:</label>
                </th>
                <td>
                    <input name="paypalEmail" type="text" id="paypalEmail" value="<?php echo get_option(
						'bmabPaypalEmail', 'Paypal Email Here' ); ?>"
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="paypalMode">Paypal Endpoint:</label>
                </th>
                <td>
					<?php
					$paypalMode = get_option( 'bmabPaypalMode', 'sandbox' );
					?>
                    <select name="paypalMode" id="paypalMode">
                        <option value="sandbox" value="sandbox" <?php if ( $paypalMode == 'sandbox' ) {
							echo "selected";
						}
						?>>Sandbox
                        </option>
                        <option value="live" value="live" <?php if ( $paypalMode == 'live' ) {
							echo "selected";
						}
						?>>Live
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="paypalClientId">Paypal Client ID:</label>
                </th>
                <td>
                    <input name="paypalClientId" type="text" id="paypalClientId" value="<?php echo get_option(
						'bmabPaypalClientId', 'Paypal Client ID Here' ); ?>"
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="paypalSecret">Paypal Secret:</label>
                </th>
                <td>
                    <input name="paypalSecret" type="text" id="paypalSecret" value="<?php echo get_option(
						'bmabPaypalSecret', 'Paypal Secret Here' ); ?>"
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="bmabCurrency">Currency:</label>
                </th>
                <td>
					<?php
					$currencies   = array(
						"AUD" => "Australian dollar",
						"CAD" => "Canadian dollar",
						"EUR" => "Euro",
						"HKD" => "Hong Kong dollar",
						"NZD" => "New Zealand dollar",
						"NOK" => "Norwegian kroner",
						"GBP" => "Pound sterling",
						"SEK" => "Swedish krona",
						"CHF" => "Swiss franc",
						"USD" => "United States dollar"
					);
					$bmabCurrency = get_option( 'bmabCurrency', 'USD' );
					?>
                    <select name="bmabCurrency" id="bmabCurrency">
						<?php foreach ( $currencies as $key => $currency ) {
							if ( $key == $bmabCurrency ) {
								echo "<option value='$key' selected>$currency</option>";
							} else {
								echo "<option value='$key'>$currency</option>";
							}

						}
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="bmabSuccessPage">Donation success page:</label>
                </th>
                <td>
                    <select name="bmabSuccessPage" id="bmabSuccessPage">
						<?php
						$bmabSuccessPage = get_option( 'bmabSuccessPage', false );
						if ( ! $bmabSuccessPage ) {
							echo "<option value='0'>Pick a page to redirect donation success to!</option>";
						}
						$pages = get_pages();
						foreach ( $pages as $page ) {
							$selected = '';
							if ( $page->post_status !== 'publish' ) {
								continue;
							}
							if ( $bmabSuccessPage && $page->ID == $bmabSuccessPage ) {
								$selected = ' selected';
							}
							echo "<option value='$page->ID' $selected>{$page->post_title}</option>";
						}
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="bmabErrorPage">Donation failure page:</label>
                </th>
                <td>
                    <select name="bmabErrorPage" id="bmabErrorPage">
						<?php
						$bmabErrorPage = get_option( 'bmabErrorPage', false );
						if ( ! $bmabErrorPage ) {
							echo "<option value='0'>Pick a page to redirect donation failures to!</option>";
						}
						$pages = get_pages();
						foreach ( $pages as $page ) {
							$selected = '';
							if ( $page->post_status !== 'publish' ) {
								continue;
							}
							if ( $bmabErrorPage && $page->ID == $bmabErrorPage ) {
								$selected = ' selected';
							}
							echo "<option value='{$page->ID}' $selected>{$page->post_title}</option>";
						}
						?>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="settings" class="button button-primary bmabAction"
                   value="Save Changes"></p>
    </div>

    <!-- View Items -->
    <div class="bmabContent" id="bmabViewItems">
        <div class="tablenav top">

            <div class="alignleft actions bulkactions">
                <label for="bmabItemBulkOption" class="screen-reader-text">Select bulk action</label>
                <select name="bmabItemBulkOption" id="bmabItemBulkOption">
                    <option value="-1" selected="selected">Bulk Actions</option>
                    <option value="delete-selected" id="delete">Delete</option>
                </select>
                <input type="submit" name="" id="bmabItemBulk" class="button action" value="Apply">
                <button id="bmabAddItem" class="button button-primary bmabPage">Add New</button>
            </div>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat">
            <thead>
            <tr>
                <th scope="col" id="cb" class="manage-column column-cb check-column" style="">
                    <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                    <input id="cb-select-all-1" type="checkbox">
                </th>
                <th scope="col" id="name" class="manage-column column-name" style="">Name</th>
                <th scope="col" id="price" class="manage-column column-price" style="">Price</th>
            </tr>
            </thead>

            <tfoot>
            <tr>
                <th scope="col" class="manage-column column-cb check-column" style="">
                    <label class="screen-reader-text" for="cb-select-all-2">Select All</label>
                    <input id="cb-select-all-2" type="checkbox">
                </th>
                <th scope="col" class="manage-column column-name" style="">Name</th>
                <th scope="col" class="manage-column column-price" style="">Price</th>
            </tr>
            </tfoot>

            <tbody id="bmabItemsContent">

            </tbody>

        </table>
        <p class="submit">
            <button id="bmabAddItem" class="button button-primary bmabPage">Add New</button>
        </p>
    </div>

    <!-- Add Item -->
    <div class="bmabContent" id="bmabAddItem">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="newItemName">Name:</label>
                </th>
                <td>
                    <input type="text" id="newItemName">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="newItemPrice">Price:</label>
                </th>
                <td>
                    <input type="text" id="newItemPrice">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="newItemGroups">Groups:</label>
                </th>
                <td>
                    <select id="newItemGroups" multiple>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button id="bmabViewItems" class="bmabPage button button-secondary">Cancel</button>
                    &nbsp;&nbsp;&nbsp;
                    <button id="bmabAddItem" class="bmabAction button button-primary">Add</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Edit Item -->
    <div class="bmabContent" id="bmabEditItem">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="editItemName">Name:</label>
                </th>
                <td>
                    <input type="text" id="editItemName">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="editItemPrice">Price:</label>
                </th>
                <td>
                    <input type="text" id="editItemPrice">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="editItemGroups">Groups:</label>
                </th>
                <td>
                    <select id="editItemGroups" multiple>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="hidden" name="editItemId" id="editItemId" value="0"/>
                    <button id="bmabViewItems" class="bmabPage button button-secondary">Cancel</button>
                    &nbsp;&nbsp;&nbsp;
                    <button id="bmabEditItem" class="bmabAction button button-primary">Save</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- View Groups -->
    <div class="bmabContent" id="bmabViewGroups">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <label for="bmabGroupBulkOption" class="screen-reader-text">Select bulk action</label>
                <select name="bmabGroupBulkOption" id="bmabGroupBulkOption">
                    <option value="-1" selected="selected">Bulk Actions</option>
                    <option value="delete-selected" id="delete">Delete</option>
                </select>
                <input type="submit" name="bmabGroupBulk" id="bmabGroupBulk" class="button action" value="Apply">
                <button id="bmabAddGroup" class="button button-primary bmabPage">Add New</button>
            </div>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat plugins">
            <thead>
            <tr>
                <th scope="col" id="cb" class="manage-column column-cb check-column" style="">
                    <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                    <input id="cb-select-all-1" type="checkbox">
                </th>
                <th scope="col" id="name" class="manage-name column-title" style="">Name</th>
            </tr>
            </thead>

            <tfoot>
            <tr>
                <th scope="col" class="manage-column column-cb check-column" style="">
                    <label class="screen-reader-text" for="cb-select-all-2">Select All</label>
                    <input id="cb-select-all-2" type="checkbox">
                </th>
                <th scope="col" class="manage-column column-name" style="">Name</th>
            </tr>
            </tfoot>

            <tbody id="bmabGroupsContent">

            </tbody>

        </table>
        <p class="submit">
            <button id="bmabAddGroup" class="button button-primary bmabPage">Add New</button>
        </p>
    </div>

    <!-- Add Group -->
    <div class="bmabContent" id="bmabAddGroup">
        <h3>Add Group</h3>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="newGroupName">Name:</label>
                </th>
                <td>
                    <input type="text" id="newGroupName">
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button id="bmabViewGroups" class="bmabPage button button-secondary">Cancel</button>
                    &nbsp;&nbsp;&nbsp;
                    <button id="bmabAddGroup" class="bmabAction button button-primary">Add</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Edit Group -->
    <div class="bmabContent" id="bmabEditGroup">
        <h3>Edit Group</h3>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="editGroupName">Name:</label>
                </th>
                <td>
                    <input type="text" id="editGroupName" name="editGroupName">
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="hidden" name="editGroupId" id="editGroupId" value="0">
                    <button id="bmabViewGroups" class="bmabPage button button-secondary">Cancel</button>
                    &nbsp;&nbsp;&nbsp;
                    <button id="bmabEditGroup" class="bmabAction button button-primary">Save</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- View Widgets -->
    <div class="bmabContent" id="bmabViewWidgets">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <label for="bmabWidgetBulkOption" class="screen-reader-text">Select bulk action</label>
                <select name="bmabWidgetBulkOption" id="bmabWidgetBulkOption">
                    <option value="-1" selected="selected">Bulk Actions</option>
                    <option value="delete-selected" id="delete">Delete</option>
                </select>
                <input type="submit" name="bmabWidgetBulk" id="bmabWidgetBulk" class="button action" value="Apply">
                <button id="bmabAddWidget" class="button button-primary bmabPage">Add New</button>
            </div>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat plugins">
            <thead>
            <tr>
                <th scope="col" id="cb" class="manage-column column-cb check-column" style="">
                    <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                    <input id="cb-select-all-1" type="checkbox">
                </th>
                <th scope="col" id="name" class="manage-column column-title" style="">Title</th>
                <th scope="col" id="description" class="manage-column column-description" style="">Description</th>
                <th scope="col" id="description" class="manage-column column-image" style="">Image</th>
            </tr>
            </thead>

            <tfoot>
            <tr>
                <th scope="col" class="manage-column column-cb check-column" style="">
                    <label class="screen-reader-text" for="cb-select-all-2">Select All</label>
                    <input id="cb-select-all-2" type="checkbox">
                </th>
                <th scope="col" class="manage-column column-title" style="">Title</th>
                <th scope="col" class="manage-column column-description" style="">Description</th>
                <th scope="col" class="manage-column column-image" style="">Image</th>
            </tr>
            </tfoot>

            <tbody id="bmabWidgetsContent">

            </tbody>

        </table>
        <p class="submit">
            <button id="bmabAddWidget" class="button button-primary bmabPage">Add New</button>
        </p>
    </div>

    <!-- Add Widget -->
    <div class="bmabContent" id="bmabAddWidget">
        <h3>Add Widget</h3>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="newWidgetTitle">Title:</label>
                </th>
                <td>
                    <input type="text" id="newWidgetTitle">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="newWidgetDescription">Description:</label>
                </th>
                <td>
                    <textarea id="newWidgetDescription" cols="50"></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="newWidgetImage">Image:</label>
                </th>
                <td>
                    <div>
                        <input type="text" name="newWidgetImage" id="newWidgetImage" class="regular-text">
                        <input type="button" name="wordpressUploader" id="wordpressUploader"
                               data-input-id="newWidgetImage" class="button-secondary" value="Upload Image">

                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button id="bmabViewWidgets" class="bmabPage button button-secondary">Cancel</button>
                    &nbsp;&nbsp;&nbsp;
                    <button id="bmabAddWidget" class="bmabAction button button-primary">Add</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Edit Widget -->
    <div class="bmabContent" id="bmabEditWidget">
        <h3>Edit Widget</h3>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="editWidgetTitle">Title:</label>
                </th>
                <td>
                    <input type="text" id="editWidgetTitle" name="editWidgetTitle">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="editWidgetDescription">Description:</label>
                </th>
                <td>
                    <textarea id="editWidgetDescription" name="editWidgetDescription" cols="50"></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="editWidgetImage">Image:</label>
                </th>
                <td>
                    <div>
                        <input type="text" name="editWidgetImage" id="editWidgetImage" class="regular-text">
                        <input type="button" name="wordpressUploader" id="wordpressUploader"
                               data-input-id="editWidgetImage" class="button-secondary"
                               value="Upload Image">

                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="hidden" name="editWidgetId" id="editWidgetId" value="0">
                    <button id="bmabViewWidgets" class="bmabPage button button-secondary">Cancel</button>
                    &nbsp;&nbsp;&nbsp;
                    <button id="bmabEditWidget" class="bmabAction button button-primary">Save</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- View Payments -->
    <div class="bmabContent" id="bmabViewPayments">
        <table class="wp-list-table widefat plugins">
            <thead>
            <tr>
                <th scope="col" class="manage-column column-title">Paypal ID</th>
                <th scope="col" class="manage-column column-title">Price</th>
                <th scope="col" class="manage-column column-title">Email</th>
                <th scope="col" class="manage-column column-title">Name</th>
                <th scope="col" class="manage-column column-title">Date</th>
                <th scope="col" class="manage-column column-title">Post Linked From</th>
            </tr>
            </thead>
            <tbody id="bmabPaymentsContent">

            </tbody>
        </table>
    </div>

    <!-- Help -->
    <div class="bmabContent" id="bmabViewHelp">
        <!-- Todo Sean: Add help content -->
    </div>

</div>