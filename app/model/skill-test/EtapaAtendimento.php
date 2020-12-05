<?php
/**
 * EtapaAtendimento Active Record
 * @author  <your-name-here>
 */
class EtapaAtendimento extends TRecord
{
    const TABLENAME = 'etapa_atendimento';
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
        parent::addAttribute('ordem');
    }

    
    /**
     * Method getAtendimentos
     */
    public function getAtendimentos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('etapa_atendimento_id', '=', $this->id));
        return Atendimento::getObjects( $criteria );
    }
    


}
