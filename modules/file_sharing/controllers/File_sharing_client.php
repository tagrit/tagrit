<?php

defined('BASEPATH') or exit('No direct script access allowed');

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
foreach ($map as $key => $value) {
  include_once($value);
}

/**
 * Commission client Controller
 */
class File_sharing_client extends ClientsController
{
    public function index()
    {
        if (is_client_logged_in()) {
            $this->load->model('file_sharing_model');
            $this->load->helper('url');
            $data['title']     = _l('file_sharing');
            $data['connector'] = site_url() . 'file_sharing/file_sharing_client/file_sharing_media_connector';
            $client_language = get_client_default_language(get_client_user_id());
            $data['client_default_language'] = $this->file_sharing_model->getByLanguage($client_language);
            
            $this->data($data);

            $this->view('client/elfinder');
            $this->layout();

        } else {
            redirect(site_url());
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
   * new root main
   * @return $opts 
   */
  public function new_root_main(){
    $cmd = $this->input->get('cmd');
    $init = $this->input->get('init');
    $target = $this->input->get('target');
    $h = substr($target, 3);
    $h = base64_decode(strtr($h, '-_.', '+/='));

    $media_folder = FILE_SHARING_FOLDER_NAME;
    $mediaPath    = FILE_SHARING_MEDIA_PATH;
    if(($cmd == 'open' && $init == 1) || ($cmd == 'tree') || ($cmd == 'parents')){
      $mediaPath      = FILE_SHARING_MEDIA_PATH.'/Shared';
    }else{
      $mediaPath      = FILE_SHARING_MEDIA_PATH;
    }

    if (!is_dir($mediaPath)) {
      mkdir($mediaPath, 0755);
      fs_new_file_database($mediaPath,'','','directory', 1);
    }

    if (!file_exists($mediaPath . '/index.html')) {
      $fp = fopen($mediaPath . '/index.html', 'w');
      if ($fp) {
        fclose($fp);
      }
    }

    $this->load->helper('path');
    $this->load->model('file_sharing_model');

    $fs_global_extension = $this->file_sharing_model->get_global_extension();

    $root_options = [
      'driver' => 'LocalFileSystem',
      'path'   => file_sharing_set_realpath($mediaPath),
      'URL'    => site_url('modules/file_sharing/uploads/'.FILE_SHARING_FOLDER_NAME),
      'trashHash'     => 't1_Lw',                     // elFinder's hash of trash folder
      'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
      'uploadMaxSize' => get_option('fs_global_max_size') . 'M',
      'accessControl' => 'access_control_media',
      'uploadDeny'    => [
        'all'
      ],
      'uploadAllow' => $fs_global_extension,
      'uploadOrder' => [
        'deny',
        'allow',
      ],
      'disabled' => ['netmount','help'],
      'attributes' => [
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


    $config = $this->file_sharing_model->get_client_config(get_client_user_id());
    if($config){

      $is_read = $config->is_read == 1 ? true : false;
      $is_write = $config->is_write == 1 ? true : false;
      $is_delete = $config->is_delete == 1 ? true : false;
      $is_upload  = $config->is_upload == 1 ? true : false;
      $is_download = $config->is_download == 1 ? true : false;
    }else{
      $is_read = get_option('fs_permisstion_client_view') == 1 ? true : false;
      $is_write = get_option('fs_permisstion_client_write') == 1 ? true : false;
      $is_delete = get_option('fs_permisstion_client_delete') == 1 ? true : false;
      $is_upload = get_option('fs_permisstion_client_upload') == 1 ? true : false;
      $is_download = get_option('fs_permisstion_client_download') == 1 ? true : false;
    }
      
      $public_root         = $root_options;
      $publicRootPath      = FILE_SHARING_MEDIA_PATH;
      $public_root['path'] = file_sharing_set_realpath($publicRootPath);
      $public_root['URL'] = site_url('modules/file_sharing/uploads/'.FILE_SHARING_FOLDER_NAME);

      if (!is_dir($publicRootPath)) {
        mkdir($publicRootPath, 0755);
        fs_new_file_database($publicRootPath,'','','directory', 1);
      }

      if (!file_exists($publicRootPath . '/index.html')) {
        $fp = fopen($publicRootPath . '/index.html', 'w');
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

      $clientPath = FILE_SHARING_MEDIA_PATH . '/Client Files/' . str_pad(get_client_user_id(), 5, '0', STR_PAD_LEFT);
      $path      = file_sharing_set_realpath($clientPath);
     
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

      //folder share of staff
      $sharePath          = FILE_SHARING_MEDIA_PATH . '/Shared';

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
        
      //folder share of staff
      $trashPath          = FILE_SHARING_MEDIA_PATH . '/.trash/' . str_pad(get_client_user_id(), 5, '0', STR_PAD_LEFT);

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



      if(($cmd == 'open' && $init == 1) || ($h == '\\' || $h == '/') || $cmd == 'parents'){
        $public_root['defaults'] = [
                  'read'    => true,
                  'write'   => true,
                  'locked'  => false,
                  'hidden'  => true,
                ];
      }
      
      if(!$is_delete || ($h == '\\' || $h == '/') || ($h == 'Shared' || $h == '\\Shared' || $h == '/Shared') || ($h == 'Client Files' || $h == '\\Client Files' || $h == '/Client Files')){
        array_push($public_root['disabled'], 'rm'); 
      }

      if(!$is_upload || ($h == '\\' || $h == '/') || ($h == 'Shared' || $h == '\\Shared' || $h == '/Shared') || ($h == 'Client Files' || $h == '\\Client Files' || $h == '/Client Files')){
        array_push($public_root['disabled'], 'mkdir'); 
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

      if($cmd == 'rename'){
          if(($h == '\\' || $h == '/') || ($h == 'Shared' || $h == '\\Shared' || $h == '/Shared') || ($h == 'Client Files' || $h == '\\Client Files' || $h == '/Client Files') || ($h == 'Client Files/'. str_pad(get_client_user_id(), 5, '0', STR_PAD_LEFT) || $h == 'Client Files\\'. str_pad(get_client_user_id(), 5, '0', STR_PAD_LEFT) || $h == '\\Client Files\\'. str_pad(get_client_user_id(), 5, '0', STR_PAD_LEFT) || $h == '/Client Files/'. str_pad(get_client_user_id(), 5, '0', STR_PAD_LEFT))){
              $public_root['defaults'] = [
                  'read'    => true,
                  'write'   => false,
                  'locked'  => true,
                  'hidden'  => false,
              ];
          }
      }else{
        array_push($public_root['attributes'], [
              'pattern' => '/Client Files/',
              'read'    => $is_read,
              'write'   => true,
              'locked'  => false,
              'hidden'  => false,
          ]);

        array_push($public_root['attributes'], [
              'pattern' => '/Shared/',
              'read'    => $is_read,
              'write'   => true,
              'locked'  => false,
              'hidden'  => false,
          ]);
      }

    $opts = [
      'roots' => [
        isset($public_root) ? $public_root : $root_options,
        // Trash volume
        array(
            'id'            => '1',
            'driver'        => 'Trash',
            'path'          => './'.FILE_SHARING_MEDIA_PATH . '/.trash/' . str_pad(get_client_user_id(), 5, '0', STR_PAD_LEFT) . '/',
            'tmbURL'        => dirname($_SERVER['PHP_SELF']) . '/./'.FILE_SHARING_MEDIA_PATH . '/.trash/' . str_pad(get_client_user_id(), 5, '0', STR_PAD_LEFT) . '/.tmb/',
            'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
            'uploadDeny'    => array('all'),                // Recomend the same settings as the original volume that uses the trash
            'uploadAllow'   => array('all'), // Same as above
            'uploadOrder'   => array('deny', 'allow'),      // Same as above
            'accessControl' => 'access',                    // Same as above
        ),
      ],
    ];

    return $opts;
  }
}
