<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Class File Sharing
 */
$map = array(
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/elFinder.class.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/elFinderConnector.class.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/editors/editor.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/libs/GdBmp.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/elFinderPlugin.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/plugins/AutoResize/plugin.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/plugins/AutoRotate/plugin.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/plugins/Normalizer/plugin.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/plugins/Sanitizer/plugin.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/plugins/Watermark/plugin.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/elFinderSessionInterface.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/elFinderSession.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/elFinderVolumeDriver.class.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/elFinderVolumeDropbox2.class.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/elFinderVolumeFTP.class.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/elFinderVolumeGoogleDrive.class.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/elFinderVolumeGroup.class.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/elFinderVolumeLocalFileSystem.class.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/elFinderVolumeMySQL.class.php',
    FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/elFinderVolumeTrash.class.php',
);

//require FCPATH . 'modules/file_sharing/vendor/autoload.php';

foreach ($map as $key => $value) {
    include_once $value;
}

class switchclass
{
    public function switch_html($status, $name, $id, $input_attr = '')
    {
        $checked = $status == 1 ? "checked" : "";
        return '
        <div class="fs-permisstion-switch">
        <div class="form-group">
        <div class="checkbox checbox-switch switch-primary">
        <label class="swith-label">
        <input data-id="' . $id . '" type="checkbox" name="' . $name . '"  ' . $checked . ' ' . $input_attr . '/>
        <span></span>
        </label>
        </div>
        </div>
        </div>';
    }
}
class File_sharing extends AdminController
{

    /**
     * __construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['staff_model', 'file_sharing_model', 'clients_model', 'roles_model']);
        hooks()->do_action('file_sharing_init');
    }

    /**
     * manage
     * @return view
     */
    public function manage()
    {
        if(!has_permission('file_sharing', '', 'view_file_management')){
            access_denied('file_sharing');
        }

        $data['title'] = _l('file_sharing');

        $this->load->helper('url');
        $data['connector']     = admin_url() . 'file_sharing/file_sharing_media_connector';
        $data['staffs']        = $this->staff_model->get();
        $data['clients']       = $this->clients_model->get();
        $data['roles']         = $this->roles_model->get();
        $data['client_groups'] = $this->clients_model->get_groups();
        $this->load->view('elfinder', $data);
    }

    /**
     * file sharing media connector
     * @return $connector
     */
    public function file_sharing_media_connector()
    {
        $opts = $this->new_root_main();

        $connector = new fs_elFinderConnector(new fs_elFinder($opts));
        $connector->run();
    }

    /**
     * getDirectories
     * @param  string $path
     * @return $directories
     */
    public function getDirectories(string $path)
    {
        $directories = [];
        $items       = scandir($path);
        foreach ($items as $item) {
            if ($item == '..' || $item == '.') {
                continue;
            }

            if (is_dir($path . '/' . $item)) {
                $directories[] = $item;
            }

        }
        return $directories;
    }

    /**
     * setting
     * @return view
     */
    public function setting()
    {
        if(!has_permission('file_sharing', '', 'view_setting_file_sharing')){
            access_denied('file_sharing');
        }
        
        $data['tab'] = $this->input->get('tab');

        if ($data['tab'] == '') {
            $data['tab'] = 'general';
        }
        if ($data['tab'] == 'configuration') {
            $data['tab'] = 'configuration';
        }

        $data['title'] = _l($data['tab']);

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('file_sharing', 'table/table_fs_config_share'));
        }

        $data['staffs']        = $this->staff_model->get('', ['active' => true]);
        $data['clients']       = $this->clients_model->get();
        $data['roles']         = $this->roles_model->get();
        $data['client_groups'] = $this->clients_model->get_groups();

        $this->load->view('setting', $data);
    }

    /* Change status to staff active or inactive / ajax */
    public function change_staff_permissions($id, $type, $status)
    {
        if (has_permission('staff', '', 'edit')) {
            if ($this->input->is_ajax_request()) {
                $this->file_sharing_model->change_staff_permissions($id, $type, $status);
            }
        }
    }

    /**
     * Simple function to demonstrate how to control file access using "accessControl" callback.
     * This method will disable accessing files/folders starting from '.' (dot)
     *
     * @param  string    $attr    attribute name (read|write|locked|hidden)
     * @param  string    $path    absolute file path
     * @param  string    $data    value of volume option `accessControlData`
     * @param  object    $volume  elFinder volume driver object
     * @param  bool|null $isDir   path is directory (true: directory, false: file, null: unknown)
     * @param  string    $relpath file path relative to volume root directory started with directory separator
     * @return bool|null
     **/
    function access($attr, $path, $data, $volume, $isDir, $relpath) {
        $basename = basename($path);
        return $basename[0] === '.'                  // if file/folder begins with '.' (dot)
                 && strlen($relpath) !== 1           // but with out volume root
            ? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
            :  null;                                 // else elFinder decide it itself
    }

    /**
     * new root main
     * @return $opts
     */
    public function new_root_main()
    {
        fs_elFinder::$netDrivers['ftp'] = 'FTP';


// Required for Dropbox network mount
// Installation by composer
// `composer require kunalvarma05/dropbox-php-sdk` on php directory
// Enable network mount
//fs_elFinder::$netDrivers['dropbox2'] = 'Dropbox2';
// Dropbox2 Netmount driver need next two settings. You can get at https://www.dropbox.com/developers/apps
// AND require register redirect url to "YOUR_CONNECTOR_URL?cmd=netmount&protocol=dropbox2&host=1"
// If the elFinder HTML element ID is not "elfinder", you need to change "host=1" to "host=ElementID"
//define('ELFINDER_DROPBOX_APPKEY',    'vy6yhbq95ywx4oc');
//define('ELFINDER_DROPBOX_APPSECRET', '');

// Required for Google Drive network mount
// Installation by composer
// `composer require google/apiclient:^2.0` on php directory
// Enable network mount
//fs_elFinder::$netDrivers['googledrive'] = 'GoogleDrive';
// GoogleDrive Netmount driver need next two settings. You can get at https://console.developers.google.com
// AND require register redirect url to "YOUR_CONNECTOR_URL?cmd=netmount&protocol=googledrive&host=1"
// If the elFinder HTML element ID is not "elfinder", you need to change "host=1" to "host=ElementID"
// Required case when Google API is NOT added via composer
//define('ELFINDER_GOOGLEDRIVE_GOOGLEAPICLIENT', '/path/to/google-api-php-client/vendor/autoload.php');

// Required for Google Drive network mount with Flysystem
// Installation by composer
// `composer require nao-pon/flysystem-google-drive:~1.1 nao-pon/elfinder-flysystem-driver-ext` on php directory
// Enable network mount
//fs_elFinder::$netDrivers['googledrive'] = 'FlysystemGoogleDriveNetmount';
// GoogleDrive Netmount driver need next two settings. You can get at https://console.developers.google.com
// AND require register redirect url to "YOUR_CONNECTOR_URL?cmd=netmount&protocol=googledrive&host=1"
// If the elFinder HTML element ID is not "elfinder", you need to change "host=1" to "host=ElementID"
//define('ELFINDER_GOOGLEDRIVE_CLIENTID',     '');
//define('ELFINDER_GOOGLEDRIVE_CLIENTSECRET', '');
// And "php/.tmp" directory must exist and be writable by PHP.

// Required for One Drive network mount
//  * cURL PHP extension required
//  * HTTP server PATH_INFO supports required
// Enable network mount
//fs_elFinder::$netDrivers['onedrive'] = 'OneDrive';
// GoogleDrive Netmount driver need next two settings. You can get at https://dev.onedrive.com
// AND require register redirect url to "YOUR_CONNECTOR_URL/netmount/onedrive/1"
// If the elFinder HTML element ID is not "elfinder", you need to change "/1" to "/ElementID"
//define('ELFINDER_ONEDRIVE_CLIENTID',     '');
//define('ELFINDER_ONEDRIVE_CLIENTSECRET', '');

// Required for Box network mount
//  * cURL PHP extension required
// Enable network mount
//fs_elFinder::$netDrivers['box'] = 'Box';
// Box Netmount driver need next two settings. You can get at https://developer.box.com
// AND require register redirect url to "YOUR_CONNECTOR_URL?cmd=netmount&protocol=box&host=1"
// If the elFinder HTML element ID is not "elfinder", you need to change "host=1" to "host=ElementID"
/*define('ELFINDER_BOX_CLIENTID',     '');
define('ELFINDER_BOX_CLIENTSECRET', '');*/
        
        $media_folder = FILE_SHARING_FOLDER_NAME;
        $cmd  = $this->input->get('cmd');
        $init = $this->input->get('init');
        $target = $this->input->get('target');
        $h = substr($target ?? '', 3);
        $h = base64_decode(strtr($h, '-_.', '+/='));

        $mediaPath      = FILE_SHARING_MEDIA_PATH.'/';

        if (!is_dir($mediaPath)) {
            mkdir($mediaPath, 0755);
            fs_new_file_database($mediaPath, '', '', 'directory', 1);
        }

        if (!file_exists($mediaPath . '/index.html')) {
            $fp = fopen($mediaPath . '/index.html', 'w');
            if ($fp) {
                fclose($fp);
            }
        }

        $this->load->helper('path');

        $this->db->select('media_path_slug,staffid,firstname,lastname')
            ->from(db_prefix() . 'staff')
            ->where('staffid', get_staff_user_id());
        $user      = $this->db->get()->row();

        $disable_trash = true;
        $trashHash = '';
        if(($h == '\\' || $h == '/' || $h == '' || $h == 'Public') || !(strpos($h, $user->media_path_slug) === false)){
            $disable_trash = false;
            $trashHash = 't1_Lw';
        }

        $fs_global_extension = $this->file_sharing_model->get_global_extension();
        $root_options        = [
            'driver'        => 'LocalFileSystem',
            'path'          => file_sharing_set_realpath($mediaPath),
            'URL'           => site_url('modules/file_sharing/uploads/'.FILE_SHARING_FOLDER_NAME.'/'),
            'uploadMaxSize' => get_option('fs_global_max_size') . 'M',
            'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
            'accessControl' => 'access',
            'trashHash'     => $trashHash,                     // elFinder's hash of trash folder
            'uploadDeny'    => [
                'all',
            ],
            'uploadAllow'   => $fs_global_extension,
            'uploadOrder'   => [
                'deny',
                'allow',
            ],
            'disabled' => [],
            'attributes'    => [
                [
                    'pattern' => '/.tmb/',
                    'hidden'  => true,
                ],
                [
                    'pattern' => '/.quarantine/',
                    'hidden'  => true,
                ],
            ],
        ];
        $config = $this->file_sharing_model->get_staff_config(get_staff_user_id());
        if ($config) {
            $is_read   = $config->is_read == 1 ? true : false;
            $is_upload  = $config->is_upload == 1 ? true : false;
            $is_delete = $config->is_delete == 1 ? true : false;
            $is_write = $config->is_write == 1 ? true : false;
            $is_download = $config->is_download == 1 ? true : false;
        } else {
            $is_read   = get_option('fs_permisstion_staff_view') == 1 ? true : false;
            $is_delete = get_option('fs_permisstion_staff_delete') == 1 ? true : false;
            $is_upload  = get_option('fs_permisstion_staff_upload') == 1 ? true : false;
            $is_write = get_option('fs_permisstion_staff_upload_and_override') == 1 ? true : false;
            $is_download = get_option('fs_permisstion_staff_download') == 1 ? true : false;
        }


        $is_public_admin = false;
        $fs_the_administrator_of_the_public_folder = explode(',', get_option('fs_the_administrator_of_the_public_folder'));

        foreach ($fs_the_administrator_of_the_public_folder as $value) {
            if($value == get_staff_user_id()){
                $is_public_admin = true;
                break;
            }
        }

        $is_share = get_option('fs_permisstion_staff_share') == 1 ? true : false;
        $is_share_to_client = get_option('fs_permisstion_staff_share_to_client') == 1 ? true : false;

        
        $staffPath = FILE_SHARING_MODULE_UPLOAD_FOLDER . '/' . $media_folder . '/' . $user->media_path_slug;
        $path      = file_sharing_set_realpath($staffPath);
        if (empty($user->media_path_slug)) {
            $this->db->where('staffid', $user->staffid);
            $slug = slug_it($user->firstname . ' ' . $user->lastname);
            $this->db->update(db_prefix() . 'staff', [
                'media_path_slug' => $slug,
            ]);
            $user->media_path_slug = $slug;
            $path                  = file_sharing_set_realpath($media_folder . '/' . $user->media_path_slug);
        }

        if (!is_dir($path)) {
            mkdir($path, 0755);
            fs_new_file_database($path, '', '', 'directory', 1);
        }
        if (!file_exists($path . '/index.html')) {
            $fp = fopen($path . '/index.html', 'w');
            if ($fp) {
                fclose($fp);
            }
        }

        //My folder staff
        $myfolderPath = FILE_SHARING_MEDIA_PATH . '/' . $user->media_path_slug . '/My Files';

        if (!is_dir($myfolderPath)) {
            mkdir($myfolderPath, 0755);
        }

        if (!file_exists($myfolderPath . '/index.html')) {
            $fp = fopen($myfolderPath . '/index.html', 'w');
            if ($fp) {
                fclose($fp);
            }
        }
        //folder share of staff
        $sharePath          = FILE_SHARING_MEDIA_PATH . '/' . $user->media_path_slug . '/Shared';

        if (!is_dir($sharePath)) {
            mkdir($sharePath, 0755);
            fs_new_file_database($sharePath, '', '', 'directory', 1);
        }

        if (!file_exists($sharePath . '/index.html')) {
            $fp = fopen($sharePath . '/index.html', 'w');
            if ($fp) {
                fclose($fp);
            }
        }

        //folder trash of staff
        $trashPath          = FILE_SHARING_MEDIA_PATH . '/.trash';

        if (!is_dir($trashPath)) {
            mkdir($trashPath, 0755);
            fs_new_file_database($trashPath, '', '', 'directory', 1);
        }

        if (!file_exists($trashPath . '/index.html')) {
            $fp = fopen($trashPath . '/index.html', 'w');
            if ($fp) {
                fclose($fp);
            }
        }

        //folder trash of staff
        $trashPath          = FILE_SHARING_MEDIA_PATH . '/.trash/' . $user->media_path_slug;

        if (!is_dir($trashPath)) {
            mkdir($trashPath, 0755);
            fs_new_file_database($trashPath, '', '', 'directory', 1);
        }

        if (!file_exists($trashPath . '/index.html')) {
            $fp = fopen($trashPath . '/index.html', 'w');
            if ($fp) {
                fclose($fp);
            }
        }

        //Public
        $publicPath = FILE_SHARING_MEDIA_PATH . '/Public';

        if (!is_dir($publicPath)) {
            mkdir($publicPath, 0755);
        }

        if (!file_exists($publicPath . '/index.html')) {
            $fp = fopen($publicPath . '/index.html', 'w');
            if ($fp) {
                fclose($fp);
            }
        }

        //Client Files
        $clientFilesPath = FILE_SHARING_MEDIA_PATH . '/Client Files';

        if (!is_dir($clientFilesPath)) {
            mkdir($clientFilesPath, 0755);
        }

        if (!file_exists($clientFilesPath . '/index.html')) {
            $fp = fopen($clientFilesPath . '/index.html', 'w');
            if ($fp) {
                fclose($fp);
            }
        }

        $public_root         = $root_options;
        $publicRootPath      = FILE_SHARING_MEDIA_PATH;
        $public_root['path'] = file_sharing_set_realpath($publicRootPath);
        $public_root['URL'] = site_url('modules/file_sharing/uploads/'.FILE_SHARING_FOLDER_NAME);
        
        if (!is_dir($publicRootPath)) {
            mkdir($publicRootPath, 0755);
            fs_new_file_database($publicRootPath, '', '', 'directory', 1);
        }

        if (!file_exists($publicRootPath . '/index.html')) {
            $fp = fopen($publicRootPath . '/index.html', 'w');
            if ($fp) {
                fclose($fp);
            }
        }

        //folder share of admin
        $shareRootPath            = FILE_SHARING_MEDIA_PATH . '/Shared';

        if (!is_dir($shareRootPath)) {
            mkdir($shareRootPath, 0755);
            fs_new_file_database($shareRootPath, '', '', 'directory', 1);
        }

        if (!file_exists($shareRootPath . '/index.html')) {
            $fp = fopen($shareRootPath . '/index.html', 'w');
            if ($fp) {
                fclose($fp);
            }
        }



        if($cmd == 'rename'){
            if(($h == '\\' || $h == '/') || ($h == $user->media_path_slug || $h == '\\'.$user->media_path_slug || $h == '/'.$user->media_path_slug) || ($h == $user->media_path_slug.'\Shared' || $h == $user->media_path_slug.'/Shared' || $h == '\\'.$user->media_path_slug.'\Shared' || $h == '/'.$user->media_path_slug.'/Shared') || ($h == $user->media_path_slug.'\My Files' || $h == $user->media_path_slug.'/My Files' || $h == '\\'.$user->media_path_slug.'\My Files' || $h == '/'.$user->media_path_slug.'/My Files') || ($h == '\\' || $h == '/') || ($h == $user->media_path_slug.'\Client Files' || $h == $user->media_path_slug.'/Client Files' || $h == '\\'.$user->media_path_slug.'\Client Files' || $h == '/'.$user->media_path_slug.'/Client Files')){
                $public_root['defaults'] = [
                    'read'    => true,
                    'write'   => false,
                    'locked'  => true,
                    'hidden'  => false,
                ];
            }
        }else{
            if(($cmd == 'open' && $init == 1) || ($h == '\\' || $h == '/') || $cmd == 'parents'){
                $public_root['defaults'] = [
                    'read'    => true,
                    'write'   => true,
                    'locked'  => false,
                    'hidden'  => true,
                ];
            }
            if($is_public_admin || is_admin()){
                array_push($public_root['attributes'], [
                    'pattern' => '/Public/',
                    'read'    => $is_read,
                    'write'   => true,
                    'locked'  => false,
                    'hidden'  => false,
                ]);
            }else{
                array_push($public_root['attributes'], [
                    'pattern' => '/Public/',
                    'read'    => $is_read,
                    'write'   => false,
                    'locked'  => true,
                    'hidden'  => false,
                ]);
            }

            if(is_admin()){
                array_push($public_root['attributes'], [
                    'pattern' => '/Client Files/',
                    'read'    => $is_read,
                    'write'   => true,
                    'locked'  => false,
                    'hidden'  => false,
                ]);
            }

            if($cmd != 'mkdir'){
                array_push($public_root['attributes'], [
                    'pattern' => '!^/' . $user->media_path_slug . '$!',
                    'read'    => $is_read,
                    'write'   => true,
                    'locked'  => false,
                    'hidden'  => false,
                ]);



            }else{
                array_push($public_root['attributes'], [
                    'pattern' => '/' . $user->media_path_slug . '/',
                    'read'    => $is_read,
                    'write'   => true,
                    'locked'  => false,
                    'hidden'  => false,
                ]);
            }
        }

        

        if(!$is_delete || ($h == '\\' || $h == '/') || ($h == $user->media_path_slug || $h == '\\'.$user->media_path_slug || $h == '/'.$user->media_path_slug)){
           array_push($public_root['disabled'], 'rm'); 
        }


        if(!$is_upload || ($h == $user->media_path_slug.'\Shared' || $h == $user->media_path_slug.'/Shared' || $h == '\\'.$user->media_path_slug.'\Shared' || $h == '/'.$user->media_path_slug.'/Shared') || ($h == '\\' || $h == '/') || ($h == $user->media_path_slug || $h == '\\'.$user->media_path_slug || $h == '/'.$user->media_path_slug) || ($h == 'Client Files' || $h == '\Client Files' || $h == '/Client Files')){
            if($cmd != 'mkdir'){
                array_push($public_root['disabled'], 'mkdir'); 
            }
           array_push($public_root['disabled'], 'mkfile'); 
           array_push($public_root['disabled'], 'upload'); 
        }

        if(!$is_download){
           array_push($public_root['disabled'], 'download'); 
           array_push($public_root['disabled'], 'zipdl'); 
           array_push($public_root['disabled'], 'file'); 
        }

        if(get_option('fs_allow_file_editing') == 0){
            array_push($public_root['disabled'], 'edit'); 
        }


        if((!$is_share && !$is_share_to_client && !is_admin()) || ($h == '\\' || $h == '/') || ($h == $user->media_path_slug || $h == '\\'.$user->media_path_slug || $h == '/'.$user->media_path_slug) || ($h == $user->media_path_slug.'\Shared' || $h == $user->media_path_slug.'/Shared' || $h == '\\'.$user->media_path_slug.'\Shared' || $h == '/'.$user->media_path_slug.'/Shared') || ($h == '\\' || $h == '/') || ($h == $user->media_path_slug.'\Client Files' || $h == $user->media_path_slug.'/Client Files' || $h == '\\'.$user->media_path_slug.'\Client Files' || $h == '/'.$user->media_path_slug.'/Client Files') || (strpos($h, $user->media_path_slug) === false)){
           array_push($public_root['disabled'], 'share'); 
        }
           
        

        // if (strpos($h, $user->media_path_slug) === false && ($h != '\\' && $h != '/' && $h != '')) {
        //    array_push($public_root['disabled'], 'share');

        //    $file_sharing = $this->file_sharing_model->get_file_share('', true);

        //    foreach($file_sharing as $file){
        //         $h_2 = substr($file['hash'], 3);
        //         $h_2 = base64_decode(strtr($h_2, '-_.', '+/='));

        //         $_is_read   = $file['is_read'] == 1 ? true : false;
        //         $_is_upload  = $file['is_upload'] == 1 ? true : false;
        //         $_is_delete = $file['is_delete'] == 1 ? false : true;
        //         $_is_write = $file['is_write'] == 1 ? true : false;
        //         $_is_download = $file['is_download'] == 1 ? true : false;

        //         array_push($public_root['attributes'], [
        //                 'pattern' => '!^/'.$h_2.'!',
        //                 'read'    => $_is_read,
        //                 'write'   => $_is_write,
        //                 'locked'  => $_is_delete,
        //                 'hidden'  => false,
        //             ]);
        //    }
        // }

        $trash_option = array();
        if(!$disable_trash){
            // Trash volume
            $trash_option = array(
                'id'            => '1',
                'driver'        => 'Trash',
                'path'          => './'.FILE_SHARING_MEDIA_PATH.'/.trash/' . $user->media_path_slug.'/',
                'tmbURL'        => dirname($_SERVER['PHP_SELF']) . '/./'.FILE_SHARING_MEDIA_PATH . '/.trash/' . $user->media_path_slug . '/.tmb/',
                'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
                'uploadDeny'    => array('all'),                // Recomend the same settings as the original volume that uses the trash
                'uploadAllow'   => array('all'), // Same as above
                'uploadOrder'   => array('deny', 'allow'),      // Same as above
                'accessControl' => 'access',                    // Same as above
            );
        }


        $opts = [
            'roots' => [
                isset($public_root) ? $public_root : $root_options,
                $trash_option
            ],
        ];
        return $opts;
    }

    /**
     * new folder
     * @return json
     */
    public function new_folder()
    {
        $opts        = hooks()->apply_filters('before_init_media', $this->new_root_main());
        $elFinder    = new elFinder($opts);
        $hash        = $this->input->post('hash');
        $fileid      = $this->input->post('fileid');
        $type        = $this->input->post('type');
        $path        = $elFinder->realpath($hash);
        $path_upload = explode(FCPATH, $path);
        if ($path != '') {
            fs_new_file_database($path_upload[1], $hash, $fileid, $type, 0);
        }

        echo json_encode($path);
    }

    /**
     * add new share
     * @return redirect
     */
    public function add_new_share()
    {
        $data    = $this->input->post();
        $success = false;
        if ($data['id'] == '') {
            $id = $this->file_sharing_model->add_new_share($data);
            if (is_numeric($id)) {
                $success = true;
                $message = _l('added_successfully', _l('fs_share'));
                set_alert('success', $message);
            } else {
                $success = false;
                $message = _l('added_fail');
                set_alert('warning', $message);
            }
        } else {
            $res = $this->file_sharing_model->edit_new_share($data);
            if ($res == true) {
                $success = true;
                $message = _l('updated_successfully', _l('fs_share'));
                set_alert('success', $message);
            } else {
                $success = false;
                $message = _l('updated_fail');
                set_alert('warning', $message);
            }
        }
        echo json_encode(['success' => $success, 'message' => $message]);
    }

    /**
     * add new config
     * @return redirect
     */
    public function add_new_config()
    {
        $data = $this->input->post();
        if ($data['id'] == '') {
            $id = $this->file_sharing_model->add_new_config($data);
            if (is_numeric($id)) {
                $message = _l('added_successfully');
                set_alert('success', $message);
            } else {
                $message = _l('added_fail');
                set_alert('warning', $message);
            }
        } else {
            $res = $this->file_sharing_model->edit_new_config($data, $data['id']);
            if ($res == true) {
                $message = _l('updated_successfully');
                set_alert('success', $message);
            } else {
                $message = _l('updated_fail');
                set_alert('warning', $message);
            }
        }
        redirect(admin_url('file_sharing/setting?tab=configuration'));
    }

    /**
     * delete config
     * @param  integer $id
     * @return
     */
    public function delete_config($id)
    {
        $success = $this->file_sharing_model->delete_config($id);
        $message = '';
        if ($success) {
            $message = _l('deleted');
            set_alert('success', $message);
        } else {
            $message = _l('can_not_delete');
            set_alert('warning', $message);
        }
        redirect(admin_url('file_sharing/setting?tab=configuration'));
    }

    /**
     * update field
     * @param  int $id
     * @param  int $status
     * @param  string $name
     * @return json
     */
    public function update_field($id, $status, $name)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fs_setting_configuration', [$name => $status]);

        $message = _l('updated_successfully');
        echo json_encode(['success' => $message]);
    }

    /**
     * update field
     * @param  int $id
     * @param  int $status
     * @param  string $name
     * @return json
     */
    public function update_sharing_permission($id, $status, $name)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fs_sharings', [$name => $status]);

        $message = _l('updated_successfully');
        echo json_encode(['success' => $message]);
    }

    /**
     * update general setting
     */
    public function update_setting()
    {
        $data    = $this->input->post();
        $success = $this->file_sharing_model->update_setting($data);
        if ($success == true) {
            $message = _l('updated_successfully', _l('general_setting'));
            set_alert('success', $message);
        }
        redirect(admin_url('file_sharing/setting'));
    }

    /**
     * download management
     *  @return view
     */
    public function download_management()
    {
        if(!has_permission('file_sharing', '', 'view_download_management')){
            access_denied('file_sharing');
        }

        $data['title']      = _l('download_management');
        $data['staffs']     = $this->staff_model->get();
        $data['hash_share'] = $this->file_sharing_model->get_sharing_by_staff();

        $this->load->view('download_management/manage', $data);
    }

    /**
     * sharing management
     *  @return view
     */
    public function sharing()
    {
        if(!has_permission('file_sharing', '', 'view_sharing')){
            access_denied('file_sharing');
        }

        $data['title']         = _l('sharing');
        $data['hash_share']    = $this->file_sharing_model->get_sharing_by_staff();
        $data['staffs']        = $this->staff_model->get();
        $data['clients']       = $this->clients_model->get();
        $data['roles']         = $this->roles_model->get();
        $data['client_groups'] = $this->clients_model->get_groups();
        $this->load->view('sharing/manage', $data);
    }

    /**
     * download management table
     * @return json
     */
    public function download_management_table()
    {
        if ($this->input->is_ajax_request()) {

            $select = [
                db_prefix() . 'fs_downloads.hash_share as hash_share',
                'name',
                'size',
                'expiration_date',
                'download_limits',
                'time',
                'ip',
                'browser_name',
            ];
            $where = [];
            if (!is_admin()) {
                array_push($where, 'AND (' . db_prefix() . 'fs_downloads.hash_share in (select ' . db_prefix() . 'fs_sharings.hash_share from ' . db_prefix() . 'fs_sharings where created_at = ' . get_staff_user_id() . '))');
            }

            if ($this->input->post('member_filter')) {
                $staff_filter = $this->input->post('member_filter');
                array_push($where, 'AND created_at IN (' . implode(', ', $staff_filter) . ')');
            }

            if ($this->input->post('hash_share')) {
                $hash_share = $this->input->post('hash_share');
                array_push($where, 'AND ' . db_prefix() . 'fs_sharings.id IN (' . implode(', ', $hash_share) . ')');
            }

            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                if (!$this->file_sharing_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                if (!$this->file_sharing_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (time >= "' . $from_date . '" and time <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (time >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (time <= "' . $to_date . '")');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fs_downloads';
            $join         = ['JOIN ' . db_prefix() . 'fs_sharings ON ' . db_prefix() . 'fs_sharings.hash_share = ' . db_prefix() . 'fs_downloads.hash_share'];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['downloads']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = $aRow['hash_share'];
                $row[] = $aRow['name'];
                $bytes = $aRow['size'];
                if ($bytes >= 1073741824) {
                    $bytes = number_format($bytes / 1073741824, 2) . ' GB';
                } elseif ($bytes >= 1048576) {
                    $bytes = number_format($bytes / 1048576, 2) . ' MB';
                } elseif ($bytes >= 1024) {
                    $bytes = number_format($bytes / 1024, 2) . ' KB';
                } elseif ($bytes > 1) {
                    $bytes = $bytes . ' bytes';
                } elseif ($bytes == 1) {
                    $bytes = $bytes . ' byte';
                } else {
                    $bytes = '0 bytes';
                }
                $row[] = $bytes;

                $row[] = _d($aRow['expiration_date']);

                $downloads = '';
                if ($aRow['download_limits'] > 0) {
                    $downloads = $aRow['downloads'] . '/' . $aRow['download_limits'];
                }
                $row[] = $downloads;
                $row[] = '<span class="text text-success">' . $aRow['ip'] . '</span>';
                $row[] = '<span class="text text-success">' . $aRow['browser_name'] . '</span>';
                $row[] = '<span class="text text-success">' . _dt($aRow['time']) . '</span>';

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * sharing table
     * @return json
     */
    public function sharing_table()
    {
        if ($this->input->is_ajax_request()) {

            $select = [
                'id',
                'hash_share',
                'name',
                'expiration_date',
                'download_limits',
                'type',
                'created_at',
                'inserted_at',
                'updated_at',
                'has_been_deleted',
                'downloads',
                'password',
            ];
            $where = [];
            if (!is_admin()) {
                array_push($where, 'AND (created_at = ' . get_staff_user_id() . ')');
            }

            if ($this->input->post('member_filter')) {
                $staff_filter = $this->input->post('member_filter');
                array_push($where, 'AND created_at IN (' . implode(', ', $staff_filter) . ')');
            }

            if ($this->input->post('type')) {
                $types      = $this->input->post('type');
                $where_type = '';
                if ($types != '') {
                    foreach ($types as $key => $value) {
                        if ($where_type == '') {
                            $where_type .= 'type = "' . $value . '"';
                        } else {
                            $where_type .= ' or type = "' . $value . '"';
                        }
                    }
                }

                if ($where_type != '') {
                    array_push($where, 'AND (' . $where_type . ')');
                }
            }

            if ($this->input->post('password')) {
                $passwords      = $this->input->post('password');
                $where_password = '';

                foreach ($passwords as $key => $value) {
                    if ($value != '') {
                        if ($value == 'no_password') {
                            if ($where_password == '') {
                                $where_password .= 'password = ""';
                            } else {
                                $where_password .= ' or password = ""';
                            }
                        } else {
                            if ($where_password == '') {
                                $where_password .= 'password != ""';
                            } else {
                                $where_password .= ' or password != ""';
                            }
                        }

                    }
                }

                if ($where_password != '') {
                    array_push($where, 'AND (' . $where_password . ')');
                }
            }

            if ($this->input->post('status')) {
                $statuss      = $this->input->post('status');
                $where_status = '';
                if ($statuss != '') {
                    foreach ($statuss as $key => $value) {
                        if ($value == 'has_been_deleted') {
                            if ($where_status == '') {
                                $where_status .= 'has_been_deleted = 1';
                            } else {
                                $where_status .= ' or has_been_deleted = 1';
                            }
                        } elseif ($value == 'expiration_date') {
                            if ($where_status == '') {
                                $where_status .= '(expiration_date < "' . date('Y-m-d') . '" and has_been_deleted = 0)';
                            } else {
                                $where_status .= ' or (expiration_date < "' . date('Y-m-d') . '" and has_been_deleted = 0)';
                            }
                        } elseif ($value == 'download_limits') {
                            if ($where_status == '') {
                                $where_status .= '(download_limits <= downloads and expiration_date >= "' . date('Y-m-d') . '")';
                            } else {
                                $where_status .= ' or (download_limits <= downloads and expiration_date >= "' . date('Y-m-d') . '")';
                            }
                        } elseif ($value == 'normal') {
                            if ($where_status == '') {
                                $where_status .= '(IF(download_limits_apply = 1, download_limits > downloads, 1=1) and IF(expiration_date_apply = 1, expiration_date >= "' . date('Y-m-d') . '", 1=1) and has_been_deleted = 0)';
                            } else {
                                $where_status .= ' or (IF(download_limits_apply = 1, download_limits > downloads, 1=1) and IF(expiration_date_apply = 1, expiration_date >= "' . date('Y-m-d') . '", 1=1) and has_been_deleted = 0)';
                            }
                        }
                    }
                }

                if ($where_status != '') {
                    array_push($where, 'AND (' . $where_status . ')');
                }
            }

            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                if (!$this->file_sharing_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                if (!$this->file_sharing_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (date_format(inserted_at, \'%Y-%m-%d\') >= "' . $from_date . '" and date_format(inserted_at, \'%Y-%m-%d\') <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (date_format(inserted_at, \'%Y-%m-%d\') >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (date_format(inserted_at, \'%Y-%m-%d\') <= "' . $to_date . '")');
            }
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fs_sharings';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['expiration_date_apply', 'expiration_date', 'expiration_date_delete', 'download_limits_apply', 'download_limits', 'download_limits_delete', 'password', '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and type = "staff") as staffs', '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and type = "role") as roles', '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and type = "customer") as customers', '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and type = "customer_group") as customer_groups', 'is_read', 'is_write', 'is_delete', 'is_upload', 'is_download']);

            $output  = $result['output'];
            $rResult = $result['rResult'];
            $switch  = new switchclass;

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = $aRow['hash_share'];
                $row[] = $aRow['name'];

                $options    = '';
                $input_attr = 'disabled';
                if (is_admin() || $aRow['created_at'] == get_staff_user_id()) {
                    if($aRow['type'] == 'fs_public'){
                        $options .= icon_btn('#', 'fa fa-clone', 'btn-info mbot5', [
                            'title'   => _l('copy'),
                            'onclick' => 'copy_public_link_v2(\'' . $aRow['hash_share'] . '\'); return false;',
                        ]);
                    }

                    $options .= icon_btn('#', 'fa fa-edit', 'btn-default mbot5', [
                        'title'                       => _l('edit'),
                        'data-id'                     => $aRow['id'],
                        'data-is_read'                => $aRow['is_read'],
                        'data-is_write'               => $aRow['is_write'],
                        'data-is_delete'              => $aRow['is_delete'],
                        'data-is_upload'              => $aRow['is_upload'],
                        'data-is_download'            => $aRow['is_download'],
                        'data-expiration_date_apply'  => $aRow['expiration_date_apply'],
                        'data-expiration_date'        => _d($aRow['expiration_date']),
                        'data-expiration_date_delete' => $aRow['expiration_date_delete'],
                        'data-download_limits_apply'  => $aRow['download_limits_apply'],
                        'data-download_limits'        => $aRow['download_limits'],
                        'data-download_limits_delete' => $aRow['download_limits_delete'],
                        'data-staffs'                 => $aRow['staffs'],
                        'data-roles'                  => $aRow['roles'],
                        'data-customers'              => $aRow['customers'],
                        'data-customer_groups'        => $aRow['customer_groups'],
                        'data-hash_share'             => $aRow['hash_share'],
                        'data-type'                   => $aRow['type'],
                        'data-password'               => $this->file_sharing_model->AES_256_Decrypt($aRow['password']),
                        'onclick'                     => 'edit_sharing(this); return false;',
                    ]);

                    $options .= icon_btn('#', 'fa fa-remove', 'btn-danger mbot5', [
                        'title'   => _l('delete'),
                        'onclick' => 'delete_sharing(' . $aRow['id'] . '); return false;',
                    ]);
                    $input_attr = '';
                }

                $row[]           = _d($aRow['expiration_date']);
                $download_limits = '';
                if ($aRow['download_limits'] > 0) {
                    $download_limits = $aRow['download_limits'];
                }
                $row[] = $download_limits;
                $row[] = $aRow['downloads'];
                $row[] = $aRow['password'] != '' && $aRow['type'] == 'fs_public' ? _l('settings_yes') : _l('settings_no');

                $row[] = get_staff_full_name($aRow['created_at']);
                $type  = '';
                if ($aRow['type'] == 'fs_staff') {
                    $type = _l('staff');
                } else if ($aRow['type'] == 'fs_client') {
                    $type = _l('client');
                } else {
                    $type = _l('public');
                }
                $row[] = _l($aRow['type']);
                $row[] = _dt($aRow['inserted_at']);
                $row[] = _dt($aRow['updated_at']);

                $status_name = _l('normal');
                $label_class = 'success';

                if ($aRow['has_been_deleted'] == 1) {
                    $status_name = _l('file_has_been_deleted');
                    $label_class = 'danger';
                } else if ($aRow['expiration_date_apply'] == 1 && strtotime($aRow['expiration_date']) < strtotime(date('Y-m-d'))) {
                    $label_class = 'warning';
                    $status_name = _l('contracts_view_expired');
                } else if ($aRow['download_limits_apply'] == 1 && $aRow['download_limits'] > 0 && $aRow['download_limits'] <= $aRow['downloads']) {
                    $label_class = 'warning';
                    $status_name = _l('download_limit_reached');
                }
                $row[] = '<span class="text text-' . $label_class . '">' . $status_name . '</span>';

                $row[]              = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * sharing detail table
     * @return json
     */
    public function sharing_detail_table()
    {
        if ($this->input->is_ajax_request()) {

            $select = [
                'id',
                'hash_share',
                
                'expiration_date',
                'download_limits',
                'type',
                'created_at',
                'has_been_deleted',
                'downloads',
                'password',
            ];
            $where = [];
            if ($this->input->post('share_hash')) {
                $share_hash = $this->input->post('share_hash');
                array_push($where, 'AND hash = "' . $share_hash . '"');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'fs_sharings';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['expiration_date_apply', 'expiration_date', 'expiration_date_delete', 'download_limits_apply', 'download_limits', 'download_limits_delete', 'password', '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and type = "staff") as staffs', '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and type = "role") as roles', '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and type = "customer") as customers', '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and type = "customer_group") as customer_groups','is_read',
                'is_upload',
                'is_download',
                'is_delete',
                'is_write',]);

            $output  = $result['output'];
            $rResult = $result['rResult'];
            $switch  = new switchclass;

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = $aRow['hash_share'];

                $options    = '';
                $input_attr = 'disabled';
                if (is_admin() || $aRow['created_at'] == get_staff_user_id()) {
                    if($aRow['type'] == 'fs_public'){
                        $options .= icon_btn('#', 'fa fa-clone', 'btn-info mbot5', [
                            'title'   => _l('copy'),
                            'onclick' => 'copy_public_link_v2(\'' . $aRow['hash_share'] . '\'); return false;',
                        ]);
                    }

                    $options .= icon_btn('#', 'fa fa-edit', 'btn-default mbot5', [
                        'title'                       => _l('edit'),
                        'data-id'                     => $aRow['id'],
                        'data-is_read'                => $aRow['is_read'],
                        'data-is_write'               => $aRow['is_write'],
                        'data-is_delete'              => $aRow['is_delete'],
                        'data-is_upload'              => $aRow['is_upload'],
                        'data-is_download'            => $aRow['is_download'],
                        'data-expiration_date_apply'  => $aRow['expiration_date_apply'],
                        'data-expiration_date'        => _d($aRow['expiration_date']),
                        'data-expiration_date_delete' => $aRow['expiration_date_delete'],
                        'data-download_limits_apply'  => $aRow['download_limits_apply'],
                        'data-download_limits'        => $aRow['download_limits'],
                        'data-download_limits_delete' => $aRow['download_limits_delete'],
                        'data-staffs'                 => $aRow['staffs'],
                        'data-roles'                  => $aRow['roles'],
                        'data-customers'              => $aRow['customers'],
                        'data-customer_groups'        => $aRow['customer_groups'],
                        'data-hash_share'             => $aRow['hash_share'],
                        'data-type'                   => $aRow['type'],
                        'data-password'               => $this->file_sharing_model->AES_256_Decrypt($aRow['password']),
                        'onclick'                     => 'edit_sharing(this); return false;',
                    ]);

                    $options .= icon_btn('#', 'fa fa-remove', 'btn-danger mbot5', [
                        'title'   => _l('delete'),
                        'onclick' => 'delete_sharing(' . $aRow['id'] . '); return false;',
                    ]);
                    $input_attr = '';
                }

                $row[]           = _d($aRow['expiration_date']);
                $download_limits = '';
                if ($aRow['download_limits'] > 0) {
                    $download_limits = $aRow['download_limits'];
                }
                $row[] = $download_limits;
                $row[] = $aRow['downloads'];
                $row[] = $aRow['password'] != '' && $aRow['type'] == 'fs_public' ? _l('settings_yes') : _l('settings_no');

                $row[] = get_staff_full_name($aRow['created_at']);
                $type  = '';
                if ($aRow['type'] == 'fs_staff') {
                    $type = _l('staff');
                } else if ($aRow['type'] == 'fs_client') {
                    $type = _l('client');
                } else {
                    $type = _l('public');
                }
                $row[] = _l($aRow['type']);

                $status_name = _l('normal');
                $label_class = 'success';

                if ($aRow['has_been_deleted'] == 1) {
                    $status_name = _l('file_has_been_deleted');
                    $label_class = 'danger';
                } else if ($aRow['expiration_date_apply'] == 1 && strtotime($aRow['expiration_date']) < strtotime(date('Y-m-d'))) {
                    $label_class = 'warning';
                    $status_name = _l('contracts_view_expired');
                } else if ($aRow['download_limits_apply'] == 1 && $aRow['download_limits'] > 0 && $aRow['download_limits'] <= $aRow['downloads']) {
                    $label_class = 'warning';
                    $status_name = _l('download_limit_reached');
                }
                $row[] = '<span class="text text-' . $label_class . '">' . $status_name . '</span>';

                $row[]              = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * affiliate reports
     *
     * @return view
     */
    public function reports()
    {
        if(!has_permission('file_sharing', '', 'view_reports')){
            access_denied('file_sharing');
        }

        $data['title']         = _l('reports');
        $data['staffs']        = $this->staff_model->get();
        $data['clients']       = $this->clients_model->get();
        $data['roles']         = $this->roles_model->get();
        $data['client_groups'] = $this->clients_model->get_groups();
        $data['hash_share']    = $this->file_sharing_model->get_sharing_by_staff();
        $this->load->view('reports/manage_report', $data);
    }

    /**
     * update field
     * @param  int $id
     * @param  int $status
     * @param  string $name
     * @return json
     */
    public function edit_sharing()
    {
        $data = $this->input->post();
        if (isset($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            $success = $this->file_sharing_model->update_sharing($data, $id);
            if ($success) {
                $message = _l('updated_successfully');
            } else {
                $message = _l('updated_fail');
            }

            echo json_encode(['success' => $success, 'message' => $message]);
        }
    }

    /**
     * delete sharing
     * @param  integer $id
     * @return json
     */
    public function delete_sharing($id)
    {
        $success = $this->file_sharing_model->delete_sharing($id);

        $message = _l('problem_deleting', _l('sharing'));

        if ($success) {
            $message = _l('deleted', _l('sharing'));
        }

        echo json_encode(['success' => $success, 'message' => $message]);
    }

    /**
     * get data sharing chart
     *
     * @return     json
     */
    public function sharing_chart()
    {
        $this->load->model('currencies_model');

        $filter = $this->input->post();

        $data = $this->file_sharing_model->sharing_chart($filter);

        echo json_encode([
            'data'  => $data['data'],
            'month' => $data['month'],
        ]);
        die();
    }

    /**
     * get data download chart
     *
     * @return     json
     */
    public function download_chart()
    {
        $this->load->model('currencies_model');
        $filter = $this->input->post();

        $data = $this->file_sharing_model->download_chart($filter);

        echo json_encode([
            'data'  => $data['data'],
            'month' => $data['month'],
        ]);
        die();
    }

    /**
     * send mail to public
     * @return json
     */
    public function send_mail_to_public(){
        $data = $this->input->post();

        $success = $this->file_sharing_model->send_mail_to_public($data);
        if ($success == true) {
            $message = _l('successful_mailing');
        } else {
            $message = _l('mailing_failed');
        }

        echo json_encode(['success' => $success, 'message' => $message]);
    }

    public function get_hash_share(){
        $hash_share = $this->file_sharing_model->generate_hash();

        echo json_encode($hash_share);
    }
}
