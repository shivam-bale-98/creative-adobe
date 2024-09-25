<?php
namespace Concrete\Package\Formidable\Src\Formidable;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\HttpFoundation\Session\Session;

class Data
{
    private $prefix = 'formidable';

    public $request;
    public $session;

    public function __construct()
    {
        $app = Application::getFacadeApplication();

        // $this->request = $app['request'];
        // $this->session = $app['session'];

        $this->request = $app->make(Request::class);
        $this->session = $app->make(Session::class);

        if ($this->request->isPost()) {
            foreach ($this->request->post() as $key => $post) {
                if (is_array($post)) {
                    $post = array_merge((array)$this->session->get($key), (array)$post);
                }
                $this->session->set($this->prefix.'||'.$key, $post);
            }
        }
    }

    /**
     * Returns the value from post, session or default
     * key: mixed
     * default: mixed // set default if post and session are empty
     * no_default: boolean // return empty if there should be any values
     */
    public function get($key, $default = '', $no_default = false)
    {
        $args = [];

        if (is_array($key)) {
            $args = $key;
            $key = array_shift($args);
        }

        // Load submitted values
        if ($this->request->isPost()) {
            $value = $this->request->post($key);
            if ($value) {
                if (is_array($value) && !empty($args)) {
                    foreach ($args as $second) {
                        $value = isset($value[$second])?$value[$second]:'';
                    }
                    if (!empty($value)) {
                        return $value;
                    }
                }
                else if (!empty($value) && empty($args)) {
                    return $value;
                }
            }
            return $value;
        }

        if ($no_default) {
            return '';
        }

        // Load default values
        return $default;
    }

    /**
     * Returns the value from post, session or default
     * key: mixed
     * default: mixed // set default if post and session are empty
     * no_default: boolean // return empty if there should be any values
     */
    public function session($key, $default = '', $no_default = false)
    {
        $args = [];

        if (is_array($key)) {
            $args = $key;
            $key = array_shift($args);
        }

        // Load session values
        $value = $this->session->get($this->prefix.'||'.$key);
        if ($value) {
            if (is_array($value) && !empty($args)) {
                foreach ($args as $second) {
                    $value = isset($value[$second])?$value[$second]:'';
                }
                if (!empty($value)) {
                    return $value;
                }
            }
            else if (!empty($value) && empty($args)) {
                return $value;
            }
        }

        if ($no_default) {
            return '';
        }

        // Load default values
        return $default;
    }

    public function set($key, $value = '')
    {
        $args = [];

        if (is_array($key)) {
            $args = $key;
            $key = array_shift($args);
        }

        if (is_array($value) && !empty($args)) {
            $current = $this->session->get($this->prefix.'||'.$key);
            foreach (array_reverse($args) as $second) {
                $value[$second] = $value;
            }
            // merge current and new value;
            $value = $current + $value;
        }
        $this->session->set($this->prefix.'||'.$key, $value);
    }

    public function remove($key)
    {
        $args = [];

        if (is_array($key)) {
            $args = $key;
            $key = array_shift($args);
        }
        $this->session->remove($this->prefix.'||'.$key);
    }

    public function clear()
    {
        $items = $this->session->all();
        foreach (array_keys($items) as $key) {
            if (strpos($key, $this->prefix.'||') !== false) {
                $this->session->remove($key);
            }
        }
    }

    public function files($key = '')
    {
        if (empty($key)) {
            return $this->request->files;
        }
        return $this->request->files->get($key);
    }
}