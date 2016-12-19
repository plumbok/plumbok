<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 10.12.16
 * Time: 10:19
 */
namespace Plumbok;

use Doctrine\Common\Annotations\AnnotationRegistry;
use phpDocumentor\Reflection\Types\Context as TypeContext;
use PhpParser\ParserFactory;
use Plumbok\Annotation\Getter;
use Plumbok\Annotation\Setter;
use Plumbok\Compiler\Code\ClassReader;
use Plumbok\Compiler\Code\PropertyReader;
use Plumbok\Compiler\Context;
use Doctrine\Common\Annotations\DocParser;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Parser;
use Plumbok\Compiler\GeneratorFactory;
use Plumbok\Compiler\NodeFinder;
use Plumbok\Compiler\Statements;

/**
 * Class GeneratorParser
 * @package Plumbok
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class Compiler
{
    /** @var Parser */
    private $phpParser;
    /** @var DocParser */
    private $docParser;
    /** @var Serializer */
    private $docBlockSerializer;
    /** @var NodeFinder */
    private $nodeFinder;

    /**
     * Compiler constructor.
     */
    public function __construct()
    {
        $this->phpParser = (new ParserFactory)->create(ParserFactory::ONLY_PHP7);
        $this->docParser = new DocParser();
        $this->docParser->setIgnoreNotImportedAnnotations(true);
        $this->docParser->setIgnoredAnnotationNames(['package', 'author']);
        $this->docParser->addNamespace('Plumbok\\Annotation');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Annotation' . DIRECTORY_SEPARATOR . 'Value.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Annotation' . DIRECTORY_SEPARATOR . 'Data.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Annotation' . DIRECTORY_SEPARATOR . 'Getter.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Annotation' . DIRECTORY_SEPARATOR . 'Setter.php');
        $this->docBlockSerializer = new Serializer();
        $this->nodeFinder = new NodeFinder();
    }

    /**
     * @param string $filename
     * @return Node[]
     */
    public function compile(string $filename) : array
    {
        $nodes = $this->phpParser->parse(file_get_contents($filename));
        $namespaces = $this->nodeFinder->findNamespaces(...$nodes);
        $generated = false;
        foreach ($namespaces as $namespace) {
            $typeContext = new TypeContext((string)$namespace->name, $this->nodeFinder->findUses(...$namespace->stmts));
            foreach ($this->nodeFinder->findClasses(...$namespace->stmts) as $class) {
                $generated |= $this->processClass($class, $typeContext);
            }
        }
        if (!$namespaces) {
            $typeContext = new TypeContext('global', $this->nodeFinder->findUses(...$nodes));
            foreach ($this->nodeFinder->findClasses(...$nodes) as $class) {
                $generated |= $this->processClass($class, $typeContext);
            }
        }

        if (!$generated) {
            return [];
        }

        return $nodes;
    }

    /**
     * @param Node\Stmt\Class_ $class
     * @param TypeContext $typeContext
     * @return bool
     */
    private function processClass(Node\Stmt\Class_ $class, TypeContext $typeContext) : bool
    {
        $statements = new Statements();
        $classReader = new ClassReader($this->docParser, $typeContext);
        $propertyReader = new PropertyReader($this->docParser, $typeContext);
        $generatorFactory = new GeneratorFactory($typeContext, $this->docBlockSerializer);
        $properties = $propertyReader->readProperties(
            $this->nodeFinder->findProperties(...$class->stmts),
            $this->nodeFinder->findMethods(...$class->stmts)
        );
        $classContext = new Context($classReader->readAnnotations($class));
        if ($classContext->isAllArgsConstructor()) {
            $statements->merge($generatorFactory->generateAllArgsConstructor($class->name, ...$properties));
        }
        foreach ($properties as $property) {
            if ($classContext->isAllPropertyGetters()) {
                $statements->merge($generatorFactory->generateGetter($property->getName(), $property->getType()));
            }
            if ($classContext->isAllPropertySetters()) {
                $statements->merge($generatorFactory->generateSetter($property->getName(), $property->getType()));
            }
            foreach ($property->getAnnotations() as $annotation) {
                if ($annotation instanceof Getter && !$classContext->isAllPropertyGetters()) {
                    $statements->merge($generatorFactory->generateGetter($property->getName(), $property->getType()));
                }
                if ($annotation instanceof Setter && !$classContext->isAllPropertySetters()) {
                    $statements->merge($generatorFactory->generateSetter($property->getName(), $property->getType()));
                }
            }
        }
        // remove @method tags from doc comment
        $classDocBlock = $classReader->readDocBlock($class);
        $tags = $classDocBlock->getTags();
        foreach ($tags as $index => $tag) {
            if ($tag instanceof DocBlock\Tags\Method) {
                unset($tags[$index]);
            }
        }
        $class->setDocComment($this->createDocComment($classDocBlock, ...$tags));
        // append new statements
        foreach ($statements as $statement) {
            $class->stmts[] = $statement;
        }

        return count($statements) > 0;
    }

    /**
     * @param DocBlock $docBlock
     * @param DocBlock\Tag[] ...$tags
     * @return Doc
     */
    private function createDocComment(DocBlock $docBlock, DocBlock\Tag ...$tags) : Doc
    {
        $docComment = $this->docBlockSerializer->getDocComment(new DocBlock(
            $docBlock->getSummary(),
            $docBlock->getDescription(),
            $tags
        ));

        return new Doc(str_replace("/**\n * \n *\n", "/**\n", $docComment));
    }
}
