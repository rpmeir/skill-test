<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Wrapper\BootstrapDatagridWrapper;

/**
 * AtendimentoDataGridView
 */
class AtendimentoDataGridView extends TPage
{
    private $form;      // search form
    private $datagrid;  // listing
    private $pageNavigation;
    
    use Adianti\Base\AdiantiStandardListTrait;
    
    /**
     * Class constructor
     * Creates the page, the search form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('unit_a'); // defines the database
        $this->setActiveRecord('Atendimento'); // defines the active record
        $this->setDefaultOrder('id', 'asc');  // defines the default order
        $this->setOrderCommand('cliente->nome', '(select nome from cliente where cliente_id = id)');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        // creates the datagrid columns
        $col_id      = new TDataGridColumn('id', 'Id', 'center', '10%'); // cliente->foto
        $col_descricao    = new TDataGridColumn('descricao', 'Descricao', 'left', '28%'); // descricao
        $col_nome = new TDataGridColumn('{cliente->nome}', 'Nome Cliente', 'left', '28%'); // cliente->nome
        $col_data    = new TDataGridColumn('data_atend', 'Data', 'left', '28%'); // data_atend
        $col_prioridade  = new TDataGridColumn('prioridade_id', 'Prioridade', 'left', '6%'); // atend->prioridade
        
        $col_id->setAction(new TAction([$this, 'onReload']), ['order' => 'id']);
        $col_descricao->setAction(new TAction([$this, 'onReload']), ['order' => 'descricao']);
        $col_nome->setAction(new TAction([$this, 'onReload']), ['order' => 'cliente->nome']);
        $col_data->setAction(new TAction([$this, 'onReload']), ['order' => 'data_atend']);
        $col_prioridade->setAction(new TAction([$this, 'onReload']), ['order' => 'prioridade_id']);
        
        $col_data->setTransformer( function ($value) {
            return TDate::date2br($value);
        });

        $col_prioridade->setTransformer( function($value, $object, $row) {
            $div = new TElement('span');
            $div->class="label";
            $div->style="text-shadow:none; font-size:12px; background-color: ".$object->prioridade->cor;
            $div->add($object->prioridade->nome);
            return $div;
        });

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_descricao);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_data);
        $this->datagrid->addColumn($col_prioridade);
        
        // creates two datagrid actions
        $action1 = new TDataGridAction(['AtendimentoForm', 'onEdit'], ['id'=>'{id}', 'register_state' => 'false']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1, 'Edit', 'far:edit blue');
        $this->datagrid->addAction($action2 ,'Delete', 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the form
        $this->form = new TForm('form_search_atendimento');
        
        // add datagrid inside form
        $this->form->add($this->datagrid);
        $this->form->style = 'overflow-x:auto';
        
        // create the form fields
        $id        = new TEntry('id');
        $descricao      = new TEntry('descricao');
        $nome   = new TEntry('nome');
        $data_atend = new TDate('data_atend');
        $prioridade    = new TDBCombo('prioridade', 'unit_a', 'Prioridade', 'id', 'nome');
        
        $data_atend->setDatabaseMask('yyyy-mm-dd');
        $data_atend->setMask('dd/mm/yyyy');
        
        // ENTER fires exitAction
        $id->exitOnEnter();
        $descricao->exitOnEnter();
        $nome->exitOnEnter();
        $data_atend->exitOnEnter();
        
        $id->setSize('100%');
        $descricao->setSize('100%');
        $nome->setSize('100%');
        $data_atend->setSize('100%');
        $prioridade->setSize('70');
        
        // avoid focus on tab
        $id->tabindex = -1;
        $descricao->tabindex = -1;
        $nome->tabindex = -1;
        $data_atend->tabindex = -1;
        $prioridade->tabindex = -1;
        
        $id->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $descricao->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $nome->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $data_atend->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $prioridade->setChangeAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        
        // create row with search inputs
        $tr = new TElement('tr');
        $this->datagrid->prependRow($tr);
        
        $tr->add( TElement::tag('td', ''));
        $tr->add( TElement::tag('td', ''));
        $tr->add( TElement::tag('td', $id));
        $tr->add( TElement::tag('td', $descricao));
        $tr->add( TElement::tag('td', $nome));
        $tr->add( TElement::tag('td', $data_atend));
        $tr->add( TElement::tag('td', $prioridade));
        
        $this->form->addField($id);
        $this->form->addField($descricao);
        $this->form->addField($nome);
        $this->form->addField($data_atend);
        $this->form->addField($prioridade);
        
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->enableCounters();
        
        $panel = new TPanelGroup('Todos os Atendimentos');
        $panel->add($this->form);
        $panel->addFooter($this->pageNavigation);
        
        $panel->addHeaderActionLink( 'Novo Atendimento',  new TAction(['AtendimentoForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green' );
        
        // creates the page structure using a vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($panel);
        
        // add the box inside the page
        parent::add($vbox);
    }
}