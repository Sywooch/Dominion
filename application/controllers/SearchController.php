<?php

  class SearchController extends App_Controller_Frontend_Action {    
    public $search;
    
    public $search_per_page;
    public $query;
    
    function init(){
      parent::init();      
      $this->search_per_page = $this->getSettingValue('search_per_page') ? $this->getSettingValue('search_per_page'):15;
      
      Zend_Search_Lucene_Analysis_Analyzer::setDefault(
      new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive());
    }

    public function indexAction(){             
      $request = $this->GetRequest();
      if($request->isGet()){        
        $AnotherPages = new models_AnotherPages();
        
        $search_text = trim($request->getQuery('search_text'));
        $search_text = empty($search_text) ? trim($this->_getParam('search_text', '')):$search_text;

        $o_data['id'] = 0;
        $o_data['currency'] = $this->currency;

        $this->openData($o_data);
        
        $doc_id = $AnotherPages->getDocByUrl('/search/');
        
        $ap_helper = $this->_helper->helperLoader('AnotherPages');      
        $ap_helper->setLang($this->lang, $this->lang_id);
        $ap_helper->setModel($AnotherPages);
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->getDocInfo($doc_id);
        $this->domXml = $ap_helper->getDomXml();
        
        $this->getDocPath($doc_id);

        $search_text = mb_convert_case($search_text , MB_CASE_LOWER, 'UTF-8');
        $this->domXml->create_element('query', $search_text);       
        $this->domXml->go_to_parent();

        if(!empty($search_text)){         
          
          try{
            Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
            $index = Zend_Search_Lucene::open(INDEX_PATH);
          } catch (Zend_Search_Lucene_Exception $e) {
            echo "Ошибка:{$e->getMessage()}";
            return false;
          }

          $queryArray = explode(" ",$search_text);
          $query = new Zend_Search_Lucene_Search_Query_MultiTerm();
          
          $query->addTerm(new Zend_Search_Lucene_Index_Term($search_text), null);
          foreach ($queryArray as $qer){
            $query->addTerm(new Zend_Search_Lucene_Index_Term($qer), null);
          }

          $hits = $index->find($query);

          $numHits = count($hits);
          if($numHits>0){
            $this->resultToXML($hits, $search_text, $query);
          }
        }
        
      }
       
    }
    
    private function resultToXML($result, $query, $qr){
      if (!empty($result)){  
        $Item = new models_Item();
        
        $page = $this->_getParam('page', 1);

        $paginator = Zend_Paginator::factory($result);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($this->search_per_page);
        
        
        $amount = $paginator->getPages()->totalItemCount;
        $page = $page > ceil($amount/$this->search_per_page) ? ceil($amount/$this->search_per_page) : $page;
        $end = ceil($amount/$this->search_per_page);
        
        $this->domXml->set_tag('//data', true);       
        $this->domXml->create_element('search_count',$amount,2);
        $this->domXml->go_to_parent();
        
        $this->openSection($query, 
                               $page,
                               $end,
                               $amount);  
                               
        $items = $paginator->getCurrentItems();
//        $pos = $this->item_per_page*($this->_getParam('page',1)-1);
        
        
        $curr_info = $Item->getCurrencyInfo($this->currency);
        
        foreach($items as $hit){        
          $item_info = $Item->getItemInfo($hit->item_id);
          
          list($new_price, $new_price1) = $Item->recountPrice($item_info['PRICE'],$item_info['PRICE1'],$item_info['CURRENCY_ID'],$this->currency, $curr_info['PRICE']);

          $item_info['sh_disc_img_small'] = '';
          $item_info['sh_disc_img_big'] = '';
          $item_info['has_discount'] = 0;
          
          if($this->currency > 1){
            $item_info['iprice'] = round($new_price,1);
            $item_info['iprice1'] = round($new_price1,1);
          }
          else{
            $item_info['iprice'] = round($new_price);
            $item_info['iprice1'] = round($new_price1);
          }                       
          
          $params['currency'] = $this->currency;
          $helperLoader = Zend_Controller_Action_HelperBroker::getStaticHelper('HelperLoader');
          $ct_helper = $helperLoader->loadHelper('Cart', $params);
          $ct_helper->setModel($Item);
          $item_info = $ct_helper->recountPrice($item_info);
          
          $node_attr = array('item_id'  => $item_info['ITEM_ID']
                      ,'price'   =>  $item_info['iprice']
                      ,'price1' =>  $item_info['iprice1']
                      ,'real_price'   =>  $item_info['PRICE']
                      ,'real_price1' =>  $item_info['PRICE1']);
          
          $this->domXml->create_element('search_result',"",2);
          $this->domXml->set_attribute($node_attr);
                              
          $this->domXml->create_element('href',$hit->url);
          $this->domXml->create_element('name',$item_info['BRAND_NAME'].' '.$item_info['NAME']);
          $this->domXml->create_element('short_description',$item_info['DESCRIPTION']);
          $this->domXml->create_element('sname',$curr_info['SNAME']);
          $this->domXml->create_element('nat_sname',$item_info['SNAME']);
          
          if(!empty($item_info['IMAGE2']) && strchr($item_info['IMAGE2'],"#")){
            $tmp=explode('#',$item_info['IMAGE2']);
            $this->domXml->create_element('image_middle','',2);
            $this->domXml->set_attribute(array('src'  => $tmp[0],
                                         'w'    => $tmp[1],
                                         'h'    => $tmp[2]
                                         )
                                        );
            $this->domXml->go_to_parent();
          }
          
          $this->domXml->go_to_parent();                
        }
      }
    }
    
    private function openSection($query='', $page, $end=0, $amount=0){
      $this->domXml->create_element('section',"",2);
      $this->domXml->set_attribute(array('query' => $query
                                        ,'page' => $page
                                        ,'pcount' => $end
                                        ,'count' => $amount
                                   ));
                                   
      $this->domXml->go_to_parent(); 
    }
    
    private function createIndex(){
      $Item = new models_Item();
      $Catalogue = new models_Catalogue();
      
        set_time_limit(0);
      //удаляем существующий индекс, в большинстве случае эта операция с последующий созданием нового индекса работает гораздо быстрее
        $this->recursive_remove_directory(INDEX_PATH, TRUE);
 
        try {
          $index = Zend_Search_Lucene::create(INDEX_PATH);
        } catch (Zend_Search_Lucene_Exception $e) {
          echo "<p class=\"ui-bad-message\">Не удалось создать поисковой индекс: {$e->getMessage()}</p>";
        }
 
        try {
          Zend_Search_Lucene_Analysis_Analyzer::setDefault(
          new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());
          $i = 0;       
            $items = $Item->getItemsSearch();            
            foreach ($items as $item) {
                $doc = new Zend_Search_Lucene_Document();
                $doc->addField(Zend_Search_Lucene_Field::UnIndexed('item_id', $item['ITEM_ID']));
                $doc->addField(Zend_Search_Lucene_Field::Text('name', $item['NAME'], 'UTF-8'));
                $doc->addField(Zend_Search_Lucene_Field::Text('brand_name', $item['BRAND_NAME'], 'UTF-8'));
                $doc->addField(Zend_Search_Lucene_Field::Text('catalogue', $item['CNAME'], 'UTF-8'));
                $doc->addField(Zend_Search_Lucene_Field::Text('description', $item['DESCRIPTION'], 'UTF-8'));
                $doc->addField(Zend_Search_Lucene_Field::Keyword('article', $item['ARTICLE']));

                $attrValues = $Item->getItemSearchAttrs($item['ITEM_ID']);
                if(!empty($attrValues)){
                    $attrSearchField = '';
                    foreach ($attrValues as $val){
                        $attrSearchField .= $val['NAME']." ";
                    }
                    $doc->addField(Zend_Search_Lucene_Field::Text('attr_val',$attrSearchField, 'UTF-8'));
                }

                $url = $item['CATALOGUE_REALCATNAME'].$item['ITEM_ID'].'-'.$item['CATNAME'].'/';
                $doc->addField(Zend_Search_Lucene_Field::UnIndexed('url', $url));
                $doc->addField(Zend_Search_Lucene_Field::UnIndexed('image', $item['IMAGE1']));
                $doc->addField(Zend_Search_Lucene_Field::UnIndexed('image_src', '/images/it/'));
                $index->addDocument($doc);
                $i++;
            }
            //$news = $this->News->getSearchNews();            
//            foreach ($news as $new) {
//              $doc = new Zend_Search_Lucene_Document();
//              $doc->addField(Zend_Search_Lucene_Field::UnIndexed('item_id', $new['NEWS_ID']));
//              $doc->addField(Zend_Search_Lucene_Field::Text('name', $new['NAME'], 'UTF-8'));
//              $doc->addField(Zend_Search_Lucene_Field::Text('description', $new['descript'], 'UTF-8'));

//              $url = "/news/all/n/{$new['NEWS_ID']}/";
//              $doc->addField(Zend_Search_Lucene_Field::UnIndexed('url', $url));
//              $doc->addField(Zend_Search_Lucene_Field::UnIndexed('image', $new['IMAGE1']));
//              $doc->addField(Zend_Search_Lucene_Field::UnIndexed('image_src', '/images/news/'));
//              $index->addDocument($doc);
//              $i++;
//            }
//            $articles = $this->Article->getSearchArticles();            
//            foreach ($articles as $article) {
//              $doc = new Zend_Search_Lucene_Document();
//              $doc->addField(Zend_Search_Lucene_Field::UnIndexed('item_id', $article['ARTICLE_ID']));
//              $doc->addField(Zend_Search_Lucene_Field::Text('name', $article['NAME'], 'UTF-8'));
//              $doc->addField(Zend_Search_Lucene_Field::Text('description', $article['descript'], 'UTF-8'));

//              $url = "/articles/view/n/{$article['ARTICLE_ID']}/";
//              $doc->addField(Zend_Search_Lucene_Field::UnIndexed('url', $url));
//              $doc->addField(Zend_Search_Lucene_Field::UnIndexed('image', $article['IMAGE1']));
//              $doc->addField(Zend_Search_Lucene_Field::UnIndexed('image_src', '/images/article/'));
//              $index->addDocument($doc);
//              $i++;
//            }
            $cats = $Catalogue->getIndexTree();            
            foreach ($cats as $cat) {
              $doc = new Zend_Search_Lucene_Document();
              $doc->addField(Zend_Search_Lucene_Field::UnIndexed('item_id', $cat['CATALOGUE_ID']));
              $doc->addField(Zend_Search_Lucene_Field::Text('name', $cat['NAME'], 'UTF-8'));

//              $articleID = $this->Catalogue->getCatArticle($cat['CATALOGUE_ID']);
//              $articleInfo = $this->Article->getArticleSingle($articleID);
//              $doc->addField(Zend_Search_Lucene_Field::Text('description', $articleInfo['DESCRIPTION'], 'UTF-8'));

              $url = $this->getRealURL($cat);
              $doc->addField(Zend_Search_Lucene_Field::UnIndexed('url', $url));
              $doc->addField(Zend_Search_Lucene_Field::UnIndexed('image', $cat['IMAGE1']));
              $doc->addField(Zend_Search_Lucene_Field::UnIndexed('image_src', '/images/cat/'));
              $index->addDocument($doc);
              $i++;
            }
            
        } catch (Zend_Search_Lucene_Exception $e) {
            echo "<p class=\"ui-bad-message\">Ошибки индексации: {$e->getMessage()}</p>";
        }
 
        $index->optimize();
    }
    
    function recursive_remove_directory($directory, $empty=FALSE) {
      if(substr($directory,-1) == '/'){
        $directory = substr($directory,0,-1);
      }
      if(!file_exists($directory) || !is_dir($directory)){
        return FALSE;
      }elseif(is_readable($directory)){
        $handle = opendir($directory);
        while (FALSE !== ($item = readdir($handle))){
          if($item != '.' && $item != '..'){
            $path = $directory.'/'.$item;
            if(is_dir($path)){
              self::recursive_remove_directory($path);
            }else{
              unlink($path);
            }
          }
        }
        closedir($handle);
        if($empty == FALSE){
          if(!rmdir($directory)){
            return FALSE;
          }
        }
      }
      return TRUE;
    }
    
    public function updateAction() {
      set_time_limit(3600);
      $this->_helper->viewRenderer->setNoRender(true);
      $this->createIndex();
    }
  }