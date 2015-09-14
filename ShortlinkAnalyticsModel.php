<?php


class ShortlinkAnalyticsModel
{

    /**
     * Database connection
     * @param $wpdb
     */

    public function __construct($wpdb)
    {
        $this->db = $wpdb;
    }


    /**
     * Create database structure on plugin activation
     */

    function createDatabase()
    {

       $linksTable = $this->db->prefix . 'svs_shortlinks';
       $statisticsTable = $this->db->prefix . 'svs_statistics';

        $linksSql = "CREATE TABLE IF NOT EXISTS $linksTable (
            `PK_links` int(11) NOT NULL AUTO_INCREMENT,
            `name_link` varchar(255) NOT NULL,
            `generated_link` varchar(255) NOT NULL,
            `original_link` varchar(255) NOT NULL,
            PRIMARY KEY PK_links (PK_links)
            );";

        $statisticsSql = "CREATE TABLE IF NOT EXISTS $statisticsTable (
            `PK_statistics` int(11) NOT NULL AUTO_INCREMENT,
            `FK_links` int(11) NOT NULL,
            `date` timestamp NOT NULL,
            `country` varchar(255) NOT NULL,
            `town` varchar(255) NOT NULL,
            `ip` varchar(255) NOT NULL,
            `browser` varchar(255) NOT NULL,
            `system` varchar(255) NOT NULL,
            `device` varchar(255) NOT NULL,
            PRIMARY KEY PK_statistics (PK_statistics)
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($linksSql);
        dbDelta($statisticsSql);
    }


    /**
     * Get previously created short links
     */

    function getLinks()
    {
        $wp_svs_shortlinks = $this->db->prefix . 'svs_shortlinks';
        $wp_svs_statistics = $this->db->prefix . 'svs_statistics';
        
        $url = $this->getCurrentUrl();
        return $this->db->get_results(
            "
            SELECT
             `links`.
             `PK_links`,
             `links`.
             `name_link`,
             `links`.
             `generated_link`,
             `links`.`original_link`,
             `clicks`.`click_count`,
             `uniques`.
             `unique_count`
           FROM
             $wp_svs_shortlinks as `links`
           LEFT JOIN (
           SELECT
               `FK_links`,
               COUNT(`FK_links`) as `click_count`
           FROM
              $wp_svs_statistics
           GROUP BY
           `FK_links` ) as `clicks`
           ON
              `links`.`PK_links`=`clicks`.`FK_links`
           LEFT JOIN (
           SELECT
               `FK_links`,
               COUNT(DISTINCT `ip`) as `unique_count`
           FROM
               $wp_svs_statistics
           GROUP BY
              `FK_links` ) as `uniques`
           ON
           `links`.`PK_links`=`uniques`.`FK_links`
            "
        );
    }


    /**
     * Add new short link
     */

    function addLink()
    {
        $wp_svs_shortlinks = $this->db->prefix . 'svs_shortlinks';
        $wp_svs_statistics = $this->db->prefix . 'svs_statistics';
        if (empty($_POST)){
            return false;
        }
        $name = esc_sql(@$_POST['name']);
        $select_http = esc_sql(@$_POST['select_http']);
        $url = esc_sql(@$_POST['url']);
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $insert_url = $select_http.$url;
        } else {
            $insert_url = $url;
        }
            if (!$name or $name == "") {
                echo "Name required";
            }
            else if (!$url or $url == "") {
                echo "Url required";
            }
            else {
                $randomnumber = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
                $generated_link = get_site_url() . "/" . $randomnumber;
                $this->db->insert(
                    $wp_svs_shortlinks,
                    array(
                        'name_link' => esc_sql($_POST['name']),
                        'generated_link' => $generated_link,
                        'original_link' => $insert_url
                    ),
                    array(
                        '%s',
                        '%s',
                        '%s'
                    )
                );
            }
    }


    /**
     * Delete shortlink
     */

    function deleteLink()
    {
        $wp_svs_shortlinks = $this->db->prefix . 'svs_shortlinks';
        $wp_svs_statistics = $this->db->prefix . 'svs_statistics';
        
        $id = esc_sql($_GET['id']);
        $this->db->query("
            DELETE FROM
                $wp_svs_shortlinks
            WHERE PK_links='$id'
            ");
        $this->db->query("
            DELETE FROM
                $wp_svs_statistics
            WHERE FK_links='$id'
            ");
    }

    /**
     * Reset shortlink statistics
     */

    function resetLink()
    {
        $wp_svs_shortlinks = $this->db->prefix . 'svs_shortlinks';
        $wp_svs_statistics = $this->db->prefix . 'svs_statistics';

        $id = esc_sql($_GET['id']);
        $this->db->query("
            DELETE FROM
                $wp_svs_statistics
            WHERE FK_links='$id'
            ");
    }

    /**
     * Get statistics for a specific shortlink
     */

    function getStatistics()
    {
        $wp_svs_shortlinks = $this->db->prefix . 'svs_shortlinks';
        $wp_svs_statistics = $this->db->prefix . 'svs_statistics';

        $id = esc_sql(@$_GET['id']);
        $filter = esc_sql(@$_GET['filter']);
        $display_range = esc_sql(@$_GET['displayRange']);
        $startDate = esc_sql(@$_GET['Startdate']);
        $endDate = esc_sql(@$_GET['Enddate']);


        if (!$filter) {
            $filter = "country";
        }

        $and_sql = "";
        if ($display_range == 'thisweek'){
            $and_sql = " AND YEARWEEK(date)=YEARWEEK(NOW())";
        } else if ($display_range == 'thismonth'){
            $and_sql = " AND MONTH(date) = MONTH(NOW())";
        }  else if ($display_range == 'lastmonth'){
            $and_sql = "AND MONTH(date) = (MONTH(NOW())-1)";
        }  else if ($display_range == 'total'){
            $and_sql = "";
        }  else if ($display_range == 'custom'){
            $and_sql = " AND `date` BETWEEN '$startDate' AND '$endDate'";
        }

        return $this->db->get_results(
            "
            SELECT
             `wps`.`$filter` as `field`,
              COUNT(`wps`.`FK_links`) as `visits`  ,
              COUNT(DISTINCT `wps`.`ip`) as `unique`,
			`total_count`.`total`
           FROM
             $wp_svs_statistics as `wps`
			LEFT JOIN
			(
                SELECT
                	`FK_links`,
                	COUNT(*) as `total`

                FROM
               	 $wp_svs_statistics
                WHERE FK_links='$id'
                $and_sql

            ) as `total_count`
			ON
			`total_count`.`FK_links` = `wps`.`FK_links`
           WHERE
            `wps`.`FK_links`='$id'
            $and_sql
           group by `wps`.`$filter`
           LIMIT 10
             ");
    }


    /**
     * Load view
     */

    function loadView($view, $_data = null)
    {
        if ($view == "statistics") {
            require_once('views/statistics.php');
        } else {
            require_once('views/default.php');
        }
        exit;
    }

    /**
     * Get current page url
     */

    private function getCurrentUrl() {
        $pageURL = 'http';
        if (array_key_exists("HTTPS", $_SERVER) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    /**
     * Check if current link is a shortlink & collect statistics & redirect the page
     */

    function checkRedirect() {

        $wp_svs_shortlinks = $this->db->prefix . 'svs_shortlinks';
        $wp_svs_statistics = $this->db->prefix . 'svs_statistics';
        
        $url = $this->getCurrentUrl();
        if (strpos($url, admin_url()) === false){
            $result = $this->db->get_results("
              SELECT
                *
              FROM
                $wp_svs_shortlinks
              WHERE
                generated_link = '$url'
              LIMIT 1
           ");

            if ($this->db->num_rows > 0 ) {
                $shortlinkAnalyticsUtil = new ShortlinkAnalyticsUtil();
                $original_link = $result[0]->original_link;
                $id = $result[0]->PK_links;
                $ip = (string)$shortlinkAnalyticsUtil->getip();
                $browser = (string)$shortlinkAnalyticsUtil->getbrowser();
                $os_platform = (string)$shortlinkAnalyticsUtil->getOS();
                $details = (string)$shortlinkAnalyticsUtil->getcountry();
                $details_obs = json_decode($details);
                $device = (string)$shortlinkAnalyticsUtil->getdevice();
                $country = (string)((!empty($details_obs->geoplugin_countryName)) ? $details_obs->geoplugin_countryName : "Unknown");
                $town = (string)((!empty($details_obs->geoplugin_city)) ? $details_obs->geoplugin_city : "Unknown");
                $this->db->insert(
                    $wp_svs_statistics,
                    array(
                        'FK_links' => $id,
                        'country' => "$country",
                        'town' => " $town",
                        'ip' => $ip,
                        'browser' => $browser,
                        'system' => $os_platform,
                        'device' => $device
                    ),
                    array (
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s'
                    )
                );
                header("Location: ".(string)$original_link);
                die();
            }
        }
    }

}