<?php
/**
 * 
 * Helper to build an escaped href or src attribute value for an action URI.
 * 
 * @category Solar
 * 
 * @package Solar_View
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: ActionHref.php 3988 2009-09-04 13:51:51Z pmjones $
 * 
 */
class Solar_View_Helper_ActionHref extends Solar_View_Helper
{
    /**
     * 
     * Internal URI object for creating links.
     * 
     * @var Solar_Uri_Action
     * 
     */
    protected $_uri = null;
    
    /**
     * 
     * Post-construction tasks to complete object construction.
     * 
     * @return void
     * 
     */
    protected function _postConstruct()
    {
        parent::_postConstruct();
        $this->_uri = Solar::factory('Solar_Uri_Action');
    }
    
    /**
     * 
     * Returns an escaped href or src attribute value for an action URI.
     * 
     * @param Solar_Uri_Action|string $spec The href or src specification.
     * 
     * @return string
     * 
     */
    public function actionHref($spec)
    {
        if ($spec instanceof Solar_Uri_Action) {
            // already an action uri object
            $href = $spec->get();
        } else {
            // build-and-fetch the string as an action spec
            $href = $this->_uri->quick($spec);
        }
        
        return $this->_view->escape($href);
    }
}