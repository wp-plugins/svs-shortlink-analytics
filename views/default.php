<?php require_once('_header.php'); ?>
<form method="post" action="<?php echo admin_url()?>admin.php?page=svs_shortlink_analytics&Action=Add">
    <div id="title_table_svs">
        <h3>Add new link</h3>
    </div>
    <table class="widefat">
        <thead>
        <tr>
            <th>Name</th>
            <th>Insert your link</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th><input required uiredtype="text" name="name"><span><?php @$error_name ?></span></th>
            <th>
                <select name="select_http">
                    <option>http://</option>
                    <option>https://</option>
                    <option>ftp://</option>
                    <option>ftps://</option>
                </select>
                <input required type="text" name="url">
            </th>
            <th><input type="submit" class="button_primary" name="submit" value="" id="add_link_button"/></th>
        </tr>
        </tbody>
    </table>
</form>
</br>
    <div id="title_table_svs">
        <h3>Your links</h3>
    </div>
<table class="widefat">
    <thead>
    <tr>
        <th>Name</th>
        <th>Original Links</th>
        <th>Generated Links</th>
        <th class="text-align_center">Clicks</th>
        <th class="text-align_center">Visitors</th>
        <th class="text-align_center">Actions</th>
        <th></th>
    </tr>
    </thead>
    <tfoot>
    <th>Name</th>
    <th>Original Links</th>
    <th>Generated Links</th>
    <th class="text-align_center">Clicks</th>
    <th class="text-align_center">Visitors</th>
    <th class="text-align_center">Actions</th>
    <th></th>
    </tfoot>
    <tbody>
    <?php
    foreach ($_data as $link) {
        ?>
        <tr>
            <?php
            echo "<td>".$link->name_link."</td>";
            echo "<td>".$link->original_link."</td>";
            echo "<td>".$link->generated_link."</td>";
            if(!$link->click_count) {
                 echo "<td class='text-align_center'> 0 </td>";
            } else {
                 echo "<td class='text-align_center'>".$link->click_count."</td>";
            }
            if(!$link->unique_count) {
                echo "<td class='text-align_center'> 0 </td>";
            } else {
                echo "<td class='text-align_center'>".$link->unique_count."</td>";
            }
            ?>
            <td  class='text-align_center'>
                <a href="<?php echo admin_url() . "admin.php?page=svs_shortlink_analytics&Action=View&id=$link->PK_links" ?>">
                <img src="<?php echo plugins_url('../images/show_statistics.png', __FILE__ ) ?>" alt="Show Statistics" title="Show Statistics" height="20" width="20">&nbsp;</a>
                <a href="<?php echo admin_url() . "admin.php?page=svs_shortlink_analytics&Action=Reset&id=$link->PK_links" ?>" onclick="javascript: return confirm('Are you sure you want to reset the data for this field?')">
                <img src="<?php echo plugins_url('../images/reset.png', __FILE__ ) ?>" alt="Reset"  title="Reset" height="20" width="20">&nbsp;</a>
                <a href="<?php echo admin_url() . "admin.php?page=svs_shortlink_analytics&Action=Delete&id=$link->PK_links" ?>" onclick="javascript: return confirm('Are you sure you want to delete this link?')">
                <img src="<?php echo plugins_url('../images/delete.png', __FILE__ ) ?>" alt="Delete"  title="Delete" height="20" width="20">&nbsp;</a>
            </td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>
<?php require_once('_footer.php'); ?>