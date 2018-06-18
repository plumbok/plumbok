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
class BaseTestListenerTest extends \PHPUnit\Framework\TestCase
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

    /**
     * @dataProvider annotationsProvider
     * @param string $expected
     * @param string $testValue
     */
    public function testRemoveSpaceFromClassTags(string $expected, string $testValue)
    {
        $this->assertEquals($expected, \Plumbok\TagsUpdater::removeSpaceFromClassTags($testValue));
    }

    public function annotationsProvider(): array
    {
        return [
            [
                '/**
 * Class Account
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 * @ORM\Table(name="account")
 * @AnnotationWithParameters(sampleParameter="test")
 * @method \DateTime getDateAdd()
 * @method void setDateAdd(\DateTime $dateAdd)
 */',
                '/**
 * Class Account
 *
 * @package App\Entity
 * @ORM\Entity (repositoryClass="App\Repository\AccountRepository")
 * @ORM\Table (name="account")
 * @AnnotationWithParameters (sampleParameter="test")
 * @method \DateTime getDateAdd()
 * @method void setDateAdd(\DateTime $dateAdd)
 */'
            ],
            [
                '/**
 * Class Test
 * 
 * Sample class which do stuff
 *
 * @package App\Entity
 * @Doctrine\Orm\Entity(repositoryClass="App\Repository\AccountRepository", test="test", test2="tes2")
 * @Doctrine\Orm\Table(name="account", test="test",      test2="tes2")
 * @AnnotationWithParameters(sampleParameter="test")
 * @method    \DateTime getDateAdd()
 * @method void setDateAdd(\DateTime $dateAdd)
 */',
                '/**
 * Class Test
 * 
 * Sample class which do stuff
 *
 * @package App\Entity
 * @Doctrine\Orm\Entity (repositoryClass="App\Repository\AccountRepository", test="test", test2="tes2")
 * @Doctrine\Orm\Table   (name="account", test="test",      test2="tes2")
 * @AnnotationWithParameters   (sampleParameter="test")
 * @method    \DateTime getDateAdd()
 * @method void setDateAdd(\DateTime $dateAdd)
 */'
            ],
            [
                '/**
 * Class BalanceDto
 *
 * @SWG\Definition(
 *     required=""
 * )
 * @package App\Controller\Dto
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method string getAccountNumber()
 * @method void setAccountNumber(string $accountNumber)
 */',
                '/**
 * Class BalanceDto
 *
 * @SWG\Definition (
 *     required=""
 * )
 * @package App\Controller\Dto
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method string getAccountNumber()
 * @method void setAccountNumber(string $accountNumber)
 */'
            ]
        ];
    }
}
