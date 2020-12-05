<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
use Adianti\Widget\Util\TKanban;

/**
 * OverviewAtendimentoView
 */
class OverviewAtendimentoView extends TPage
{
	
	public function __construct()
	{
		parent::__construct();
		
		TTransaction::open('unit_a');
		$etapa_atendimentos = EtapaAtendimento::orderBy('ordem')->load();
		$atendimentos  = Atendimento::orderBy('ordem')->load();
		
		$kanban = new TKanban;
		foreach ($etapa_atendimentos as $key => $etapa_atendimento)
		{
			$kanban->addStage($etapa_atendimento->id, $etapa_atendimento->nome, $etapa_atendimento);
		}
		
		foreach ($atendimentos as $key => $atendimento)
		{
			$kanban->addItem($atendimento->id, $atendimento->etapa_atendimento_id, $atendimento->prioridade->nome, $atendimento->descricao, $atendimento->prioridade->cor, $atendimento);
		}
		
        TTransaction::close();
        
		$kanban->setItemTemplate('app/resources/skill-test-card.html');
		$kanban->setItemDropAction(new TAction([__CLASS__, 'onUpdateItemDrop']));
		$kanban->setStageDropAction(new TAction([__CLASS__, 'onUpdateStageDrop']));
		
		parent::add($kanban);
	}
	
	public function onLoad($param)
	{
	
	}
	
    /**
     * Update stage on drop
     */
	public static function onUpdateStageDrop($param)
	{
		if (empty($param['order']))
		{
			return;
		}
		
		try
		{
    		TTransaction::open('unit_a');
    		
    		foreach ($param['order'] as $key => $id)
    		{
    			$sequence = ++ $key;
    
    			$etapa_atendimento = new EtapaAtendimento($id);
    			$etapa_atendimento->ordem = $sequence;
    
    			$etapa_atendimento->store();
    		}
    		
    		TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
	}
	
    /**
     * Update item on drop
     */
	public static function onUpdateItemDrop($param)
	{
		if (empty($param['order']))
		{
			return;
		}

        try
        {
    		TTransaction::open('unit_a');
    
    		foreach ($param['order'] as $key => $id)
    		{
    			$sequence = ++$key;
    
    			$atendimento = new Atendimento($id);
    			$atendimento->atendimento_order = $sequence;
    			$atendimento->etapa_atendimento_id = $param['stage_id'];
    			$atendimento->store();
    		}
    		
    		TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
	}
	
	/**
	 *
	 */
	public static function onDelete($param)
	{
        // define the delete action
        $action = new TAction(array(__CLASS__, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
	}
	
    /**
     * method Delete()
     * Delete a record
     */
    public static function Delete($param)
    {
        try
        {
            // instantiates object and delete
            TTransaction::open('unit_a');
            $object = new Atendimento( $param['key'] );
            $object->delete();
            TTransaction::close();
            
            AdiantiCoreApplication::loadPage(__CLASS__, 'onLoad');
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Item click
     */
	public static function onItemClick($param = NULL)
	{
		new TMessage('info', str_replace(',', '<br>', json_encode($param)));
	}
	
    /**
     * Display condition
     */
	public static function teste($param = NULL)
	{
		return TRUE;
	}
}
