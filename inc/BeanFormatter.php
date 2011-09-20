<?php

class BeanFormatter implements RedBean_IBeanFormatter
{
    public function formatBeanTable($table)
    {
        return $table;
    }
     
    public function formatBeanID( $table ) 
    {
        return 'internal_'.$table.'_id'; // append table name to id. The table should not include the prefix.
    }
	
	static function _formatBeanId($table)
	{
		return 'internal_'.$table.'_id';
	}
	
	public function getAlias($type)
	{
		return parent::getAlias($type) ;
	}	
}