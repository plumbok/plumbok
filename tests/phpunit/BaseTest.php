<?php
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 26.12.16
 * Time: 05:46
 */
class BaseTestListenerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('src'));
    }

    public function testEmptyFileCompile()
    {
        $compiler = new \Plumbok\Compiler();
        $file = vfsStream::url('src/empty.php');
        file_put_contents($file, '');
        $nodes = $compiler->compile($file);
        $this->assertCount(0, $nodes);
    }

    public function testUnnamespacedFileCompile()
    {
        $compiler = new \Plumbok\Compiler();
        $file = vfsStream::url('src/Amount.php');
        file_put_contents($file, <<<PHP
<?php
/**
 * @Data
 */
class Amount {
    /**
     * @var int
     */
    private \$amount;
    /**
     * @var int
     */
    private \$digits;
}
PHP
);
        $nodes = $compiler->compile($file);
        $this->assertCount(1, $nodes);
    }
}
