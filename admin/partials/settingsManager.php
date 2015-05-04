<script type="text/javascript">
    jQuery.noConflict();
    jQuery(document).ready(function ($) {
        bmabInit();
    });
</script>

<div class="bmabWrap">
    <h2>Buy Me A Beer Settings</h2>

    <ul class="subsubsub">
        <li class="all"><a href="" id="bmabMain" class="current bmabPage">Main</span></a> |</li>
        <li class="active"><a href="" id="bmabPQ" class="bmabPage">Manage Prices &amp; Quantities</a>
            |
        </li>
        <li class="inactive"><a href="" id="bmabDescriptions" class="bmabPage">Manage Titles &
                Descriptions</a>
            |
        </li>
        <li class="inactive"><a href="" id="bmabPayments" class="bmabPage">Payments</a>

            |
        </li>
        <li class="inactive"><a href="" id="bmabHelp" class="bmabPage">Help</a></li>
    </ul>

    <div id="alertArea"></div>

    <!-- Main -->
    <div class="bmabContent" id="bmabMain">
        <p>
            <strong>Where do I get my Paypal ID?</strong> <br/>
            You can get a Paypal REST ID by <a href="https://developer.paypal.com/webapps/developer/applications/" target="_blank"> clicking here </a>
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
                        <option id="automatic" value="automatic" <?php if ($displayMode == 'automatic') {
                            echo "selected";
                        }
                        ?>>Automatic (Display on all posts)
                        </option>
                        <option id="manual" value="manual" <?php if ($displayMode == 'manual') {
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
                    <label for="paypaplMode">Paypal Endpoint:</label>
                </th>
                <td>
                    <?php
                    $paypalMode = get_option( 'bmabPaypalMode', 'sandbox' );
                    ?>
                    <select name="paypalMode" id="paypalMode">
                        <option id="sandbox" value="sandbox" <?php if ($paypalMode == 'sandbox') {
                            echo "selected";
                        }
                        ?>>Sandbox
                        </option>
                        <option id="live" value="live" <?php if ($paypalMode == 'live') {
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
                        <?php foreach ($currencies as $key => $currency) {
                            if ($key == $bmabCurrency) {
                                echo "<option id='$key' selected>$currency</option>";
                            } else {
                                echo "<option id='$key'>$currency</option>";
                            }

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

    <!-- Prices and Quantity's -->
    <div class="bmabContent" id="bmabPQ">
        <div class="tablenav top">

            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
                <select name="bmabPQBulkOption" id="bmabPQBulkOption">
                    <option value="-1" selected="selected">Bulk Actions</option>
                    <option value="delete-selected" id="delete">Delete</option>
                </select>
                <input type="submit" name="" id="bmabPQBulk" class="button action" value="Apply">
                <button id="bmabAddPQ" class="button button-primary bmabPage">Add New</button>
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
                <th scope="col" id="quantity" class="manage-column column-name" style="">Quantity Name</th>
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

            <tbody id="bmabPQContent">

            </tbody>

        </table>
        <p class="submit">
            <button id="bmabAddPQ" class="button button-primary bmabPage">Add New</button>
        </p>
    </div>

    <!-- Add PQ -->
    <div class="bmabContent" id="bmabAddPQ">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="newPQName">Quantity Name:</label>
                </th>
                <td>
                    <input type="text" id="newPQName">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="newPQPrice">Price:</label>
                </th>
                <td>
                    <input type="text" id="newPQPrice">
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button id="bmabPQ" class="bmabPage button button-secondary">Cancel</button>
                    &nbsp;&nbsp;&nbsp;
                    <button id="bmabAddPQ" class="bmabAction button button-primary">Add</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Edit PQ -->
    <div class="bmabContent" id="bmabEditPQ">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="editPQName">Quantity Name:</label>
                </th>
                <td>
                    <input type="text" id="editPQName">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="editPQPrice">Price:</label>
                </th>
                <td>
                    <input type="text" id="editPQPrice">
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="hidden" name="editPQId" id="editPQId" value="0"/>
                    <button id="bmabPQ" class="bmabPage button button-secondary">Cancel</button>
                    &nbsp;&nbsp;&nbsp;
                    <button id="bmabEditPQ" class="bmabAction button button-primary">Save</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Descriptions -->
    <div class="bmabContent" id="bmabDescriptions">
        <div class="tablenav top">

            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
                <select name="bmabDescripBulkOption" id="bmabDescripBulkOption">
                    <option value="-1" selected="selected">Bulk Actions</option>
                    <option value="delete-selected" id="delete">Delete</option>
                </select>
                <input type="submit" name="bmabDescripBulk" id="bmabDescripBulk" class="button action" value="Apply">
                <button id="bmabAddDescription" class="button button-primary bmabPage">Add New</button>
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

            <tbody id="bmabDescripContent">

            </tbody>

        </table>
        <p class="submit">
            <button id="bmabAddDescription" class="button button-primary bmabPage">Add New</button>
        </p>
    </div>

    <!-- Add Description -->
    <div class="bmabContent" id="bmabAddDescription">
        <h3>Add Title &amp; Description</h3>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="newDescriptionTitle">Title:</label>
                </th>
                <td>
                    <input type="text" id="newDescriptionTitle">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="newDescriptionDescription">Description:</label>
                </th>
                <td>
                    <textarea id="newDescriptionDescription"></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="descriptionImage">Image:</label>
                </th>
                <td>
                    <div>
                        <input type="text" name="descriptionImage" id="descriptionImage" class="regular-text">
                        <input type="button" name="wordpressUploader" id="wordpressUploader" class="button-secondary" value="Upload Image">

                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button id="bmabDescriptions" class="bmabPage button button-secondary">Cancel</button>
                    &nbsp;&nbsp;&nbsp;
                    <button id="bmabAddDescription" class="bmabAction button button-primary">Add</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Edit Description -->
    <div class="bmabContent" id="bmabEditDescription">
        <h3>Edit Title &amp; Description</h3>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="editDescriptionTitle">Title:</label>
                </th>
                <td>
                    <input type="text" id="editDescriptionTitle" name="editDescriptionTitle">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="editDescriptionDescription">Description:</label>
                </th>
                <td>
                    <textarea id="editDescriptionDescription" name="editDescriptionDescription"></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="descriptionImage">Image:</label>
                </th>
                <td>
                    <div>
                        <input type="text" name="descriptionImage" id="descriptionImage" class="regular-text">
                        <input type="button" name="wordpressUploader" id="wordpressUploader" class="button-secondary" value="Upload Image">

                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="hidden" name="editDescriptionId" id="editDescriptionId" value="0">
                    <button id="bmabDescriptions" class="bmabPage button button-secondary">Cancel</button>
                    &nbsp;&nbsp;&nbsp;
                    <button id="bmabEditDescription" class="bmabAction button button-primary">Save</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Payments -->
    <div class="bmabContent" id="bmabPayments">
        <table class="wp-list-table widefat plugins">
            <thead>
            <tr>
                <th scope="col" class="manage-column column-title">Paypal ID</th>
                <th scope="col" class="manage-column column-title">Price</th>
                <th scope="col" class="manage-column column-title">Email</th>
                <th scope="col" class="manage-column column-title">Name</th>
                <th scope="col" class="manage-column column-title">Date</th>
                <th scope="col" class="manage-column column-title">Post Linked From</th>
                <th scope="col" class="manage-column column-title">Description Title</th>
            </tr>
            </thead>
            <tbody id="bmabPaymentsContent">

            </tbody>
        </table>
    </div>
    <!-- Help -->
    <div class="bmabContent" id="bmabHelp">
        <!-- Todo Sean: Add help content -->
    </div>

</div>