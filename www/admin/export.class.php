<?php
class Export
{
   public function Export($cmf)
   {
      $this->cmf = $cmf;
   }
   
   public function getParents($id)
   {
       $path = array();
       $parents = $this->cmf->select("select PARENT_ID from CATALOGUE where CATALOGUE_ID=? and STATUS=1 order by NAME",$id);

       if(count($parents) > 0)
       {
          foreach($parents as $parent)
          {
             if($parent['PARENT_ID']>0)
             {
                $path[] = $parent['PARENT_ID'];
                $path = array_merge($path,$this->getParents($parent['PARENT_ID']));
             }
          }
       }
       return $path;
   }
   
   function getChilds($pid)
   {
     $childs = array();
     $sth = $this->cmf->execute("select CATALOGUE_ID from CATALOGUE where PARENT_ID=?",$pid);
      while($row = mysql_fetch_array($sth))
      {
         $childs[] = $row['CATALOGUE_ID'];
      }
      return $childs;
   }
   
   public function getPath($id)
   {
        $path = array();
        $parents = $this->getParents($id);
        $parents = array_reverse($parents);
        if($parents)
        {
           for($i=0;$i<sizeof($parents);$i++)
           {
               $catinfo = $this->getCatInfo($parents[$i]);
               $path[] = $catinfo;
           }
        }
        $catinfo2 = $this->getCatInfo($id);
        $path[] = $catinfo2;
        return $path;
   }
   
   public function getCatInfo($id)
   {
      $catinfo = $this->cmf->selectrow_array("select NAME from CATALOGUE where CATALOGUE_ID=?",$id);
      return $catinfo;
   }
}
