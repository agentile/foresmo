<?php
/**
 * Foresmo_Modules_Calendar
 *
 *
 */
class Foresmo_Modules_Calendar extends Solar_Base {

    protected $_Foresmo_Modules_Calendar = array('model' => null);
    protected $_model;
    protected $_name = 'Calendar';
    protected $_view;
    protected $_view_path;
    protected $_view_file;
    protected $_module_info = array();

    public $output = '';
    public $posts;

    /**
     * _postConstruct
     *
     * @param $model
     */
    protected function _postConstruct()
    {
        parent::_postConstruct();
        $this->_model = $this->_config['model'];
        $this->_view_path = Solar_Config::get('Solar', 'web_root') . 'modules/' . $this->_name . '/View';
        $this->_view_file = 'index.php';
        $this->_view = Solar::factory('Solar_View', array('template_path' => $this->_view_path));
        $this->_module_info = $this->_model->modules->fetchModuleInfoByName($this->_name);
    }

    /**
     * request
     * module request
     *
     * @param array $data
     * @return void
     */
    public function request($data)
    {
        $month = (isset($data['PARAMS'][0])) ? (int) $data['PARAMS'][0] : null;
        $year = (isset($data['PARAMS'][1])) ? (int) $data['PARAMS'][1] : null;
        if ($month < 1 || $month > 12) {
            $month = null;
        }
        $this->start($month, $year);
    }

    /**
     * start
     * Begin Module Work
     *
     * @return void
     */
    public function start($month = null, $year = null)
    {
        $current_time_info = getdate();
        if (is_null($month) || !is_int($month)) {
            $month = $current_time_info['mon'];
        }

        if (is_null($year) || !is_int($year)) {
            $year = $current_time_info['year'];
        }

        $start_day = 0;

        if (isset($this->_module_info[0]['moduleinfo'])) {
            foreach ($this->_module_info[0]['moduleinfo'] as $row) {
                if ($row['name'] == 'start_of_week') {
                    $start_day = (int) $row['value'];
                }
            }
        }

        $days_of_week = array(
            array('full' => 'Sunday', 'short' => 'Su'),
            array('full' => 'Monday', 'short' => 'M'),
            array('full' => 'Tuesday', 'short' => 'T'),
            array('full' => 'Wednesday', 'short' => 'W'),
            array('full' => 'Thursday', 'short' => 'Th'),
            array('full' => 'Friday', 'short' => 'F'),
            array('full' => 'Saturday', 'short' => 'Sa'),
        );

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
        $this->_view->assign('calendar', $this->getCalendar($month, $year));
        $this->_view->assign('start_day', $start_day);
        $this->_view->assign('days_of_week', $days_of_week);
        $this->_view->assign('months_of_year', $months_of_year);
        $this->_view->assign('posts', $this->posts);
        $this->output = $this->_view->fetch($this->_view_file);
    }

    /**
     * getCalendar
     *
     * Get calendar info
     *
     * @return array
     */
    public function getCalendar($month = null, $year = null)
    {
        $current_time_info = getdate();
        if (is_null($month) || !is_int($month)) {
            $month = $current_time_info['mon'];
        }

        if (is_null($year) || !is_int($year)) {
            $year = $current_time_info['year'];
        }
        // First of month is always 1, we need what day it falls on
        $ts = gmmktime(0, 0, 0, $month, 1, $year);
        $first_of_month = gmstrftime("%w", $ts);
        $month_string = gmstrftime("%B", $ts);
        if ($month == 12) {
            $next_month = 1;
            $next_year = $year + 1;
        } else {
            $next_month = $month + 1;
            $next_year = $year;
        }
        // Get the numeric last day of month.
        $last_of_month = gmstrftime("%d", gmmktime(0, 0, 0, $next_month, 0, $next_year));

        // Get posts for requested month/year
        $this->posts = $this->_model->posts->fetchPublishedPostsByDate($year, $month);

        return array(
            'first_day' => (int) $first_of_month,
            'last_day' => (int) $last_of_month,
            'month' => $month,
            'month_text' => $month_string,
            'year' => $year,
            'today' => $current_time_info['mday'],
        );
    }
}
