<?php

  class FileUpload { 
    private $path = "./uploads";         
    private $allowtype = array('jpg','gif','png'); 
    private $maxsize = 1000000;           
    private $israndname = true;          
  
    private $originName;            
    private $tmpFileName;             
    private $fileType;              
    private $fileSize;              
    private $newFileName;              
    private $errorNum = 0;             
    private $errorMess="";             
 
    function set($key, $val){
      $key = strtolower($key); 
      if( array_key_exists( $key, get_class_vars(get_class($this) ) ) ){
        $this->setOption($key, $val);
      }
      return $this;
    }
  

  
    function upload($fileField) {
      $return = true;

      if( !$this->checkFilePath() ) {       
        $this->errorMess = $this->getError();
        return false;
      }

      $name = $_FILES[$fileField]['name'];
      $tmp_name = $_FILES[$fileField]['tmp_name'];
      $size = $_FILES[$fileField]['size'];
      $error = $_FILES[$fileField]['error'];
  

      if(is_Array($name)){    
        $errors=array();

        for($i = 0; $i < count($name); $i++){ 
        
          if($this->setFiles($name[$i],$tmp_name[$i],$size[$i],$error[$i] )) {
            if(!$this->checkFileSize() || !$this->checkFileType()){
              $errors[] = $this->getError();
              $return=false; 
            }
          }else{
            $errors[] = $this->getError();
            $return=false;
          }
      
          if(!$return)          
            $this->setFiles();
        }
  
        if($return){
       
          $fileNames = array();      
       
          for($i = 0; $i < count($name); $i++){ 
            if($this->setFiles($name[$i], $tmp_name[$i], $size[$i], $error[$i] )) {
              $this->setNewFileName(); 
              if(!$this->copyFile()){
                $errors[] = $this->getError();
                $return = false;
              }
              $fileNames[] = $this->newFileName;  
            }          
          }
          $this->newFileName = $fileNames;
        }
        $this->errorMess = $errors;
        return $return;
    
      } else {
       
        if($this->setFiles($name,$tmp_name,$size,$error)) {
       
          if($this->checkFileSize() && $this->checkFileType()){ 
          
            $this->setNewFileName(); 
        
            if($this->copyFile()){ 
              return true;
            }else{
              $return=false;
            }
          }else{
            $return=false;
          }
        } else {
          $return=false; 
        }
      
        if(!$return)
          $this->errorMess=$this->getError();  
  
        return $return;
      }
    }
  

    public function getFileName(){
      return $this->newFileName;
    }
  

    public function getErrorMsg(){
      return $this->errorMess;
    }
  

    private function getError() {
      $str = "上載文檔<font color='red'>{$this->originName}</font>時出錯 : ";
      switch ($this->errorNum) {
        case 4: $str .= "沒有文檔被上載"; break;
        case 3: $str .= "文檔只有部分被上載?"; break;
        case 2: $str .= "上載文檔過大"; break;
        case 1: $str .= "上載文檔超時"; break;
        case -1: $str .= "上載文檔類型錯誤"; break;
        case -2: $str .= "文檔過大,不能超過{$this->maxsize}個字節"; break;
        case -3: $str .= "上載失敗"; break;
        case -4: $str .= "上載文件夾不存在"; break;
        case -5: $str .= "必須指定路徑"; break;
        default: $str .= "未知錯誤";
      }
      return $str.'<br>';
    }
  
   
    private function setFiles($name="", $tmp_name="", $size=0, $error=0) {
      $this->setOption('errorNum', $error);
      if($error)
        return false;
      $this->setOption('originName', $name);
      $this->setOption('tmpFileName',$tmp_name);
      $aryStr = explode(".", $name);
      $this->setOption('fileType', strtolower($aryStr[count($aryStr)-1]));
      $this->setOption('fileSize', $size);
      return true;
    }
  

    private function setOption($key, $val) {
      $this->$key = $val;
    }
  

    private function setNewFileName() {
      if ($this->israndname) {
        $this->setOption('newFileName', $this->proRandName());  
      } else{ 
        $this->setOption('newFileName', $this->originName);
      } 
    }
  

    private function checkFileType() {
      if (in_array(strtolower($this->fileType), $this->allowtype)) {
        return true;
      }else {
        $this->setOption('errorNum', -1);
        return false;
      }
    }
  

    private function checkFileSize() {
      if ($this->fileSize > $this->maxsize) {
        $this->setOption('errorNum', -2);
        return false;
      }else{
        return true;
      }
    }
  

    private function checkFilePath() {
      if(empty($this->path)){
        $this->setOption('errorNum', -5);
        return false;
      }
      if (!file_exists($this->path) || !is_writable($this->path)) {
        if (!@mkdir($this->path, 0755)) {
          $this->setOption('errorNum', -4);
          return false;
        }
      }
      return true;
    }
  

    private function proRandName() {    
      $fileName = date('YmdHis')."_".rand(100,999);    
      return $fileName.'.'.$this->fileType; 
    }
  

    private function copyFile() {
      if(!$this->errorNum) {
        $path = rtrim($this->path, '/').'/';
        $path .= $this->newFileName;
		// iconv('big5','GBK',$path)
        if (@move_uploaded_file($this->tmpFileName, $path)) {
          return true;
        }else{
          $this->setOption('errorNum', -3);
          return false;
        }
      } else {
        return false;
      }
    }
  }
 ?>