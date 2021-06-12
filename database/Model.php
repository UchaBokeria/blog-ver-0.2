<?php
  class Model extends DBH
  {
    protected function get($sql,$params,$html = ""){
      $stmt = $this->connect()->prepare($sql);
      $length = count($params);

      for ($i=0,$bindInd=0; $i < $length; $i++) {
        $bindInd++;
        $stmt->bindParam($bindInd, $params[$i]["attr"], $params[$i]["type"]);
      }
      $stmt->execute();
      $result = $stmt->fetchAll();
      //print_r($result);
      if($html == "")
        return $result;
      else{
        $html = htmlspecialchars_decode($html);
        $reshtml = "";
        $paramKeys = array_keys($result);
        $parsed = $html;
        foreach ($result as $value) {
            foreach ($value as $key => $paramValue) {
                $parsed = str_replace('$value["'.$key.'"]',$paramValue,$parsed);
            }
            $reshtml .= $parsed;
        }
        $allResult = array();
        $allResult["html"] = $reshtml;
        $allResult["data"] = $result;
        return $allResult;
      }
    }

    protected function getAll($sql){
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll();
      return $result;
    }

    protected function set($sql,$params){
      $stmt = $this->connect()->prepare($sql);
      $length = count($params);

      for ($i=0,$bindInd=0; $i < $length; $i++) {
        $bindInd++;
        $stmt->bindParam($bindInd, $params[$i]['attr']);
      }
      $stmt->execute();
    }
  }
