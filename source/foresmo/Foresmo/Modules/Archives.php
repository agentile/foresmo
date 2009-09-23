<?php
/**
 * Foresmo_Modules_Archives
 *
 *
 */
class Foresmo_Modules_Archives extends Solar_Base {

    protected $_Foresmo_Modules_Archives = array('model' => null);
    protected $_model;
    protected $_name = 'Archives';
    protected $_view;
    protected $_view_path;
    protected $_view_file;

    public $output = '';

    /**
     * _postConstruct
     *
     * @param $model
     */
    protected function _postConstruct()
    {
        parent::_postConstruct();
        $this->_model = $this->_config['model'];
        $this->_view_path = Solar_Class::dir($this, 'View');
        $this->_view_file = 'index.php';
        $this->_view = Solar::factory('Solar_View', array('template_path' => $this->_view_path));
    }

    /**
     * start
     * Begin Module Work
     *
     * @return void
     */
    public function start()
    {
        $posts = $this->_model->posts->getPublishedPosts();
        $blog_start = $this->_model->options->getOptionValue('blog_installed');
        $blog_start = explode('/', date('n/j/Y', $blog_start));
        $current = getdate();

        $archive = $this->getArchiveArray($blog_start[0], $blog_start[2], $current['mon'], $current['year']);

        foreach ($posts as $key => $post) {
            $date = explode('/', date('n/j/Y', $post['pubdate_ts']));
            $m = (int) $date[0];
            $y = (int) $date[2];
            $archive[$y][$m] += 1;
        }

        $months_of_year = array(
            1 => array('full' => 'January', 'short' => 'Jan'),
            2 => array('full' => 'February', 'short' => 'Feb'),
            3 => array('full' => 'March', 'short' => 'Mar'),
            4 => array('full' => 'April', 'short' => 'Apr'),
            5 => array('full' => 'May', 'short' => 'May'),
            6 => array('full' => 'June', 'short' => 'Jun'),
            7 => array('full' => 'July', 'short' => 'Jul'),
            8 => array('full' => 'August', 'short' => 'Aug'),
            9 => array('full' => 'September', 'short' => 'Sep'),
            10 => array('full' => 'October', 'short' => 'Oct'),
            11 => array('full' => 'November', 'short' => 'Nov'),
            12 => array('full' => 'December', 'short' => 'Dec'),
        );

        $this->_view->assign('archive', $archive);
        $this->_view->assign('months_of_year', $months_of_year);
        $this->output = $this->_view->fetch($this->_view_file);
    }

    protected function getArchiveArray($start_mon, $start_year, $end_mon, $end_year)
    {
        $start_mon = (int) $start_mon;
        $start_year = (int) $start_year;
        $end_mon = (int) $end_mon;
        $end_year = (int) $end_year;

        $arr = array();
        for ($y = $end_year; $y >= $start_year; $y--) {
            $max_mon = ($y == $end_year) ? $end_mon : 12;
            $min_mon = ($y == $start_year) ? $start_mon : 1;
            for ($m = $max_mon; $m >= $min_mon; $m--) {
                $arr[$y][$m] = 0;
            }
        }
        return $arr;
    }
}

