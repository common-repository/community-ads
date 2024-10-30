<?php
/*
Plugin Name: Community Ads Plugin
Plugin URI: http://omerucel.com/gunluk/2009/07/24/community-ads
Description: Yazarların gönderdiği içeriklerde kendi reklamlarının görüntülenmesini sağlamaya yarayan bir eklenti.
Version: 0.1
Author: Ömer ÜCEL
Author URI: http://www.omerucel.com
*/

// Installer - UnInstaller
function community_ads_install() {
    global $wpdb;

    add_option('community_ads_default_code','pub-0120389385396422','','yes');
    add_option('community_ads_default_channel','5407358612','','yes');

    $table_name = $wpdb->prefix . "community_ads";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
            id mediumint(9) NOT NULL,
            code VARCHAR(20) NOT NULL,
            channel VARCHAR(20) NOT NULL,
            type tinyint(1) NOT NULL default 1,
            UNIQUE KEY id (id)
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

function community_ads_uninstall(){
    delete_option('community_ads_default_code');
    delete_option('community_ads_default_channel');
}

register_deactivation_hook(__FILE__,'community_ads_uninstall');
register_activation_hook(__FILE__,'community_ads_install');

// Init
function community_ads_init(){
    add_action('admin_menu','community_ads_menu');
}
add_action('init','community_ads_init');

// Configs
function community_ads_menu(){
    if (function_exists('add_submenu_page')){
        add_menu_page('Reklam Ayarları','Reklam Ayarları','level_2',__FILE__,'community_ads_conf');
        add_submenu_page(__FILE__,__('Reklam Ayarları'),__('Reklam Ayarları'),'level_2',__FILE__,'community_ads_conf');
    }
}

function community_ads_conf(){
    $community_ads_code = '';
    $community_ads_channel = '';
    $community_ads_type = 1;

    global $wpdb;
    global $current_user;
    get_currentuserinfo();

    if (isset($_POST['community_ads_code'])){
        $code = isset($_POST['community_ads_code']) ? strip_tags(stripslashes($_POST['community_ads_code'])) : '';
        $channel = isset($_POST['community_ads_channel']) ? strip_tags(stripslashes($_POST['community_ads_channel'])) : '';
        $type = isset($_POST['use_community_ads_type']) ? $_POST['use_community_ads_type'] : 1;

        if (trim($code)!=''){
            $row = $wpdb->get_results(sprintf('SELECT count(*) as count FROM %scommunity_ads where id=%d',
                $wpdb->prefix,$current_user->ID
            ));
            if ($row){
                if ($row[0]->count>0){
                    $wpdb->query(sprintf('update %scommunity_ads set code="%s",channel="%s",type=%d where id=%d',
                        $wpdb->prefix,$code,$channel,$type,$current_user->ID
                    ));
                }else{
                    $wpdb->query(sprintf('insert into %scommunity_ads(id,code,channel,type) values(%d,"%s","%s",%d)',
                        $wpdb->prefix,$current_user->ID,$code,$channel,$type
                    ));
                }
            }
        }
    }
    if (isset($_POST['community_ads_default_code'])){
        update_option('community_ads_default_code',strip_tags(stripslashes($_POST['community_ads_default_code'])));
        update_option('community_ads_default_channel',strip_tags(stripslashes($_POST['community_ads_default_channel'])));
    }
    $row = $wpdb->get_results(sprintf('SELECT * FROM %scommunity_ads where id=%d',
        $wpdb->prefix,$current_user->ID
    ));
    if ($row){
        $community_ads_code = $row[0]->code;
        $community_ads_channel = $row[0]->channel;
        $community_ads_type = $row[0]->type;
    }
    ?>
    <div class="wrap">
        <div id="icon-options-general" class="icon32"><br /></div>
        <h2>Reklam Ayarları</h2>
        <br />
        Sitede yayınlanan her yazınızın üst kısmında, 468x60 boyutlarındaki adsense reklamı tercihlerinize göre otomatik olarak görüntülenecektir.
        <br /><br />
        <form method="post" id="community_ads_form">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="community_ads_code">Adsense Kodu</label></th>
                    <td><input name="community_ads_code" type="text" id="community_ads_code" value="<?php echo $community_ads_code; ?>" class="regular-text code" />
                    <span class="description">(örn: pub-0120389385396422)</span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="community_ads_channel">Kanal Kodu</label></th>
                    <td><input name="community_ads_channel" type="text" id="community_ads_channel" value="<?php echo $community_ads_channel; ?>" class="regular-text code" />
                    <span class="description">(örn: 5407358612)</span>
                    </td>
                </tr>
            </table>

            <table class="form-table">
                <tr>
                    <th scope="row" class="th-full">
                    <label for="use_community_my_code">
                        <input name="use_community_ads_type" type="radio" id="use_community_my_code" value="1" <?php if ($community_ads_type==1){?>checked="checked"<?php } ?> />
                        Yazılarımda kendi adsense kodum kullanılsın</label>
                    <br />
                    <label for="use_community_pytr_code">
                        <input name="use_community_ads_type" type="radio" id="use_community_pytr_code" value="0" <?php if ($community_ads_type==0){?>checked="checked"<?php } ?> />
                        Yazılarımda sitenin öntanımlı adsense kodu kullanılsın.</label>
                    <br />
                    <label for="use_community_nothing_code">
                        <input name="use_community_ads_type" type="radio" id="use_community_nothing_code" value="9" <?php if ($community_ads_type==9){?>checked="checked"<?php } ?> />
                        Yazılarımda reklam görüntülenmesin.</label>
                    </th>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="Submit" class="button-primary" value="Değişiklikleri Kaydet" />
            </p>
        </form>
    </div>
    <?php if ($current_user->user_level==10){ ?>
        <div class="wrap">
            <div id="icon-options-general" class="icon32"><br /></div>
            <h2>Eklenti ayarları</h2>
            <form method="post" id="community_ads_form">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="community_ads_code">Öntanımlı Adsense Kodu</label></th>
                        <td><input name="community_ads_default_code" type="text" id="community_ads__default_code" value="<?php echo get_option('community_ads_default_code'); ?>" class="regular-text code" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="community_ads_channel">Öntanımlı Kanal Kodu</label></th>
                        <td><input name="community_ads_default_channel" type="text" id="community_ads_default_channel" value="<?php echo get_option('community_ads_default_channel'); ?>" class="regular-text code" />
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="Submit" class="button-primary" value="Değişiklikleri Kaydet" />
                </p>
            </form>
        </div>
    <?php
    }
}

// show ad
function community_ads_show_ad($author_id=0){
    global $wpdb;
    

    $community_ads_code = '';
    $community_ads_channel = '';
    $community_ads_type = 9;

    $table_name = $wpdb->prefix . "community_ads";
    $row = $wpdb->get_results(sprintf('SELECT * FROM %scommunity_ads where id=%d',
        $wpdb->prefix,$author_id
    ));
    if ($row){
        $community_ads_code = $row[0]->code;
        $community_ads_channel = $row[0]->channel;
        $community_ads_type = $row[0]->type;
    }
    
    if ($community_ads_type==0){
        $community_ads_code = get_option('community_ads_default_code');
        $community_ads_channel = get_option('community_ads_default_channel');
    }

    if ($community_ads_type!=9 && $community_ads_code!='' && $community_ads_channel!=''){
        ?>
        <script type="text/javascript"><!--
        google_ad_client = "<?php echo $community_ads_code; ?>";
        /* 468x60, oluşturulma 23.07.2009 */
        google_ad_slot = "<?php echo $community_ads_channel; ?>";
        google_ad_width = 468;
        google_ad_height = 60;
        //-->
        </script>
        <script type="text/javascript"
        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
        </script>
        <br />
        <?php
    }
}
?>
