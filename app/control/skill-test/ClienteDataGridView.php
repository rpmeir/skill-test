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
use Adianti\Wrapper\BootstrapDatagridWrapper;

/**
 * ClienteDataGridView
 */
class ClienteDataGridView extends TPage
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
        $this->setActiveRecord('Cliente'); // defines the active record
        $this->setDefaultOrder('id', 'asc');  // defines the default order
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        // creates the datagrid columns
        $col_id      = new TDataGridColumn('id', 'Id', 'center', '10%'); 
        $col_foto  = new TDataGridColumn('foto', 'Foto', 'left', '20%'); 
        $col_nome = new TDataGridColumn('nome', 'Nome Cliente', 'left', '35%'); 
        $col_data_nasc    = new TDataGridColumn('data_nasc', 'Data Nascimento', 'left', '35%'); 
        
        $col_id->setAction(new TAction([$this, 'onReload']), ['order' => 'id']);
        $col_nome->setAction(new TAction([$this, 'onReload']), ['order' => 'nome']);
        $col_data_nasc->setAction(new TAction([$this, 'onReload']), ['order' => 'data_nasc']);

        $col_foto->setTransformer( function($value, $object, $row) {
            if (file_exists($value)) {
                return "<img style='max-width:50px;border-radius:50%' src='download.php?file=$value'>";
            }
        });
        
        $col_data_nasc->setTransformer( function ($value) {
            return TDate::date2br($value);
        });

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_foto);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_data_nasc);
        
        // creates two datagrid actions
        $action1 = new TDataGridAction(['ClienteForm', 'onEdit'], ['id'=>'{id}', 'register_state' => 'false']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1, 'Edit', 'far:edit blue');
        $this->datagrid->addAction($action2 ,'Delete', 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the form
        $this->form = new TForm('form_search_cliente');
        
        // add datagrid inside form
        $this->form->add($this->datagrid);
        $this->form->style = 'overflow-x:auto';
        
        // create the form fields
        $id        = new TEntry('id');
        $nome   = new TEntry('nome');
        $data_nasc = new TDate('data_nasc');
        
        $data_nasc->setDatabaseMask('yyyy-mm-dd');
        $data_nasc->setMask('dd/mm/yyyy');
        
        // ENTER fires exitAction
        $id->exitOnEnter();
        $nome->exitOnEnter();
        $data_nasc->exitOnEnter();
        
        $id->setSize('100%');
        $nome->setSize('100%');
        $data_nasc->setSize('100%');
        
        // avoid focus on tab
        $id->tabindex = -1;
        $nome->tabindex = -1;
        $data_nasc->tabindex = -1;
        
        $id->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $nome->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $data_nasc->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        
        // create row with search inputs
        $tr = new TElement('tr');
        $this->datagrid->prependRow($tr);
        
        $tr->add( TElement::tag('td', ''));
        $tr->add( TElement::tag('td', ''));
        $tr->add( TElement::tag('td', $id));
        $tr->add( TElement::tag('td', ''));
        $tr->add( TElement::tag('td', $nome));
        $tr->add( TElement::tag('td', $data_nasc));
        
        $this->form->addField($id);
        $this->form->addField($nome);
        $this->form->addField($data_nasc);
        
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->enableCounters();
        
        $panel = new TPanelGroup('Todos os Clientes');
        $panel->add($this->form);
        $panel->addFooter($this->pageNavigation);
        
        $panel->addHeaderActionLink( 'Novo Cliente',  new TAction(['ClienteForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green' );
        
        // creates the page structure using a vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($panel);
        
        // add the box inside the page
        parent::add($vbox);
    }
}