<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 10.12.16
 * Time: 10:19
 */
namespace Plumbok;

use Doctrine\Common\Annotations\AnnotationRegistry;
use PhpParser\PrettyPrinterAbstract;
use Plumbok\Annotation\Getter;
use Plumbok\Annotation\Setter;
use Plumbok\Generator\Getter as GetterGenerator;
use Plumbok\Generator\Setter as SetterGenerator;
use Doctrine\Common\Annotations\DocParser;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Mixed;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Parser;

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
    /** @var DocBlock */
    private $docBlockFactory;
    /** @var Serializer */
    private $docBlockSerializer;

    /**
     * Compiler constructor.
     * @param Parser $phpParser
     * @param PrettyPrinterAbstract $prettyPrinter
     */
    public function __construct(Parser $phpParser, PrettyPrinterAbstract $prettyPrinter)
    {
        $this->phpParser = $phpParser;
        $this->docParser = new DocParser();
        $this->docParser->setIgnoreNotImportedAnnotations(true);
        $this->docParser->setIgnoredAnnotationNames(['package', 'author']);
        $this->docParser->addNamespace('Plumbok\\Annotation');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Annotation' . DIRECTORY_SEPARATOR . 'Value.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Annotation' . DIRECTORY_SEPARATOR . 'Data.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Annotation' . DIRECTORY_SEPARATOR . 'Getter.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Annotation' . DIRECTORY_SEPARATOR . 'Setter.php');
        $this->docBlockFactory = DocBlockFactory::createInstance();
        $this->prettyPrinter = $prettyPrinter;
        $this->docBlockSerializer = new Serializer();
    }

    /**
     * @param string $filename
     * @return string
     */
    public function compile(string $filename) : string
    {
        $nodes = $this->phpParser->parse(file_get_contents($filename));
//        $uses = $this->findUses(...$nodes);
        $classes = $this->findClasses(...$nodes);

        foreach ($classes as $class) {
//            $annotations = $this->docParser->parse($class->getDocComment()->getText());
            $docblock = $this->docBlockFactory->create($class->getDocComment()->getText());
//            $process = false;
//            foreach ($annotations as $annotation) {
//                switch (get_class($annotation)) {
//                    case Annotation\Value::class:
//                        $process = true;
//                        break;
//                    case Annotation\Data::class:
//                        $process = true;
//                        break;
//                }
//            }
//            if (false === $process) {
//                continue;
//            }
            $generationResult = new GenerationResult();
            $classProperties = $this->findProperties(...$class->stmts);

            foreach ($classProperties as $property) {
                $propertyAnnotations = $this->docParser->parse($property->getDocComment()->getText());
                $propertyDocblock = $this->docBlockFactory->create($property->getDocComment()->getText());

                $varTags = $propertyDocblock->getTagsByName('var');
                $type = count($varTags) ? $varTags[0]->getType() : new Mixed();

                foreach ($propertyAnnotations as $annotation) {
                    foreach ($property->props as $prop) {
                        if ($annotation instanceof Getter) {
                            $generationResult->merge($this->generateGetter($prop->name, $type));
                        }
                        if ($annotation instanceof Setter) {
                            $generationResult->merge($this->generateSetter($prop->name, $type));
                        }
                    }
                }
            }

            $class->setDocComment(new Doc($this->docBlockSerializer->getDocComment(new DocBlock(
                $docblock->getSummary(),
                $docblock->getDescription(),
                array_merge($docblock->getTags(), $generationResult->getTags())
            ))));
            $class->stmts = array_merge($class->stmts, $generationResult->getStmts());
        }

        return $this->prettyPrinter->prettyPrint($nodes);
    }

    /**
     * @param Node[] ...$nodes
     * @return Node\Stmt\Property[]
     */
    private function findProperties(Node ...$nodes) : array
    {
        $properties = [];
        foreach ($nodes as $node) {
            if ($node instanceof Node\Stmt\Property) {
                $properties[] = $node;
            }
        }

        return $properties;
    }

    /**
     * @param Node[] ...$nodes
     * @return Node\Stmt\UseUse[]
     */
    private function findUses(Node ...$nodes) : array
    {
        $uses = [];
        foreach ($nodes as $node) {
            if ($node instanceof Node\Stmt\Use_) {
                if (count($node->uses)) {
                    $uses += $node->uses;
                }
            }
            if (property_exists($node, 'stmts') && count($node->stmts)) {
                $uses += $this->findUses(...$node->stmts);
            }
        }

        return $uses;
    }

    /**
     * @param Node[] ...$nodes
     * @return Node\Stmt\Class_[]
     */
    private function findClasses(Node ...$nodes) : array
    {
        $classes = [];
        foreach ($nodes as $node) {
            if ($node instanceof Node\Stmt\Class_) {
                $classes[] = $node;
            }
            if (property_exists($node, 'stmts') && count($node->stmts)) {
                $classes += $this->findClasses(...$node->stmts);
            }
        }

        return $classes;
    }

    /**
     * @param string $propertyName
     * @param Type $type
     * @return GenerationResult
     */
    private function generateGetter(string $propertyName, Type $type) : GenerationResult
    {
        $generator = new GetterGenerator($this->docBlockSerializer);
        $generator->setPropertyName($propertyName);
        $generator->setType($type);

        return $generator->generate();
    }

    /**
     * @param string $propertyName
     * @param Type $type
     * @return GenerationResult
     */
    private function generateSetter(string $propertyName, Type $type) : GenerationResult
    {
        $generator = new SetterGenerator($this->docBlockSerializer);
        $generator->setPropertyName($propertyName);
        $generator->setType($type);

        return $generator->generate();
    }
}
