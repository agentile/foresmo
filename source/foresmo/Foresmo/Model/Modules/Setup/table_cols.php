<?php
return array (
  'id' =>
  array (
    'name' => 'id',
    'type' => 'int',
    'size' => NULL,
    'scope' => NULL,
    'default' => NULL,
    'require' => true,
    'primary' => true,
    'autoinc' => true,
  ),
  'name' =>
  array (
    'name' => 'name',
    'type' => 'varchar',
    'size' => 255,
    'scope' => NULL,
    'default' => NULL,
    'require' => true,
    'primary' => false,
    'autoinc' => false,
  ),
  'enabled' =>
  array (
    'name' => 'type',
    'type' => 'smallint',
    'size' => NULL,
    'scope' => NULL,
    'default' => '0',
    'require' => true,
    'primary' => false,
    'autoinc' => false,
  ),
);
