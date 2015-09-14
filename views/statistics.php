<!--    SCRIPT DATEPIKER-->
<script>
    jQuery(document).ready(function($){
        $( "#startpicker" ).datepicker({dateFormat:"yy-mm-dd"});
        $( "#endpicker" ).datepicker({dateFormat:"yy-mm-dd"});
    });
</script>


<!--    SCRIPT GOOGLE CHARTS-->
<?php
    $gcres = '';

foreach ($_data as $result) {
    $gcres .= "['$result->field', $result->visits],";
}
$gcres = substr($gcres, 0, -1);
    ?>
<!--            --><?//=$gcres?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
                        <?=$gcres?>
        ]);

        var options = {

            title: '',
            is3D: true
        };

        var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
        chart.draw(data, options);
    }
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
</script>
<?php require_once('_header.php'); ?>
<a id="back_button" href="<?php echo admin_url()?>admin.php?page=svs_shortlink_analytics"><img src="<?php echo plugins_url('../images/back.png', __FILE__ ) ?>" alt="Back" title="Back" height="20" width="50"></a></a> </br>
<?php

    $id = esc_sql(@$_GET['id']);
    $filter = esc_sql(@$_GET['filter']);
    if (!$filter) {
        $filter = "country";
    }
    $displayRange = esc_sql(@$_GET['displayRange']);
    $startDate = esc_sql(@$_GET['Startdate']);
    $endDate = esc_sql(@$_GET['Enddate']);
    $today = date('Y-m-d');
    $firstday = date('Y-m-01');

?>
    <div id="content_svs_statistics" style="display: inline-block">
    <div id="filters_svs">
        <div>
            <h2 style="font-size: 20px;padding-top: 40px">Filter by</h2>
        </div>
        <p <?php if(@$_GET['filter'] == 'country' or !@$_GET['filter']){ echo "class='selected_filter'"; } ?> >
            <a href="<?php echo admin_url() . "admin.php?page=svs_shortlink_analytics&Action=View&id=$id&filter=country&displayRange=$displayRange&Startdate=$startDate&Enddate=$endDate"?>">Country</a></p>
        <p <?php if(@$_GET['filter'] == 'town'){ echo "class='selected_filter'"; } ?> >
            <a href="<?php echo admin_url() . "admin.php?page=svs_shortlink_analytics&Action=View&id=$id&filter=town&displayRange=$displayRange&Startdate=$startDate&Enddate=$endDate" ?>">City</a></p>
        <p <?php if(@$_GET['filter'] == 'browser'){ echo "class='selected_filter'"; } ?> >
            <a href="<?php echo admin_url() . "admin.php?page=svs_shortlink_analytics&Action=View&id=$id&filter=browser&displayRange=$displayRange&Startdate=$startDate&Enddate=$endDate" ?>">Browser</a></p>
        <p <?php if(@$_GET['filter'] == 'system'){ echo "class='selected_filter'"; } ?>  style="border-bottom: 1px solid #E1DBDB;">
            <a href="<?php echo admin_url() . "admin.php?page=svs_shortlink_analytics&Action=View&id=$id&filter=system&displayRange=$displayRange&Startdate=$startDate&Enddate=$endDate" ?>">Operating System</a></p>

        <script type="text/javascript">
            function sort() {
                var select = document.getElementById('select_sort').value;
                document.location.href = '<?php echo admin_url() . "admin.php?page=svs_shortlink_analytics&Action=View&id=$id&filter=$filter&displayRange="?>'+select;
            }
            function piker_dater() {
                var startdate = document.getElementById('startpicker').value;
                var enddate = document.getElementById('endpicker').value;
                document.location.href = '<?php echo admin_url() . "admin.php?page=svs_shortlink_analytics&Action=View&id=$id&filter=$filter&displayRange=$displayRange&Startdate="?>'+startdate+'&Enddate='+enddate;
            }
        </script>

        <label>Select Date:</label>
        <select id="select_sort" onchange="sort()" name="date">
            <option  <?php if (@$_GET['displayRange'] == 'total') { echo "selected"; }?> value="total">Total</option>
            <option  <?php if (@$_GET['displayRange'] == 'thisweek') { echo "selected"; }?> value="thisweek">This Week</option>
            <option  <?php if (@$_GET['displayRange'] == 'thismonth') { echo "selected"; }?> value="thismonth">This Month</option>
            <option  <?php if (@$_GET['displayRange'] == 'lastmonth') { echo "selected"; }?> value="lastmonth">Last Month</option>
            <option  <?php if (@$_GET['displayRange'] == 'custom') { echo "selected"; }?> value="custom">Custom</option>
        </select>
        <div onload id="datepiker" style="display: none;">
            <p>Start Date: <input type="text" id="startpicker"
               <?php
                if ($startDate){ echo "value='$startDate'"; }
                else { echo "value='$firstday'"; }
               ?>
            ></p>
            <p>End Date: <input type="text" id="endpicker"
                <?php
                if ($endDate){ echo "value='$endDate'"; }
                else { echo "value='$today'"; }
                ?>
            </p>
            <p onclick="piker_dater()" id="apply_date">Apply</p>
        </div>
    </div>
    <div id="charts_svs">
        <h2 style="font-size: 20px; text-align: center;">Clicks and Views filter by
            <?php
            if(@$_GET['filter'] == 'country' or !@$_GET['filter']){ echo "Country "; }
            else if (@$_GET['filter'] == 'town' or !@$_GET['filter']){ echo "City "; }
            else if (@$_GET['filter'] == 'browser' or !@$_GET['filter']){ echo "Browser "; }
            else if (@$_GET['filter'] == 'system' or !@$_GET['filter']){ echo "Operating System "; }
            if (@$_GET['displayRange'] or @$_GET['displayRange'] != "" or @$_GET['displayRange'] != "total"){
                if(@$_GET['displayRange'] == "thisweek") {
                    $start = (date('D') != 'Mon') ? date('Y-m-d', strtotime('last Monday')) : date('Y-m-d');
                    $today = date("Y-m-d");
                    echo " and period: ".$start." / " .$today;
                }else if(@$_GET['displayRange'] == "thismonth") {
                    echo ": ".date('Y-M');
                }else if(@$_GET['displayRange'] == "lastmonth") {
                    echo ": ".date('Y-M', strtotime(date('Y-m')." -1 month"));
                }else if(@$_GET['displayRange'] == "custom" and @$_GET['Startdate'] != "" and @$_GET['Enddate'] != "") {
                    echo " and period: ".$startDate." / " .$endDate;
                }
            }
            ?>
        </h2>
        <div id="donutchart" style="width: 600px; height: 300px;"></div>
    </div>
    <div style="clear=both"></div>
    </div>
<div id="title_table_svs">
    <h3>Statistics</h3>
</div>
<table class="widefat">
        <thead>
        <tr id="first_tr">
            <th id="position"></th>
            <th  id="title_table_field">
                <?php
                $title_filter = esc_sql(@$_GET['filter']);
                if (!$title_filter) {
                    echo "Country";
                } else if($title_filter == "system") {
                    echo "Operating System";
                } else if($title_filter == "town") {
                    echo "City";
                }
                else {
                    echo $title_filter;
                }
                ?>
            </th>
            <th>Clicks</th>
            <th>Visitors</th>
            <th>% Sessions</th>
        </tr>
        </thead>
        <tfoot>
        <tr id="last_tr">
        <th id="position"></th>
            <th  id="title_table_field">
                <?php
                $title_filter = esc_sql(@$_GET['filter']);
                if (!$title_filter) {
                    echo "Country";
                } else if($title_filter == "system") {
                    echo "Operating System";
                } else if($title_filter == "town") {
                    echo "City";
                }
                else {
                    echo $title_filter;
                }
                ?>
            </th>
            <th>Clicks</th>
            <th>Visitors</th>
            <th>% Sessions</th>
        </tr>
        </tfoot>
        <tbody>
<?php
$i = 0;
foreach ($_data as $result) {
    ?>
    <tr>
        <?php
        $i++;
        echo "<td>" . $i . ".</td>";
        echo "<td id='$result->field'>".$result->field."</td>";
        echo "<td>".$result->visits."</td>";
        echo "<td>".$result->unique."</td>";
        $procent = ($result->visits * 100)/$result->total;
        $session = number_format($procent, 2);
        echo "<td>".$session." %</td>";
        ?>
    </tr>
<?php
}
?>
</tbody>
</table>
<script>
    var select = document.getElementById('select_sort').value;
    var datepiker = document.getElementById('datepiker');
    if (select == "custom") {
        datepiker.setAttribute("style","display:block;");
    }
</script>
<?php require_once('_footer.php'); ?>