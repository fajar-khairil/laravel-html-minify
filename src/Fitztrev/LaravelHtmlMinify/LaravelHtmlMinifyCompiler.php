<?php namespace Fitztrev\LaravelHtmlMinify;

use Illuminate\View\Compilers\BladeCompiler;

class LaravelHtmlMinifyCompiler extends BladeCompiler
{
    private $_config;

    public function __construct($config, $files, $cachePath)
    {
        parent::__construct($files, $cachePath);
        
        $this->_config = $config;
        // Add Minify to the list of compilers
        if ($this->_config['enabled'] === true) {
            $this->compilers[] = 'Minify';
        }

        // Set Blade contentTags and escapedContentTags
        $this->setContentTags(
            $this->_config['blade']['contentTags'][0],
            $this->_config['blade']['contentTags'][1]
        );

        $this->setEscapedContentTags(
            $this->_config['blade']['escapedContentTags'][0],
            $this->_config['blade']['escapedContentTags'][1]
        );

    }

    /**
     * Compile the view at the given path.
     *
     * @param  string  $path
     * @return void
     */
    public function compile($path = null)
    {
        if ($path)
        {
            $this->setPath($path);
        }

        if ($this->_config['enabled'] === true) {
            $contents = $this->compileMinify( $this->compileString($this->files->get($this->getPath())) );
        }else{
            $contents = $this->compileString($this->files->get($this->getPath()));
        }
        

        if ( ! is_null($this->cachePath))
        {
            $this->files->put($this->getCompiledPath($this->getPath()), $contents );
        }
    }

    /**
    * Compress the HTML output before saving it
    *
    * @param string $value the contents of the view file
    *
    * @return string
    */
    protected function compileMinify($value)
    {
        $replace = array(
            '/<!--[^\[](.*?)[^\]]-->/s' => '',
            "/<\?php/"                  => '<?php ',
            "/\n([\S])/"                => ' $1',
            "/\r/"                      => '',
            "/\n/"                      => '',
            "/\t/"                      => ' ',
            "/ +/"                      => ' ',
        );

        return preg_replace(
            array_keys($replace), array_values($replace), $value
        );
    }

}
