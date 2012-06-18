<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */


/*
The template class for the hycus CMS
**/
defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

class hTemplate{
   private static $baseDir = '.';
   private static $defaultTemplateExtension = '.php';

   public static function setBaseDir($dir){
     self::$baseDir = $dir;
   }

   public static function getbaseDir(){
     return self::$baseDir;
   }

   public static function setDefaultTemplateExtension($ext){
     self::$defaultTemplateExtension = $ext;
   }

   public static function getDefaultTemplateExtension(){
     return self::$defaultTemplateExtension;
   }

   public static function loadTemplate($template, $vars = array(), $baseDir=null){
    if($baseDir == null){
      $baseDir = self::getbaseDir();
    }

    $templatePath = $baseDir.'/'.$template.''.self::getDefaultTemplateExtension();
    if(!file_exists($templatePath)){
      throw new Exception('Could not include template '.$templatePath);
    }

    return self::loadTemplateFile($templatePath, $vars);

  }

  public static function renderTemplate($template, $vars = array(), $baseDir=null){
    echo self::loadTemplate($template, $vars, $baseDir);
  }

  private static function loadTemplateFile($__ct___templatePath__, $__ct___vars__){
    extract($__ct___vars__, EXTR_OVERWRITE);
    $__ct___template_return = '';
    ob_start();
    require $__ct___templatePath__;
    $__ct___template_return = ob_get_contents();
    ob_end_clean();
    return $__ct___template_return;
  }
}