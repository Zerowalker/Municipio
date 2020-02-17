<?php

namespace Municipio;

use \HelsingborgStad\GlobalBladeEngine as Blade;

class Template
{
  public function __construct() {
    add_action('init', array($this, 'registerViewPaths')); 
    add_filter('template_redirect', array($this, 'addTemplateFilters'));
    add_filter('template_include', array($this, 'sanitizeViewName'), 10);
    add_filter('template_include', array($this, 'loadViewData'), 15);
  }

  /**
   * Register paths containing views
   * @return void
   */
  public static function registerViewPaths() {
    if($viewPaths = \Municipio\Helper\Template::getViewPaths()) {
      foreach($viewPaths as $path) {
        Blade::addViewPath(rtrim($path, DIRECTORY_SEPARATOR), true);
      }
    } else {
      wp_die("No view paths registered, please register at least one."); 
    }
  }

  /**
   * @param $view
   */
  public function sanitizeViewName($view) { 
    return $this->getViewNameFromPath($view); 
  }

  /**
   * @param $view
   * @param array $data
   */
  public function loadViewData($view, $data = array()) { 

    $viewData = $this->accessProtected(
                  $this->loadController($this->getControllerNameFromView($view)),
                  'data'
                );

    $viewData = apply_filters('Municipio/blade/data', $viewData); 

    return $this->renderView(
      (string)  $this->getViewNameFromPath($view), 
      (array)   $viewData
    );
  }

  /**
  * Loads controller for view template
  * @param  string $template Path to template
  * @return object           The controller
  */
  public function loadController($template)
  {
    //Do something before controller creation
    do_action_deprecated('Municipio/blade/before_load_controller', $template, '3.0', 'Municipio/blade/beforeLoadController'); 

    //Handle 404 renaming
    if ($template == '404') {
        $template = 'e404.php';
    }

    //Locate controller
    if (!$controller = \Municipio\Helper\Controller::locateController($template)) {
        $controller = \Municipio\Helper\Controller::locateController('BaseController');
    }

    //Filter 
    $controller = apply_filters('Municipio/blade/controller', $controller);

    //Require controller
    require_once $controller;
    $namespace = \Municipio\Helper\Controller::getNamespace($controller);
    $class = '\\' . $namespace . '\\' . basename($controller, '.php');

    //Do something after controller creation
    do_action_deprecated('Municipio/blade/after_load_controller', $template, '3.0', 'Municipio/blade/afterLoadController'); 

    return new $class();
  }
  
  /**
   * @param $view
   * @param array $data
   */
  public function renderView($view, $data = array())
  {
    try {
        echo Blade::instance()->make(
            $view,
            $data
        )->render();
    } catch (\Throwable $e) {
        echo Blade::instance()->make(
            'e404',
            array_merge(
                $data,
                array('errorMessage' => $e)
            )
        )->render();
    }

    return false;
  }

  /**
   * Get a view clean view path
   * @param  string $view The view path
   * @return void
   */
  private function getViewNameFromPath($view) {
    $view = str_replace(\Municipio\Helper\Template::getViewPaths(), "", $view); // Remove view path
    $view = str_replace(".blade.php", "", $view); // Remove blade suffix
    $view = trim($view, "/"); // Remove trailing & leading slash
    $view = str_replace("/", ".", $view); // Use blade directory notation
    return $view; 
  }

  /**
   * Get a controller name
   * @param  string $view The view path
   * @return void
   */
  private function getControllerNameFromView($view) {
    return str_replace(".", "", ucwords($view)); 
  }

  /**
   * Filter template name (what to look for)
   * @return string
   */
  public function addTemplateFilters()
  {
      $types = array(
          'index'      => 'index.blade.php',
          'home'       => 'archive.blade.php',
          'single'     => 'single.blade.php',
          'page'       => 'page.blade.php',
          '404'        => '404.blade.php',
          'archive'    => 'archive.blade.php',
          'author'     => 'author.blade.php',
          'category'   => 'category.blade.php',
          'tag'        => 'tag.blade.php',
          'taxonomy'   => 'taxonomy.blade.php',
          'date'       => 'date.blade.php',
          'front-page' => 'front-page.blade.php',
          'paged'      => 'paged.blade.php',
          'search'     => 'search.blade.php',
          'single'     => 'single.blade.php',
          'singular'   => 'singular.blade.php',
          'attachment' => 'attachment.blade.php',
      );

      $types = apply_filters_deprecated('Municipio/blade/template_types', [$types], '3.0', 'Municipio/blade/templateTypes'); 

      if (isset($types) && !empty($types) && is_array($types)) {
        foreach ($types as $key => $type) {
          add_filter($key . '_template', function ($original) use ($key, $type, $types) {
            
            //Fron page
            if (empty($original) && is_front_page()) {
              $type = $types['front-page'];
            }

            $templatePath = \Municipio\Helper\Template::locateTemplate($type);

            // Look for post type archive
            global $wp_query;
            if (is_post_type_archive() && isset($wp_query->query['post_type'])) {
              $search = 'archive-' . $wp_query->query['post_type'] . '.blade.php';

              if ($found = \Municipio\Helper\Template::locateTemplate($search)) {
                $templatePath = $found;
              }
            }

            // Look for post type single page
            if (is_single() && isset($wp_query->query['post_type'])) {
              $search = 'single-' . $wp_query->query['post_type'] . '.blade.php';
              if ($found = \Municipio\Helper\Template::locateTemplate($search)) {
                $templatePath = $found;
              }
            }

            // Transformation made 
            if ($templatePath) {
              return $templatePath;
            }

            // No changes needed
            return $original;

          });
        }
      }
    }

  /**
   * Proxy for accessing provate props
   * @return mixed Array of values
   */
  public function accessProtected($obj, $prop) {
      $reflection = new \ReflectionClass($obj);
      $property = $reflection->getProperty($prop);
      $property->setAccessible(true);
      return $property->getValue($obj);
  }
}