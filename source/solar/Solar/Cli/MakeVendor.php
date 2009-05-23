<?php
/**
 * 
 * Solar command to make a Vendor directory set with symlinks to the right
 * places.
 * 
 * @category Solar
 * 
 * @package Solar_Cli
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: MakeVendor.php 3741 2009-05-15 01:34:31Z pmjones $
 * 
 * @todo Make Vendor_App_Hello, Vendor_Cli_Help.  Also make Vendor_App_Base
 * and Vendor_Cli_Base?
 * 
 */
class Solar_Cli_MakeVendor extends Solar_Cli_Base
{
    /**
     * 
     * The "StudlyCaps" version of the vendor name.
     * 
     * @var string
     * 
     */
    protected $_studly = null;
    
    /**
     * 
     * The "lowercase-dashes" version of the vendor name.
     * 
     * @var string
     * 
     */
    protected $_dashes = null;
    
    /**
     * 
     * The various "source/" dirs to create.
     * 
     * @var array
     * 
     */
    protected $_dirs = array(
        '/{:dashes}/script',
        '/{:dashes}/docs',
        '/{:dashes}/tests',
        '/{:dashes}/{:studly}/App',
        '/{:dashes}/{:studly}/App/Public',
        '/{:dashes}/{:studly}/App/Public/images',
        '/{:dashes}/{:studly}/App/Public/scripts',
        '/{:dashes}/{:studly}/App/Public/styles',
        '/{:dashes}/{:studly}/Model',
        '/{:dashes}/{:studly}/Locale',
        '/{:dashes}/{:studly}/View',
        '/{:dashes}/{:studly}/View/Helper',
    );
    
    /**
     * 
     * The registered Solar_Inflect instance.
     * 
     * @var Solar_Inflect
     * 
     */
    protected $_inflect;
    
    /**
     * 
     * Write out a series of dirs and symlinks for a new Vendor source.
     * 
     * @param string $vendor The Vendor name.
     * 
     * @return void
     * 
     */
    protected function _exec($vendor = null)
    {
        // we need a vendor name, at least
        if (! $vendor) {
            throw $this->_exception('ERR_NO_VENDOR_NAME');
        }
        
        // build "foo-bar" and "FooBar" versions of the vendor name.
        $this->_inflect = Solar_Registry::get('inflect');
        $this->_dashes  = $this->_inflect->camelToDashes($vendor);
        $this->_studly  = $this->_inflect->dashesToStudly($this->_dashes);
        
        // create dirs and symlinks
        $this->_createDirs();
        $this->_createLinks();
        
        // done!
        $done = "Done! Remember to add your new {$this->_studly}_App class "
              . "prefix to the ['Solar_Controller_Front']['classes'] element "
              . "in your config file.";

        $this->_outln($done);
    }
    
    /**
     * 
     * Creates the "source/" directories for the vendor.
     * 
     * @return void
     * 
     */
    protected function _createDirs()
    {
        $this->_outln('Making vendor source directories.');
        
        $system = Solar::$system;
        foreach ($this->_dirs as $dir) {
            
            $dir = "$system/source" . str_replace(
                array('{:dashes}', '{:studly}'),
                array($this->_dashes, $this->_studly),
                $dir
            );

            if (is_dir($dir)) {
                $this->_outln("Directory $dir exists.");
            } else {
                $this->_outln("Creating $dir.");
                mkdir($dir, 0755, true);
            }
        }
    }
    
    /**
     * 
     * Creates the various symlinks for the vendor directories.
     * 
     * @return void
     * 
     */
    protected function _createLinks()
    {
        $this->_outln('Making symlinks.');
        
        $system = Solar::$system;
        
        $links = array(
            
            // include/Vendor -> ../source/vendor/Vendor
            array(
                'dir' => "$system/include",
                'tgt' => $this->_studly,
                'src' => "../source/{$this->_dashes}/$this->_studly",
            ),
            
            // docroot/public/Vendor -> ../../include/Vendor/App/Public
            array(
                'dir' => "$system/docroot/public",
                'tgt' => $this->_studly,
                'src' => "../../include/{$this->_studly}/App/Public",
            ),
            
            // script/vendor -> ../source/solar/bin/solar
            array(
                'dir' => "$system/script",
                'tgt' => $this->_dashes,
                'src' => "../source/solar/script/solar",
            ),
            
        );
        
        foreach ($links as $link) {
            extract($link); // $dir, $src, $tgt
            $cmd = "cd $dir; ln -s $src $tgt";
            $this->_outln($cmd);
            passthru($cmd);
        }
    }
}
