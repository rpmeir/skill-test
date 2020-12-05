<?php

use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRecord;

/**
 * Cliente Active Record
 * @author  <your-name-here>
 */
class Cliente extends TRecord
{
    const TABLENAME = 'cliente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('data_nasc');
        parent::addAttribute('foto');
    }

    
    /**
     * Method getAtendimentos
     */
    public function getAtendimentos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cliente_id', '=', $this->id));
        return Atendimento::getObjects( $criteria );
    }
    


}
