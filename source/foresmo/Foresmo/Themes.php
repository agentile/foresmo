<?php
/**
 * Foresmo_Themes
 *
 *
 *
 */
class Foresmo_Themes extends Solar_Base {

    protected $_Foresmo_Themes = array('model' => null);
    protected $_model;
    public $web_root;

    /**
     * _postConstruct
     *
     * @param $model
     */
    protected function _postConstruct()
    {
        parent::_postConstruct();
        $this->_model = $this->_config['model'];
        $this->web_root = (isset($_SERVER['DOCUMENT_ROOT'])) ? $_SERVER['DOCUMENT_ROOT'] : Solar::$system . '/docroot/';
        if (substr($this->web_root, -1) != '/') {
            $this->web_root = $this->web_root . '/';
        }
    }

    /**
     * scanForThemes
     *
     */
    public function scanForThemes()
    {
        $invisible = array('.', '..', '.htaccess', '.htpasswd', '.svn');
        $exts = array('.php', '.php4', '.php5', '.phps', '.inc');
        $dirs = array();
        $dirs['main'] = Solar::$system . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR;
        $dirs['admin'] = Solar::$system . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR;
        $themes = array();
        foreach ($dirs as $type => $dir) {
            $dir_content = scandir($dir);
            foreach ($dir_content as $key => $content) {
                $path = $dir . $content;
                if (!in_array($content, $invisible)) {
                    if (is_dir($path) && is_readable($path)) {
                        $theme_info = $path . DIRECTORY_SEPARATOR . 'info.php';
                        $default = array(
                            'name' => $content,
                            'folder' => $content,
                            'description' => '',
                            'version' => 'N/A',
                            'author' => 'N/A',
                            'preview' => null,
                            'type' => (array) $type,
                        );

                        if (file_exists($theme_info)) {
                            $arr = include $theme_info;
                            $arr = array_merge($default, $arr);
                            $arr['type'] = (array) $arr['type'];
                            $themes[] = $arr;
                        } else {
                            $themes[] = $default;
                        }
                        // make assets symlink if exists
                        $src = $path . DIRECTORY_SEPARATOR . 'assets';
                        if (is_dir($src)) {
                            $d = $this->web_root . 'public' . DIRECTORY_SEPARATOR . 'Foresmo' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $type;
                            $tgt = $d . DIRECTORY_SEPARATOR . $content;
                            if (!file_exists($tgt)) {
                                Solar_Symlink::make($src, $tgt);
                            }
                        }
                    }
                }
            }
        }
        return $themes;
    }
}
