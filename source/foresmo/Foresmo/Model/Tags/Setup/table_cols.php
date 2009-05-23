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
  'tag' => 
  array (
    'name' => 'tag',
    'type' => 'varchar',
    'size' => 255,
    'scope' => NULL,
    'default' => NULL,
    'require' => true,
    'primary' => false,
    'autoinc' => false,
  ),
  'tag_slug' => 
  array (
    'name' => 'tag_slug',
    'type' => 'varchar',
    'size' => 255,
    'scope' => NULL,
    'default' => NULL,
    'require' => true,
    'primary' => false,
    'autoinc' => false,
  ),
);