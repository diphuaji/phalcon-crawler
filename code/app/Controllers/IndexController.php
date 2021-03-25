<?php

namespace App\Controllers;

use App\Services\IndexService;
use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    /**
     * @var IndexService
     */
    private $indexService;

    public function initialize(){
        $this->indexService = new IndexService();
    }

    public function indexAction()
    {

        $result = $this->indexService->getResultForIndex();
        $tableData = $result['data'];
        $tableHeaders = $this->indexService->getHeaders();
        $summary = $result['summary'];
        $summaryFieldNames = $this->indexService->getSummaryFieldNames();
//        print(json_encode($this->indexService->getResultForIndex()));exit();
        $this->view->setVars(compact([
            'data',
            'tableHeaders',
            'tableData',
            'summary',
            'summaryFieldNames'
        ]))->pick('index/index');
    }
}