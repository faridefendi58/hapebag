<?php
namespace Extensions;

class HandphoneService
{
    protected $basePath;
    protected $themeName;
    protected $adminPath;
    protected $tablePrefix;

    public function __construct($settings = null)
    {
        $this->basePath = (is_object($settings))? $settings['basePath'] : $settings['settings']['basePath'];
        $this->themeName = (is_object($settings))? $settings['theme']['name'] : $settings['settings']['theme']['name'];
        $this->adminPath = (is_object($settings))? $settings['admin']['path'] : $settings['settings']['admin']['path'];
        $this->tablePrefix = (is_object($settings))? $settings['db']['tablePrefix'] : $settings['settings']['db']['tablePrefix'];
    }
    
    public function install()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{tablePrefix}ext_handphone_brand` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(128) NOT NULL,
          `description` text,
          `created_at` datetime NOT NULL,
          `created_by` int(11) DEFAULT '0',
          `updated_at` datetime DEFAULT NULL,
          `updated_by` int(11) DEFAULT '0',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

        $sql .= "CREATE TABLE IF NOT EXISTS `{tablePrefix}ext_handphone_series` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(128) NOT NULL,
          `slug` varchar(128) DEFAULT NULL,
          `meta_description` text,
          `description` text,
          `brand_id` int(11) DEFAULT '0',
          `created_at` datetime NOT NULL,
          `created_by` int(11) DEFAULT '0',
          `updated_at` datetime DEFAULT NULL,
          `updated_by` int(11) DEFAULT '0',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

        $sql .= "CREATE TABLE IF NOT EXISTS `{tablePrefix}ext_handphone_images` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `series_id` int(11) NOT NULL,
          `type` varchar(32) NOT NULL DEFAULT 'thumbnail' COMMENT 'open_graft, thumbnail',
          `upload_folder` varchar(256) DEFAULT NULL,
          `file_name` varchar(128) DEFAULT NULL,
          `alt` varchar(128) DEFAULT NULL,
          `description` text,
          `created_at` datetime NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

        $sql = str_replace(['{tablePrefix}'], [$this->tablePrefix], $sql);
        
        $model = new \Model\OptionsModel();
        $install = $model->installExt($sql);

        return $install;
    }

    public function uninstall()
    {
        return true;
    }

    /**
     * Handphone extension available menu
     * @return array
     */
    public function getMenu()
    {
        return [
            [ 'label' => 'Daftar Brand', 'url' => 'handphone/brands/view', 'icon' => 'fa fa-search' ],
            [ 'label' => 'Tambah Brand Baru', 'url' => 'handphone/brands/create', 'icon' => 'fa fa-plus' ],
            [ 'label' => 'Daftar Series/Product', 'url' => 'handphone/series/view', 'icon' => 'fa fa-search' ],
            [ 'label' => 'Tambah Series/Product', 'url' => 'handphone/series/create', 'icon' => 'fa fa-plus' ],
            [ 'label' => 'Master Specs', 'url' => 'handphone/specs/view', 'icon' => 'fa fa-search' ],
            [ 'label' => 'Tambah Master Specs', 'url' => 'handphone/specs/create', 'icon' => 'fa fa-plus' ],
        ];
    }
}
