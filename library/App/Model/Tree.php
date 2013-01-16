<?php
class App_Model_Tree{
  
  protected $_table;
  protected $_fields = array(
            "id"    => false,
            "parent_id"  => false,
            "left"    => false,
            "right"    => false,
            "level"    => false
          );
          
  private $db;
          
  public function __construct($table, $fields = array()) {
    $this->_table = $table;
    if(!count($fields)) {
      foreach($this->_fields as $k => &$v) { $v = $k; }
    }
    else {
      foreach($fields as $key => $field) {
        switch($key) {
          case "id":
          case "parent_id":
          case "left":
          case "right":
          case "level":
            $this->_fields[$key] = $field;
            break;
        }
      }
    }
    
    $this->db = new models_NsTree(array('name'=> $this->_table));
  }
  
  public function addNode($params){
    $right_key=0;
    $level=0;
    
    if(empty($params[$this->_fields["parent_id"]])){
      $right_key = $this->db->getMaxRightKey($this->_fields["right"]);
      $right_key++;
    }
    else{
      $columns = array($this->_fields["right"],
                       $this->_fields["level"]
                       );
                      
      $res = $this->db->getNodeData($columns, $this->_fields, $params[$this->_fields["parent_id"]]);

      if(!empty($res)){
        $right_key = $res[$this->_fields["right"]];
        $level = $res[$this->_fields["level"]];
      }
    }
    
    $this->db->updateTree($right_key, $this->_fields); 
    
    $data[$this->_fields["left"]] = $right_key;
    $data[$this->_fields["right"]] = $right_key + 1;
    $data[$this->_fields["level"]] = $level+1;
    
    return $data;
  }
  
  public function moveNode($current_id, $next_id, $prev_id){
    $next_parent_id = null;
    
    $prev_id = empty($prev_id) ? false:$prev_id;
    $next_id = empty($next_id) ? false:$next_id;
    
    $columns = array($this->_fields['left']
                    ,$this->_fields['right']
                    ,$this->_fields['level']
                    ,$this->_fields['parent_id']);
                      
    // Ключи и уровень перемещаемого узла
    $current_sets = $this->db->getNodeData($columns, $this->_fields, $current_id);
    $current_parent_id = $current_sets[$this->_fields['parent_id']];
        
    if($prev_id) $prev_sets = $this->db->getNodeData($columns, $this->_fields, $next_id);
    if($next_id){
      $next_sets = $this->db->getNodeData($columns, $this->_fields, $next_id);
      $next_parent_id = $this->db->getParentId($this->_fields, $next_id);
    }

    //Уровень нового родительского узла
    if(!$prev_id){
      $level_up = 0;
    }
    else{      
      $level_up = $this->getLevelUp($current_id, $next_id, $prev_id);
    }
    
    $new_parent_id = $this->getNewParentId($current_id, $next_id, $prev_id);
    
    if($new_parent_id==0){ // При переносе узла в корень дерева
      $right_key_near = $this->db->getMaxRightKey($this->_fields["right"]);
    }
    elseif($current_parent_id != $new_parent_id){ // При простом перемещении в другой узел
      $columns = array('('.$this->_fields['right'].' – 1) AS '.$this->_fields['right']);      
      $_prev = $this->db->getNodeData($columns, $this->_fields, $new_parent_id);
      
      $right_key_near = $_prev[$this->_fields['right']];      
    }
    elseif($current_parent_id == $new_parent_id){ // При изменении порядка, когда родительский узел не меняется
      list($left_key_near, $right_key_near) = $this->getNearKeys($current_id, $prev_id);
    }    
    elseif($current_sets[$this->_fields['level']] < $next_sets[$this->_fields['level']]){ // При переносе узла в корень дерева
      $columns = array($this->_fields['right']);      
      $_right_key = $this->db->getNodeData($columns, $this->_fields, $current_sets['parent_id']);

      $left_key_near = $_right_key[$this->_fields['right']];
    }
    
    $params['id_edit'] = $this->db->getChildrens($this->_fields, $current_id);
    
    $params['skew_level'] = $level_up - $current_sets[$this->_fields["level"]] + 1; // смещение уровня изменяемого узла;
    $params['skew_tree'] = $current_sets[$this->_fields["right"]] - $current_sets[$this->_fields["left"]] + 1; // смещение ключей дерева;
    
//    echo "{$next_id} == {$right_key_near} == {$current_sets[$this->_fields['right']]}";
//    exit;
    $params['left_key'] = $current_sets[$this->_fields["left"]];
    $params['right_key'] = $current_sets[$this->_fields["right"]];
    $params['right_key_near'] = $right_key_near;
    
    

    if($right_key_near > $current_sets[$this->_fields['right']]){
      $params['skew_edit'] = $right_key_near - $params['left_key'] + 1 -  $params['skew_tree'];
      
      
      $this->db->updateDownTree($params, $this->_fields);
//      $this->db->updateUpTree($params, $this->_fields);
    }
    else{
      $params['skew_edit'] = $right_key_near - $params['left_key'] + 1;
      
//      $this->db->updateDownTree($params, $this->_fields);
      
      $this->db->updateUpTree($params, $this->_fields);
    }
    
    $update_data[$this->_fields['parent_id']] = $new_parent_id;    
    $this->db->updateNode($update_data, $this->_fields, $current_id);         
  }
  
  private function getNearKeys($current_id, $prev_id){
    $left_key_near = 0;
    $right_key_near = 0;
    $columns = array($this->_fields['left']
                    ,$this->_fields['right']
                    ,$this->_fields['level']
                    ,$this->_fields['parent_id']);
                      
    // Ключи и уровень перемещаемого узла
    $current_sets = $this->db->getNodeData($columns, $this->_fields, $current_id);
                    
    if($prev_id){
      $prev_sets = $this->db->getNodeData($columns, $this->_fields, $prev_id);
      
      if(($prev_sets[$this->_fields['level']]==$current_sets[$this->_fields['level']])&& 
         ($prev_sets[$this->_fields['parent_id']]==$current_sets[$this->_fields['parent_id']]))
      {
        $left_key_near = $prev_sets[$this->_fields['left']];
        $right_key_near = $prev_sets[$this->_fields['right']];
      }
      elseif(($current_sets[$this->_fields['level']]-$prev_sets[$this->_fields['level']])==1 &&
             ($current_sets[$this->_fields['parent_id']]==$prev_id)){
               
        $left_key_near = $prev_sets[$this->_fields['right']];
        $right_key_near = $prev_sets[$this->_fields['left']];
      }
    }
    
    return array($left_key_near, $right_key_near);
  }
  
  private function getLevelUp($current_id, $next_id, $prev_id){
    $columns = array($this->_fields['left']
                    ,$this->_fields['right']
                    ,$this->_fields['level']
                    ,$this->_fields['parent_id']
                    ,$this->_fields['id']);
                    
    if(!$prev_id){
      $_level = 0; 
    }
    elseif(!$next_id){
      $_level = 0; 
    }
    elseif($prev_id){
      $current_sets = $this->db->getNodeData($columns, $this->_fields, $current_id);
      $prev_sets = $this->db->getNodeData($columns, $this->_fields, $prev_id);
      
      $current_level = $current_sets[$this->_fields['level']];
      $current_parent = $current_sets[$this->_fields['parent_id']];
      
      $prev_id = $prev_sets[$this->_fields['id']];
      $prev_level = $prev_sets[$this->_fields['level']];
      $prev_parent = $prev_sets[$this->_fields['parent_id']];
      
      if(($current_level-$prev_level)==1){
        $_level = $prev_level;
      }
      elseif($current_parent==$prev_parent){
        $parent_sets = $this->db->getNodeData($columns, $this->_fields, $current_parent);
        
        $_level = $parent_sets[$this->_fields['level']];
      }
    }
    elseif($next_id){
      $current_sets = $this->db->getNodeData($columns, $this->_fields, $current_id);
      $next_sets = $this->db->getNodeData($columns, $this->_fields, $next_id);
      
      $current_level = $current_sets[$this->_fields['level']];
      $current_parent = $current_sets[$this->_fields['parent_id']];
      
      $next_id = $next_sets[$this->_fields['id']];
      $next_level = $next_sets[$this->_fields['level']];
      $next_parent = $next_sets[$this->_fields['parent_id']];
      
      if(($current_level-$next_level)==1){
        $_level = $next_level;
      }
      elseif($current_parent==$next_parent){
        $parent_sets = $this->db->getNodeData($columns, $this->_fields, $current_parent);
        
        $_level = $parent_sets[$this->_fields['level']];
      }
    }
    
    return $_level; 
  }
  
  private function getNewParentId($current_id, $next_id, $prev_id){
    $columns = array($this->_fields['left']
                    ,$this->_fields['right']
                    ,$this->_fields['level']
                    ,$this->_fields['parent_id']
                    ,$this->_fields['id']);
                    
    if(!$prev_id){
      $_parent_id = 0; 
    }
    elseif($prev_id){
      $current_sets = $this->db->getNodeData($columns, $this->_fields, $current_id);
      $prev_sets = $this->db->getNodeData($columns, $this->_fields, $prev_id);
      
      $current_level = $current_sets[$this->_fields['level']];
      $current_parent = $current_sets[$this->_fields['parent_id']];
      
      $prev_id = $prev_sets[$this->_fields['id']];
      $prev_level = $prev_sets[$this->_fields['level']];
      $prev_parent = $prev_sets[$this->_fields['parent_id']];
      
      if(($current_level-$prev_level)==1){
        $_parent_id = $prev_id;
      }
      elseif($current_parent==$prev_parent){
        $_parent_id = $current_parent;
      }
    }
    elseif(!$next_id){
      $_parent_id = 0; 
    }    
    elseif($next_id){
      $current_sets = $this->db->getNodeData($columns, $this->_fields, $current_id);
      $next_sets = $this->db->getNodeData($columns, $this->_fields, $next_id);
      
      $current_level = $current_sets[$this->_fields['level']];
      $current_parent = $current_sets[$this->_fields['parent_id']];
      
      $next_id = $next_sets[$this->_fields['id']];
      $next_level = $next_sets[$this->_fields['level']];
      $next_parent = $next_sets[$this->_fields['parent_id']];
      
      if(($current_level-$next_level)==1){
        $_parent_id = $next_id;
      }
      elseif($current_parent==$next_parent){
        $_parent_id = $current_parent;
      }
    }
    
    return $_parent_id; 
  }
  
  public function childrenBranch($id){
    return $this->db->getChildrens($this->_fields, $id);
  }
  
  public function parentBranch($id){    
    return $this->db->getParents($this->_fields, $id);
  }
  
  public function deleteNode($id){
    $left_key=0;
    $right_key=0;
    
    $columns = array($this->_fields["left"],
                     $this->_fields["right"]
                     );
                      
    $res = $this->db->getNodeData($columns, $this->_fields, $id);

    if(!empty($res)){
      $left_key = $res[$this->_fields["left"]];
      $right_key = $res[$this->_fields["right"]];
      
      $this->db->deleteBranch($res, $this->_fields);
      $this->db->deleteNode($id, $this->_fields);
      $this->db->updateTreeDelete($res, $this->_fields);
    }
  }
  
  public function disableNode($id){
    $columns = array($this->_fields["left"],
                     $this->_fields["right"]
                     );
                      
    $res = $this->db->getNodeData($columns, $this->_fields, $id);

    if(!empty($res)){
      $res['status'] = 0;
      $this->db->updateBranch($res, $this->_fields);
    }    
  }
  
  public function enableNode($id){
    $columns = array($this->_fields["left"],
                     $this->_fields["right"]
                     );
                      
    $res = $this->db->getNodeData($columns, $this->_fields, $id);

    if(!empty($res)){
      $res['status'] = 1;
      $this->db->updateBranch($res, $this->_fields);
    }
  }
}