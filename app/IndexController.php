<?php

namespace App;

final class IndexController extends Controller
{
    public function index()
    {
        $params = new ParseParams;
        $params->mainTagStart = '<div class="products-list__main js-list-view">';
        $params->mainTagEnd = '<div class="products-list__pager js-list-progress">';
        $params->productsBlocks = 'products-list__item';
        $params->product = 'class="link"';
        $params->productPrice = 'product-price__current';
        $params->productImage = 'class="lazy"';
        $params->host = 'zenden.ru';

        $resultParse = (new ParseModel($params))->run($this->request->url);

        $this->view->assign(['parse' => $resultParse]);
        $this->view->assign(['url' => $this->request->url]);
        $this->view->assign(['products' => (new ProductsModel)->getAll()]);
        $this->view->assign(['inc_page' => 'page_index']);
        $this->view->display('template_index');
    }
}
