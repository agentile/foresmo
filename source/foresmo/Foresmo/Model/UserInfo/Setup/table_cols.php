<?php
return array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'smallint',
    'size' => NULL,
    'scope' => NULL,
    'default' => NULL,
    'require' => true,
    'primary' => true,
    'autoinc' => true,
  ),
  'user_id' => 
  array (
    'name' => 'user_id',
    'type' => 'smallint',
    'size' => NULL,
    'scope' => NULL,
    'default' => NULL,
    'require' => true,
    'primary' => false,
    'autoinc' => false,
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
  'type' => 
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
  'value' => 
  array (
    'name' => 'value',
    'type' => 'text',
    'size' => NULL,
    'scope' => NULL,
    'default' => NULL,
    'require' => false,
    'primary' => false,
    'autoinc' => false,
  ),
);