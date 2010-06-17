<?php
/**
 *
 * Model class.
 *
 */
class Foresmo_Model_ModuleInfo extends Solar_Sql_Model {

    /**
     *
     * Model-specific setup.
     *
     * @return void
     *
     */
    protected function _setup()
    {
        $dir = str_replace('_', DIRECTORY_SEPARATOR, __CLASS__)
             . DIRECTORY_SEPARATOR
             . 'Setup'
             . DIRECTORY_SEPARATOR;

        $this->_table_name = $this->_config['prefix'] . Solar_File::load($dir . 'table_name.php');
        $this->_table_cols = Solar_File::load($dir . 'table_cols.php');
    }

    /**
     * insertModuleEntry
     * Insert description here
     *
     * @param $id
     * @param $data
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function insertModuleEntry($id, $data)
    {
        if (!isset($data['name']) || !isset($data['value'])) {
            return false;
        }

        if (isset($data['type'])) {
            $type = $data['type'];
        } else {
            $type = 0;
        }
        $arr = array(
            'module_id' => (int) $id,
            'name'      => $data['name'],
            'type'      => $type,
            'value'     => $data['value'],
        );
        $this->insert($arr);
    }

    /**
     * updateModuleEntryById
     * Insert description here
     *
     * @param $id
     * @param $module_id
     * @param $data
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function updateModuleEntryById($id, $module_id, $data)
    {
        if (!isset($data['name']) || !isset($data['value'])) {
            return false;
        }

        if (isset($data['type'])) {
            $type = $data['type'];
        } else {
            $type = 0;
        }
        $arr = array(
            'name'      => $data['name'],
            'type'      => $type,
            'value'     => $data['value'],
        );

        $where = array(
            'module_id = ?' => (int) $module_id,
            'id = ?' => (int) $id
        );
        $this->update($arr, $where);
    }
}
