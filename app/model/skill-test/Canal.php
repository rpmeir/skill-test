<?php

use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRecord;

/**
 * Canal Active Record
 * @author  <your-name-here>
 */
class Canal extends TRecord
{
    const TABLENAME = 'canal';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('cor');
    }

    /**
     * Method getCanalAtendimentos
     */
    public function getCanalAtendimentos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('canal_id', '=', $this->id));
        return CanalAtendimento::getObjects( $criteria );
    }
    
}
